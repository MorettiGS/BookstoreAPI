# Bookstore API

Seguem instruções de uso, instalação e utilização de um sistema em PHP 8.1, MySQL 8, Yii2 e Composer 2 que representa uma API de clientes e livros.

INSTALAÇÃO
------------

### Instalação por Composer

Se você não possui [Composer](http://getcomposer.org/), você pode instalar seguindo as instruções contidas em [getcomposer.org](http://getcomposer.org/doc/00-intro.md#installation-nix).

Seguindo, você pode instalar a aplicação por meio dos seguintes comandos:

~~~
git clone https://github.com/MorettiGS/BookstoreAPI.git
composer self-update
cd src
composer install
~~~

INICIANDO
---------------

Após instalar a aplicação, você deverá conduzir os seguintes passos para inicializar a aplicação instalada.

1. Criar uma base de dados em seu ambiente MySQL 8, com o nome à sua escolha.
2. Criar um arquivo ```.env``` na raiz do repositório copiado, com o mesmo formato existente no arquivo ```.env.example```:

~~~
JWT_SECRET_KEY=""
DB_DSN=""
DB_USERNAME=""
DB_PASSWORD=""
~~~

Siga os comentários do arquivo ```.env.example``` para preenchê-lo corretamente.

3. Após configurado, aplique as migrações ao banco de dados por meio do comando `php yii migrate`, ou apenas `yii migrate`. Isso criará as tabelas utilizadas na aplicação.

Para realizar o cadastro de um usuário na aplicação, utilize o comando ```php yii user/create $login $senha $nome```, substituindo $login, $senha e $nome por seus valores respectivos.

Após possuir um usuário, você poderá seguir para suas requisições de API:

~~~
/user/login -> Recebe como body um objeto com valores de "login" e "senha" para login no sistema, e retorna um token de autenticação que poderá ser usado para as outras chamadas de API. Lembre-se que toda requisição precisará de um Header com o token de autenticação.

/client/create -> Criação de cliente, com os parâmetro seguintes. Exemplo:

- {
    "name": "",
    "cpf": "",
    "cep": "",
    "street": "",
    "number": "",
    "city": "",
    "state": "",
    "gender": "",
    "complement": ""
}

/client/list -> listagem de clientes, com parâmetros por URL seguintes. Exemplo:

- /client/list?limit=10&offset=10&orderBy=name&filter=name&term=teste

/book/create -> Criação de livro, com os parâmetros seguintes. Exemplo:

- {
    "isbn": "",
    "title": "",
    "author": "",
    "price": 0,
    "stock": 0
}

/book/list -> listagem de livros, com parâmetros por URL seguintes. Exemplo:

- /book/list?limit=10&offset=10&orderBy=title&filter=title&term=teste

~~~