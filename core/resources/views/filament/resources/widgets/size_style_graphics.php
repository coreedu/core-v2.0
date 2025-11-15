<div class="flex flex-col items-center w-full space-y-6">
    <!--
        Container principal estruturado em coluna.
        Centraliza todo o conteúdo horizontalmente e adiciona espaçamento vertical entre os blocos.
    -->

    <div class="flex justify-center gap-6 w-10/12">
        <!--
            Linha superior contendo dois gráficos lado a lado.
            Ambos são centralizados e possuem um espaçamento fixo entre si (gap-6).
            A largura total é limitada a 10/12 para manter alinhamento visual mais elegante.
        -->

        <x-filament::card class="w-5/12 h-[250px] flex items-center justify-center">
            <!--
                Primeiro card configurado para ocupar metade da linha (5/12).
                Altura fixa de 250px e centralização total do gráfico dentro do card.
            -->
            {{ $this->chart1 }}
        </x-filament::card>

        <x-filament::card class="w-5/12 h-[250px] flex items-center justify-center">
            <!--
                Segundo card com as mesmas dimensões e alinhamento, garantindo simetria visual.
            -->
            {{ $this->chart2 }}
        </x-filament::card>
    </div>

    <div class="w-10/12">
        <!--
            Gráfico inferior ocupando toda a linha, mantendo a mesma largura dos cards superiores (10/12),
            reforçando consistência de layout.
        -->

        <x-filament::card class="h-[250px] flex items-center justify-center">
            <!--
                Card com altura fixa e centralização para o terceiro gráfico.
                Ideal para visualizações que precisam ocupar mais espaço horizontal.
            -->
            {{ $this->chart3 }}
        </x-filament::card>
    </div>
</div>
