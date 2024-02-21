# Instruções de uso:

### Utilizando docker e docker compose (recomendado):

-   Clone o repositório em sua máquina, navegue até raiz a pasta clonada e execute o seguinte comando no terminal:

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

Todas as tarefas do tópico "Demandas" foram concluidas.

Para melhorar o projeto!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

-   O arquivo **insomnia-api.json** foi atualizado contendo novas requisições para atender às demandas.
    -   Exemplo: para a demanda do tópico 'Logs' foi adicionada a rota:
        <pre>/productLogs?filter[productId]=4&filter[action]=update</pre>

### Refatorações Gerais:

-   Algumas **variáveis** tiveram seus nomes alterados, resultando em uma revisão da nomenclatura para melhorar a clareza e a consistência do código.

-   Foram criados **novos controllers, services e models** a fim de melhor atender ao **Princípio da Responsabilidade Única (Single Responsibility Principle)**, visando uma arquitetura mais modular e coesa. Além disso foram refatorados os arquivos já existentes.

-   Realizei a realocação das pastas `Controller`, `Middleware` e `Route` para uma nova pasta, chamada `Http`. Essa arquitetura de pastas visa agrupar os componentes relacionados à manipulação de requisições HTTP em um único local, melhorando a organização e estrutura do código.

-   O arquivo de rotas `routes.php` foi movido para uma nova pasta chamada `Route`, !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!.

### Filtros e Ordenação:

Para a parte de "Filtros e Ordenação", foi necessário refatorar alguns métodos a fim de atender às demandas do tópico.

Para realizar a filtragem/ordenação, será necessário incluir os parâmetros na rota via GET, por exemplo:
    <pre>/products?filter[categoryId]=4&filter[active]=true&filter[createdAt]=20/12/2023</pre>

É necessário utilizar o formato **filter[nomeDoFiltro]=valor**, para garantir consistência e clareza na passagem dos parâmetros.

### Banco de Dados:

-   No banco de dados, foram adicionadas em todas as tabelas, as colunas `created_at`, `updated_at` e `deleted_at`, permitindo um melhor rastreamento de quando os registros foram criados, modificados ou removidos. Por exemplo, ao deletar um registro, o campo `deleted_at` será atualizado, garantindo que o dado não seja removido completamente do banco, mas possa ser desconsiderado pela nossa API.

-   Na tabela `product_category`, a coluna `cat_id` foi renomeada para `category_id`. Isso foi feito a fim de seguir um padrão de nomenclatura mais consistente e descritivo.

-   Na tabela `product_log`, foram adicionadas as colunas `before` e `after`, possibilitando o registro das versões anterior e posterior das alterações. Isso proporciona um histórico detalhado das atualizações do produto.

# Observações

### Arquivos do Banco de Dados

-   Os arquivos `.sqlite` foram removidos do `.gitignore` para facilitar a avaliação do projeto. É relevante destacar que reconheço que essa prática não é recomendada em projetos reais. No entanto, por ser um projeto de teste, fiz essa alteração para torná-lo mais acessível e prático de utilizar.
