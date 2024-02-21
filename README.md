# DESAFIO BACKEND

## Configuração do Ambiente

### Requisitos

-   _PHP >= 8.0_ e [extensões](https://www.php.net/manual/pt_BR/extensions.php) (**não esquecer de instalar as seguintes extensões: _pdo_, _pdo_sqlite_ e _sqlite3_**);
-   _SQLite_;
-   _Composer_.

### Instalação

-   Instalar dependências pelo composer com `composer install` na raiz do projeto;
-   Servir a pasta _public_ do projeto através de algum servidor.
    (_Sugestão [PHP Built in Server](https://www.php.net/manual/en/features.commandline.webserver.)_)

## Sobre o Projeto

-   O cliente XPTO Ltda. contratou seu serviço para realizar alguns ajustes em seu sistema de cadastro de produtos;
-   O sistema permite o cadastro, edição e remoção de _produtos_ e _categorias de produtos_ para uma _empresa_;
-   Para que sejam possíveis os cadastros, alterações e remoções é necessário um usuário administrador;
-   O sistema possui categorias padrão que pertencem a todas as empresas, bem como categorias personalizadas dedicadas a uma dada empresa. As categorias padrão são: (`clothing`, `phone`, `computer` e `house`) e **devem** aparecer para todas as _empresas_;
-   O sistema tem um relatório de dados dedicado ao cliente.

## Sobre a API

As rotas estão divididas em:

-   _CRUD_ de _categorias_;
-   _CRUD_ de _produtos_;
-   Rota de busca de um _relatório_ que retorna um _html_.

**Atenção**, é bem importante que se adicione o _header_ `admin_user_id` com o id do usuário desejado ao acessar as rotas para simular o uso de um usuário no sistema.

A documentação da API se encontra na pasta `docs/api-docs.pdf`

-   A documentação assume que a url base é `localhost:8000` mas você pode usar qualquer outra url ao configurar o servidor;
-   O _header_ `admin_user_id` na documentação está indicado com valor `1` mas pode ser usado o id de qualquer outro usuário caso deseje (_pesquisando no banco de dados é possível ver os outros id's de usuários_).

Caso opte por usar o [Insomnia](https://insomnia.rest/) o arquivo para importação se encontra em `docs/insomnia-api.json`.
Caso opte por usar o [Postman](https://www.postman.com/) o arquivo para importação se encontra em `docs/postman-api.json`.

## Sobre o Banco de Dados

-   O banco de dados é um _sqlite_ simples e já vem com dados preenchidos por padrão no projeto;
-   O banco tem um arquivo de backup em `db/db-backup.sqlite` com o estado inicial do projeto caso precise ser "resetado".

## Demandas

Abaixo, as solicitações do cliente:

### Categorias

-   [x] A categoria está vindo errada na listagem de produtos para alguns casos
        (_exemplo: produto `blue trouser` está vindo na categoria `phone` e deveria ser `clothing`_);
-   [x] Alguns produtos estão vindo com a categoria `null` ao serem pesquisados individualmente (_exemplo: produto `iphone 8`_);
-   [x] Cadastrei o produto `king size bed` em mais de uma categoria, mas ele aparece **apenas** na categoria `furniture` na busca individual do produto.

### Filtros e Ordenamento

Para a listagem de produtos:

-   [x] Gostaria de poder filtrar os produtos ativos e inativos;
-   [x] Gostaria de poder filtrar os produtos por categoria;
-   [x] Gostaria de poder ordenar os produtos por data de cadastro.

### Relatório

-   [x] O relatório não está mostrando a coluna de logs corretamente, se possível, gostaria de trazer no seguinte formato:
        (Nome do usuário, Tipo de alteração e Data),
        (Nome do usuário, Tipo de alteração e Data),
        (Nome do usuário, Tipo de alteração e Data)
        Exemplo:
        (John Doe, Criação, 01/12/2023 12:50:30),
        (Jane Doe, Atualização, 11/12/2023 13:51:40),
        (Joe Doe, Remoção, 21/12/2023 14:52:50)

### Logs

-   [x] Gostaria de saber qual usuário mudou o preço do produto `iphone 8` por último.

### Extra

-   [x] Aqui fica um desafio extra **opcional**: _criar um ambiente com_ Docker _para a api_.

**Seu trabalho é atender às 7 demandas solicitadas pelo cliente.**

Caso julgue necessário, podem ser adicionadas ou modificadas as rotas da api. Caso altere, por favor, explique o porquê e indique as alterações nesse `README`.

Sinta-se a vontade para refatorar o que achar pertinente, considerando questões como arquitetura, padrões de código, padrões restful, _segurança_ e quaisquer outras boas práticas. Levaremos em conta essas mudanças.

Boa sorte! :)

## Suas Respostas, Duvidas e Observações

## Instruções de uso

### Utilizando docker e docker-compose (recomendado):

-   Clone o repositório em sua máquina, navegue até a pasta clonada e execute o seguinte comando no terminal:

  <pre>docker-compose up </pre>

-   Aguarde os contâiners serem iniciados e acesse http://localhost:8000/ no navegador.

### Se você não possui docker e docker-compose instalados:

-   Instale as ferramentas:
    <a href="https://getcomposer.org/"> Composer </a>,
    <a href="https://www.php.net/downloads.php"> PHP 8.2 </a> e
    <a href="https://www.sqlite.org/download.html"> SQLite </a>

-   Navegue até a pasta clonada e execute os seguintes comandos no terminal:
-   <pre>composer update </pre>
-   <pre>php -S localhost:8080 </pre>

## O que foi realizado:

### Todas as dependências do tópico "Demandas" foram concluidas.

### Relocalização de arquivos

-   O arquivo de rotas foi movido para a pasta 'Route', implicando uma reorganização da estrutura de diretórios para melhorar a organização e a legibilidade do código

### Renomeação de Variáveis

-   Algumas variáveis tiveram seus nomes alterados, resultando em uma revisão da nomenclatura para melhorar a clareza e a consistência do código

### Refatoração com SRP: Novos Controllers, Services e Models

-   Foram criados novos controllers, services e models com foco no Princípio da Responsabilidade Única (Single Responsibility Principle), visando uma arquitetura mais modular e coesa.

### Filtros:

-   Um novo arquivo **new-insomnia-api.json** foi adicionado contendo novas requisições para atender as demandas.

-   Exemplo: para 'Logs' foi adicionada a rota:

    -   http://localhost:8000/productLogs?filter[productId]=4&filter[action]=update

-   Para "Filtros e Ordenamento", será necessário incluir as QueryParams na rota GET :

    -   http://localhost:8000/products?filter[categoryId]=4&filter[active]=true&filter[createdAt]=20/12/2023

-   **É necessário utilizar o formato filter[filterName] nas QueryParams**
    -   As query params de filtro devem seguir o padrão 'filter[filterName]', garantindo consistência e clareza na passagem de parâmetros de filtragem

### Banco de dados

-   No banco de dados, foram adicionadas às tabelas as colunas 'created_at', 'deleted_at' e 'updated_at', permitindo o rastreamento de quando os registros foram criados, modificados ou removidos. Portanto, ao deletar um registro, o campo "deleted_at" será atualizado, garantindo que o dado não seja removido diretamente.

-   Na tabela 'product_category', a coluna 'cat_id' foi modificada para 'category_id', seguindo o padrão de nomenclatura mais consistente e descritivo.

-   Na tabela 'product_log', foram adicionadas as colunas 'before' e 'after', possibilitando o registro das versões anterior e posterior dos dados alterados de um produto, proporcionando um histórico detalhado de alterações.
