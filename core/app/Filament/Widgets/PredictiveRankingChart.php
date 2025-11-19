<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
// Cache: Usado para "memorizar" o resultado da predição (que é lenta) por 1 hora.
use Illuminate\Support\Facades\Cache;
// ClassSchedule: O Model "Fato" que contém os dados de todas as aulas.
use App\Models\ClassSchedule;
// Process: A classe do Symfony usada para executar processos externos (o nosso script Python).
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Support\Facades\Http; // (Importação não utilizada neste código, mas pode ser útil)

class PredictiveRankingChart extends ChartWidget
{
    /**
     * Título principal do widget.
     */
    protected static ?string $heading = 'Previsão de Procura: Salas (Top 5 / Bottom 5)';

    /**
     * Uma propriedade (não-estática) para guardar mensagens de erro.
     * Usamos isto para mostrar o erro no título do gráfico se a predição falhar.
     */
    protected ?string $errorTitle = null;

    /**
     * Ordem de ordenação no dashboard.
     */
    protected static ?int $sort = 4;

    /**
     * Aponta para uma view Blade personalizada (em /resources/views/)
     * para controlar a altura e o estilo do cartão deste widget.
     */
    protected static string $views = 'filament.widgets.size_style_graphics';

    /**
     * Controla o layout (largura) do widget na grelha (grid) do dashboard.
     * Este código está comentado, por isso o widget usará a configuração padrão
     * (provavelmente 12 colunas, ou o que estiver definido no Dashboard.php).
     */
    // public function getColumnSpan(): int | string | array
    // {
    //     return [
    //     'sm' => 12,
    //     'md' => 6, // ocupa 6 das 12 colunas
    //     'lg' => 6, // metade da tela
    //     ];
    // }
    
    /**
     * Sobrescreve o método getHeading() padrão.
     * Se a nossa propriedade $errorTitle foi definida (no catch),
     * este método mostra o erro como o título do gráfico.
     */
    public function getHeading(): string
    {
        if ($this->errorTitle) {
            return $this->errorTitle;
        }
        return self::$heading;
    }

