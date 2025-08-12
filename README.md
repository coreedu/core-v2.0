# Core V2.0

## Pré-requisitos

Antes de começar, certifique-se de que você tem o seguinte instalado:

- **PHP** versão 8.0 ou superior
- **Laravel** versão 12.13.0 ou superior
- **Composer**
- **Filamentphp** versão 3.0

## Passos para Configuração e Execução

### 1. **Clone o Repositório**

   Primeiro, clone o repositório do projeto para sua máquina local:

   ```bash
   git clone https://github.com/coreedu/core-v2.0.git
   ```

### 2. **Instale as Dependências**

   Utilize o **Composer** para instalar as dependências do Laravel (Certifique-se estar dentro da pasta core):

   ```bash
   composer install
   ```

### 3. **Copie o Arquivo de Ambiente**

   O arquivo de ambiente `.env.example` contém as variáveis de configuração padrão para o projeto. Copie esse arquivo para criar seu próprio `.env`:

   ```bash
   cp .env.example .env
   ```

### 4. **Gere a Chave da Aplicação**

   O Laravel exige uma chave única para o funcionamento do sistema de criptografia. Gere a chave utilizando o seguinte comando:

   ```bash
   php artisan key:generate
   ```

### 5. **Execute as Migrations e Seeders**

   Agora, execute as migrations para criar as tabelas no banco de dados e os seeders para popular o banco com dados iniciais:

   ```bash
   php artisan migrate --seed
   ```

### 7. **Inicie o Servidor Local**

   O servidor será iniciado no endereço `http://127.0.0.1:8000`. Use o comando abaixo para iniciar o servidor:

   ```bash
   php artisan serve
   ```
