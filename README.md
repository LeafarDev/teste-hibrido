# Híbrido Teste
### Tecnologias Utilizadas
* <b>Linguagem =</b> PHP 7.4
* <b>Banco de dados =</b> Mysql:8.0 
* <b>Container =</b> Docker & Docker Compose
* <b>php-di/php-di</b> = Biblioteca para injeção de dependências
* <b>miladrahimi/phprouter =</b> Permite a criação de rotas http
* <b>twig/twig =</b> Biblioteca de template HTML
* <b>composer/composer =</b> Gerenciador de dependências do PHP
* <b>doctrine/migrations = </b> Possibilita a criação scripts de geração de tabelas de banco de dados
* <b>symfony/yaml =</b> Permite a conversão yaml para php e também o inverso
* <b>doctrine/orm = </b> ORM que permite a representação das tabelas do banco de dados no formato de objeto
* <b>illuminate/validation =</b> Biblioteca de validação de dados
* <b>amonolog/monolog =</b> Permite gerar os logs da aplicação
#
### Arquitetura e Funcionamento Geral
#### A arquitetura é uma variação de MVC com as seguintes características:
* A requisição "passa" primeiro pelo arquivo `/public/index.php` onde:
    * Inicia sessão
    * Inclui o autoload
    * Configura o PHP-DI para injeção de dependências
    * Por fim chama ```TesteHibridoApp\Http\Router\RouteManager```
* O ```TesteHibridoApp\Http\Router\RouteManager```:
    * Gera as rotas da aplicação
    * Após receber uma requisição com uma rota válida invoca o ```TesteHibridoApp\Controller\ClientController``` e seu método correspondente:
        ```php
              $router->get('/clients', function () use ($clientController) {
                  return $clientController->index();
              });
        ```
* O ```TesteHibridoApp\Controller\ClientController``` tem as seguintes atribuições:
    * Validar a entrada de dados do usuário
    * Invocar a camada de exibição de dados em ```/resources/views```
    * Invocar as implementações de regra de negócio em ```TesteHibridoApp\Service\ClientService```
* O ```TesteHibridoApp\Service\ClientService```:
    * Possui as implementações como "create", "update", "find", etc.
    * Gera o log de ações como o "create" e também de erros
    * Para fazer a persistência de dados, ele utiliza ```Doctrine\ORM\EntityManager``` que representa os dados do banco através da classe ```TesteHibridoApp\Model\Client```
* Em ```/resources/views``` há os arquivos responsáveis por gerar o HTML da aplicação
* O arquivo de log fica em ```/logs``` 
* O ```Doctrine\ORM\EntityManager``` e a bilioteca ```doctrine/migrations ``` dependem do arquivo de configuração ```migrations-db.php```
#
### Dependencias:
#### Tenha instalado o [Docker](https://docs.docker.com/engine/install/) e o [Docker Compose](https://docs.docker.com/compose/install/)
#
### Instalação:
```docker-compose up --build -d```
#### Após o build do comando anterior estiver finalizado e tudo estiver executando corretamente, execute as instruções abaixo:
##### *Se estiver executando o Docker no Windows, utilize o PowerShell
* Crie o arquivo ```migrations-db.php``` a partir do ```migrations-db-example.php``` e preencha as informações do banco de dados:
    * dbname = hibrido,
    * user = root
    * password = 123456
    * host = teste-hibrido-mysql
    * port = 3306
    * driver = pdo_mysql

* instalação de dependencias(se estiver no Linux): ```docker run -it --rm -v `pwd`:/app composer install```

* instalação de dependencias(se estiver no Windows): ```docker run -it --rm -v ${pwd}:/app composer install```

* Permissão pasta log: ```docker-compose run --rm --no-deps php-fpm chmod -R 777 logs```

* gerar as tabelas do banco de dados: ```docker-compose exec php-fpm ./vendor/bin/doctrine-migrations migrate --no-interaction```
#
### Utilização
* Basta acessar a aplicação em `http://localhost:3000/`
