# SisRural - Laravel

## Configuração Inicial

1. Clonar o repositório

2. Renomear o arquivo `.env.example` para `.env`

3. Configurar as credenciais do banco de dados no arquivo `.env`

-   DB_HOST=localhost
-   DB_PORT=3306
-   DB_DATABASE=nome_da_base_de_dados
-   DB_USERNAME=root
-   DB_PASSWORD=

Utilizar a mesma base de dados para o "DB_LOG".

-   DB_LOG_HOST=localhost
-   DB_LOG_PORT=3306
-   DB_LOG_DATABASE=nome_da_base_de_dados
-   DB_LOG_USERNAME=root
-   DB_LOG_PASSWORD=

4. Instalar o 'composer', https://getcomposer.org/download/. (Versão 1.10.x)

5. Instalar o 'node', https://nodejs.org/en/download/. (Versão 12.14.0)

6. Executar os seguintes comandos:

```
composer install

php artisan key:generate
```

7. Próximos comandos fazem parte do banco de dados.

Pular esse step caso a base seja copiada (dump) do servidor de homologação.

O comando 'php artisan migrate' vai criar a estrutura base do banco.

O comando 'php artisan db:seed' vai importar os dados basicos do projeto + os dados presentes nos XLS`s de importação (resources/xlsx/\*.xlsx). Este comando demora para ser executado.

```
php artisan migrate

php artisan db:seed
```

PS: Caso algum seeder não rode (estouro de memória), é possível rodar manualmente. Ex:

```
php artisan db:seed --class=SyncMvp4Seeder
```

8.

```
php artisan storage:link

php artisan passport:install

php artisan passport:client --personal

npm install

npm run production

composer clear-all

php artisan serve --port=8000
```

## Login

Credenciais para acesso:

Ver arquivo `UserTableSeeder.php` p/ acesso do Administrador.

P/ acessos como Domínio/Unidade Operacional/Técnico, ver o arquivo `carga_usuarios.xlsx`

# Pacotes utilizados no projeto

-   https://laravel.com/docs/8.x

Laravel. Versão 8

-   https://laravel-boilerplate.com/7.0/start.html

A estrutura do projeto foi desenvolvida utilizando o laravel-boilerplate. Versão 7.0 do 'laravel-boilerplate'

-   https://coreui.io/

O laravel-boilerplate utiliza o coreui como "Admin Template". Versão 3.0.0

-   https://iconify.design/icon-sets/cil/

Ícones utilizados

# Estrutura Geral

-   Controller
-   Model
-   Repository
-   Request (Quando necessário)
-   Policy (E adicionar no AuthServiceProvider.php o Model->Policy)
-   resources/backend/nome_tabela/ (arquivos .blade)
-   Adicionar a rota no routes/backend/admin.php
-   Adicionar a sessão no views/backend/includes/sidebar.blade.php

## Criando tabelas

```
php artisan make:migration create_minha_tabela_table

php artisan migrate
```

### Tabelas que são manipuladas pelo APP

No APP (React Native)

a. Coluna "id" deve ser Varchar(255) com index()
b. Coluna "uid" Bigint(20) com auto increment
c. Coluna "app_sync" boolean

No CMS (Laravel)

a) adicionar boot:creating p/ setar o ID = Uuid::generate(4)
b) adicionar a função getRouteKeyName() passando "uid"
c) alterar os controlers para passar o UID na URL (edit, ...) e não o ID

### Gerando Polignos para testar na abrangêcia do mapa (Região)

a) http://www.birdtheme.org/useful/v3tool.html
b) No topo, logo ao lado do título "Google Maps API v3 Tool", selecionar "POLYGON" e "KML"
c) Copiar o texto do box e criar um arquivo XXXXX.kml
d) Subir este arquivo nos locais que aceitam kml (Ex: Regiões)

Se ao subir o KML der erro:

```
ErrorException
Undefined offset: 0
```

Deve ser atualizado a versão do MySQL (Versão usada em Homologação/Produção - 5.7.32-log)

# Passport

É utilizado para a parte de autenticação do APP.

Foi adicionado nas variáveis de ambiente p/ os valores não serem alterados ao publicar o servidor em hml e prod.

PASSPORT_PRIVATE_KEY=
PASSPORT_PUBLIC_KEY=

# Explicar o mysql_logs

Utilizado para o BI. Por hora não esta sendo utilizado, por isso leva a mesma configuração do banco de dados.

# Filesystem

Para testes locais utilizar a configuração

```
FILESYSTEM_DRIVER=public
```

P/ o servidor de homologação é utilizado o driver do Azure

```
FILESYSTEM_DRIVER=azure
AZURE_STORAGE_NAME=
AZURE_STORAGE_KEY=
AZURE_STORAGE_CONTAINER=
```

P/ servidores AWS é possível utilizar o S3

```
FILESYSTEM_DRIVER=s3
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=
AWS_BUCKET=
AWS_URL=
```

# Erros

### Ao rodar algum comando do artisan da erro de não encontrar o arquivo

1. composer clear-all

2. Se o comando anterior não funcionar, remover o arquivo boostrap/cache/config.php e depois executar novamente o comando
