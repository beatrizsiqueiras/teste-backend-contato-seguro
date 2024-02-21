# Teste Back-end Contato Seguro :

Este é um projeto de API em PHP desenvolvido por Beatriz Siqueira como parte de um teste para a posição de Desenvolvedor(a) Back-end Júnior.

### Objetivo

O objetivo deste projeto é demonstrar minhas habilidades em desenvolvimento back-end, utilizando PHP para a criação de uma API simples, porém funcional, seguindo boas práticas de desenvolvimento e arquitetura de software. A API oferece endpoints para realizar operações básicas de CRUD (Create, Read, Update, Delete) em recursos específicos, bem como filtragem e ordenação em alguns deles.

## Instruções de Uso:

### Utilizando docker e docker compose (recomendado):

-   Clone o repositório em sua máquina, navegue até raiz da pasta clonada e execute o seguinte comando no terminal:

  <pre>docker compose up </pre>

-   Aguarde os contêiners serem iniciados e utilize http://localhost:8000/ para realizar as requisições.

##

### Se você não possui docker e docker compose instalados:

### Requisitos:

-   <a href="https://getcomposer.org/"> Composer </a>
-   <a href="https://www.php.net/downloads.php"> PHP 8.2</a> (**instale também as extensões: _pdo_, _pdo_sqlite_ e _sqlite3_**)
-   <a href="https://www.sqlite.org/download.html"> SQLite </a>

### Iniciando a API:

-   Navegue até a pasta clonada e execute o seguinte comando no terminal:

<pre>composer install </pre>

-   Após isso, navegue até a pasta **"public"** e execute o seguinte comando para iniciar o servidor:

<pre>php -S localhost:8000 </pre>

# O que foi feito?

Todas as demandas foram concluídas com sucesso, trazendo melhorias significativas à API.

## Refatorações Gerais:

-   Algumas **variáveis** tiveram seus nomes alterados, resultando em uma revisão da nomenclatura para melhorar a clareza e a consistência do código.

-   Foram criados **novos controllers, services e models** a fim de melhor atender ao **Princípio da Responsabilidade Única (Single Responsibility Principle)**, visando uma arquitetura mais modular e coesa. Além disso foram refatorados os arquivos já existentes.

-   O arquivo de rotas `routes.php` foi movido para uma nova pasta chamada `Route`. Além disso, as rotas de alguns recursos foram movidas para novos arquivos. Isto torna mais fácil a localização e manutenção das rotas.

-   Realizei a realocação das pastas `Controller`, `Middleware` e `Route` para uma nova pasta, chamada `Http`. Essa arquitetura de pastas visa agrupar os componentes relacionados à manipulação de requisições HTTP em um único local, melhorando a organização e estrutura do código.

## Filtros e Ordenação:

Para a parte de "Filtros e Ordenação", foi necessário refatorar alguns métodos a fim de atender às demandas do tópico.

Para realizar a filtragem/ordenação, será necessário incluir os parâmetros na rota via `GET`.

### Filtros

É necessário utilizar o formato `filter[nomeDoFiltro]=valor`, para garantir consistência e clareza na passagem dos parâmetros de filtro.

Exemplo:

<pre>/products?filter[categoryId]=4&filter[active]=true</pre>

##

### Ordenação

Para a ordenação é necessário utilizar o formato `sort=[direcao][nomeDaOrdenacao]`, sendo:

-   [direcao]:
    -   utilizar o símbolo `-` para indicar `DESC`
    -   **omitir** o símbolo para indicar `ASC`
-   [nomeDaOrdenacao]: Informar o nome da ordenação desejada (exemplo: `createdAt` irá referenciar `p.created_at` no `ProductService.php`)

Exemplo de ordenação **decrescente**:

<pre>/products?sort=-createdAt</pre>

Exemplo de ordenação **crescente**:

<pre>/products?sort=createdAt</pre>

Obs.: foi criada apenas a ordenação por data de criação do produto.

## Banco de Dados:

-   No banco de dados, foram adicionadas em todas as tabelas, as colunas `created_at`, `updated_at` e `deleted_at`, permitindo um melhor rastreamento de quando os registros foram criados, modificados ou removidos. Por exemplo, ao deletar um registro, o campo `deleted_at` será atualizado, garantindo que o dado não seja removido completamente do banco, mas possa ser desconsiderado pela nossa API.

-   Na tabela `product_category`, a coluna `cat_id` foi renomeada para `category_id`. Isso foi feito a fim de seguir um padrão de nomenclatura mais consistente e descritivo.

-   Na tabela `product_log`, foram adicionadas as colunas `before` e `after`, possibilitando o registro das versões anterior e posterior das alterações. Isso proporciona um histórico detalhado das atualizações do produto.

# Observações

### Collections

-   Os arquivos `insomnia-api.json` e `postman-api.json` foram atualizados contendo novas requisições para atender às demandas.

Exemplo: para a demanda do tópico 'Logs' foi adicionada a seguinte rota:

<pre>/productLogs?filter[productId]=4&filter[action]=update</pre>

### Arquivos do Banco de Dados

-   Os arquivos `.sqlite` foram removidos do `.gitignore` para facilitar a avaliação do projeto. É relevante destacar que reconheço que essa prática não é recomendada em projetos reais. No entanto, por ser um projeto de teste, fiz essa alteração para torná-lo mais acessível e prático de utilizar.