    /**
     * Esta é a função principal que o Filament chama para obter os dados do gráfico.
     * É aqui que toda a "magia" (Cache, PHP e Python) acontece.
     */
    protected function getData(): array
    {
        /**
         * PASSO 1: OTIMIZAÇÃO DE TIMEOUT (PHP)
         * O treino de ML é lento. O PHP, por defeito, "morre" aos 30 segundos.
         * Esta linha dá ao *script PHP do gráfico* 180 segundos (3 minutos)
         * para que ele possa esperar pacientemente pela resposta do Python.
         */
        @set_time_limit(180);
        
        // Define uma chave única para o nosso cache de predição.
        $cacheKey = 'prediction_ranking_chart_data';
        // Define por quanto tempo o cache será válido (1 hora).
        $cacheDuration = 60 * 60; // 1 hora em segundos

        /**
         * PASSO 2: OTIMIZAÇÃO DE CACHE
         * Esta é a otimização de performance mais importante.
         * O Laravel tenta buscar os dados pela chave ('prediction_ranking_chart_data').
         * Se encontrar (e não tiver expirado), ele retorna os dados instantaneamente.
         * Se NÃO encontrar (ex: 1ª vez do dia), ele executa o código lento
         * dentro da função (function () { ... }) e salva o resultado.
         */
        $predictionData = Cache::remember($cacheKey, $cacheDuration, function () {
            
            try {
                /**
                 * PASSO 2a: COLETA DE DADOS (Laravel)
                 * Recolhe todos os "Fatos" (aulas) e "Dimensões" (recursos)
                 * para enviar ao Python como "dados de treino".
                 */
                $aulas = ClassSchedule::query()
                    // ->with() é o "Eager Loading". Ele "cola" os dados relacionados
                    // de outras tabelas numa única consulta eficiente.
                    ->with([
                        'schedule', 'schedule.course', 'sala', 'sala.equipments', 'dia', 'lessonTime'
                    ])
                    // ->select() define o "contrato" de dados com o Python.
                    // Renomeamos colunas (ex: 'room as room_id') para criar um JSON
                    // limpo e fácil para o Python entender.
                    ->select(
                        'id as aula_id', 'schedule_id', 'created_at',
                        'room', 'room as room_id', // 'room' é para o ->with('sala')
                        'day', 'day as day_id',     // 'day' é para o ->with('dia')
                        'time', 'time as lesson_time_id' // 'time' é para o ->with('lessonTime')
                    )
                    ->get();
                
                // Se o banco de dados estiver vazio, não podemos treinar.
                if ($aulas->isEmpty()) {
                    return ['error' => 'Nenhuma aula (class_schedule) encontrada.'];
                }
                
                // Converte a coleção inteira de dados do Laravel num JSON gigante.
                $jsonData = $aulas->toJson();

                /**
                 * PASSO 2b: EXECUÇÃO DO SCRIPT PYTHON
                 * Aqui é a "ponte" entre PHP e Python.
                 */
                $scriptPath = base_path('storage/app/python/predict_demand.py');
                $env = ['PYTHONIOENCODING' => 'UTF-8', 'LANG' => 'en_US.UTF-8'];

                // Tenta usar 'py' (o launcher padrão do Windows)
                $process = new Process(['py', $scriptPath], base_path(), $env);
                
                // --- CORREÇÃO DO TIMEOUT (PYTHON) ---
                // O Processo Python também tem um limite (60s por defeito).
                // Igualamos o limite dele ao do PHP (180s).
                $process->setTimeout(180); 
                
                try {
                    $process->setInput($jsonData); // Envia o JSON gigante para o Python
                    $process->mustRun(); // Executa e espera (a parte lenta)
                } catch (ProcessFailedException $e) {
                    // Se 'py' falhar (ex: Linux/Mac), tenta 'python'
                    $process = new Process(['python', $scriptPath], base_path(), $env);
                    $process->setTimeout(180);
                    $process->setInput($jsonData);
                    $process->mustRun();
                }

                // Captura a "saída" (o que o Python "printou")
                $output = $process->getOutput();
                
                if (empty($output)) {
                    return ['error' => 'O script Python executou mas não retornou saída.'];
                }

                // Converte a resposta JSON do Python de volta para um array PHP
                $result = json_decode($output, true); // true = array
                
                if (json_last_error() !== JSON_ERROR_NONE) {
                    // Acontece se o Python der erro (ex: NaN) e o JSON for inválido
                    return ['error' => 'Python retornou JSON inválido.', 'raw_output' => $output];
                }

                return $result; // <--- ESTE É O RESULTADO QUE SERÁ SALVO NO CACHE

            } catch (ProcessFailedException $e) {
                // Erro se o Python falhar (ex: "py" não encontrado, erro no script)
                return ['error' => 'Script Python não encontrado ou falhou.', 'stderr' => $e->getMessage()];
            } catch (\Exception $e) {
                // Erro geral do PHP (ex: falha na coleta de dados)
                return ['error' => 'Erro PHP: ' . $e->getMessage()];
            }
        }); // Fim do Cache::remember

        /**
         * PASSO 3: PROCESSAMENTO DO RESULTADO (Ranking Top 5 / Bottom 5)
         * Aqui, já temos os dados (do Cache ou do Python) e vamos formatá-los.
         */
         
        // Se a predição (ou o cache) retornou um erro, mostre-o
        if (isset($predictionData['error'])) {
            $this->errorTitle = $predictionData['error']; 
            return ['datasets' => [], 'labels' => []];
        }
        
        if (!is_array($predictionData) || empty($predictionData)) {
             $this->errorTitle = 'Predição retornou dados vazios ou inválidos.';
             return ['datasets' => [], 'labels' => []];
        }

        // Converte o array de predição numa Coleção Laravel
        $dataCollection = collect($predictionData);

        // Ordena pela pontuação (do maior para o menor)
        $sorted = $dataCollection->sortByDesc('predicted_score');
        
        // Pega os 5 primeiros (Top 5)
        $top5 = $sorted->take(5);
        // Pega os 5 últimos e reordena-os (do menor para o maior)
        $bottom5 = $sorted->take(-5)->sortBy('predicted_score');
        
        // Junta as duas listas (ex: 5 + 5 = 10 barras)
        // .unique() garante que, se houver menos de 10 salas, não haja duplicatas
        $finalData = $top5->merge($bottom5)->unique('id_da_sala');

        /**
         * PASSO 4: PREPARAÇÃO DOS DADOS PARA O CHART.JS
         */
        $datasets = [
            [
                'label' => 'Pontuação de Demanda Prevista',
                'data' => $finalData->pluck('predicted_score')->all(),
                // Lógica de cores: aplica verde para o Top 5, vermelho para o Bottom 5
                'backgroundColor' => $finalData->map(function ($item, $key) use ($top5) {
                    return $top5->contains('id_da_sala', $item['id_da_sala'])
                        ? 'rgba(75, 192, 192, 0.7)'  // Verde-água (Top)
                        : 'rgba(255, 99, 132, 0.7)'; // Vermelho (Bottom)
                })->all(),
            ]
        ];
        
        $labels = $finalData->pluck('sala_name')->all();

        return [
            'datasets' => $datasets,
            'labels' => $labels
        ];
    }

    /**
     * Define o tipo de gráfico (barra)
     */
    protected function getType(): string
    {
        return 'bar';
    }

    /**
     * Define as opções visuais do Chart.js
     */
    protected function getOptions(): array
    {
        return [
            // 'indexAxis' => 'y' é o que torna o gráfico de barras "deitado"
            'indexAxis' => 'y', 
            'scales' => [
                'x' => [
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Pontuação de Demanda Prevista'
                    ]
                ],
                'y' => [
                    // Impede que o Chart.js pule rótulos (nomes das salas)
                    'ticks' => ['autoskip' => false] 
                ]
            ],
            'plugins' => [
                'legend' => ['display' => false] // Esconde a legenda
            ]
        ];
    }
}