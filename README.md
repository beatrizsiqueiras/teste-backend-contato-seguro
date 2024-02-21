## Instruções de uso

### Utilizando docker e docker-compose (recomendado):

-   Clone o repositório em sua máquina, navegue até a pasta clonada e execute o seguinte comando no terminal:

  <pre>docker-compose up </pre>

-   Aguarde os contâiners serem iniciados e utilize http://localhost:8000/ para realizar as requisições.

### Se você não possui docker e docker-compose instalados:

-   Instale as ferramentas:
    <a href="https://getcomposer.org/"> Composer </a>,
    <a href="https://www.php.net/downloads.php"> PHP 8.2 </a> e
    <a href="https://www.sqlite.org/download.html"> SQLite </a>

-   Navegue até a pasta clonada e execute os seguintes comandos no terminal:
-   <pre>composer install </pre>
-   <pre>php -S localhost:8000 </pre>

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

### Realocação de pastas

-   Realizei a realocação das pastas Controller, Middleware e Route para a pasta Http.
    A organização dessas pastas na pasta Http segue uma lógica de agrupar todos os componentes relacionados à manipulação de requisições HTTP em um único local. Essa mudança foi feita para melhorar a organização e estrutura do código.

## Observações

### Arquivos .sqlite

-   Os arquivos .sqlite foram removidos do .gitignore para facilitar a avaliação do projeto. É relevante destacar que reconheço que essa prática não é recomendada em projetos reais. No entanto, por ser um projeto de teste, fiz essa alteração para torná-lo mais acessível e prático de utilizar.
