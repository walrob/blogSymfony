# Blog con Symfony

Este git contiene mi primer desarrollo con Symfony, un Blog.

Siga los siguientes pasos para poder configurar el entorno de desarrollo. Utilicé Symfony 4.2 y MySql 8.0

1 - Instalar composer - https://getcomposer.org/

2 - Intalar xampp - https://www.apachefriends.org/es/index.html

3 - Intalar MySql - https://www.mysql.com/downloads/

4 - En una terminal:

    Ingresar a la carpeta xampp/htdocs

    Crear el proyecto:
    $ composer create-project symfony/website-skeleton desafioBlog

5 - Clonar el Proyecto en GitHub.

6 - Crear una Base de Dato llamada desafio_blog

7 - Hacer la migración de las Entidades a la Base de Datos ingresando en la consola:
    
    $ php bin/console doctrine:migrations:migrate

8 - Cargar los datos de Fixtures a las tablas:
    
    $ php bin/console doctrine:fixtures:load

9 - Ingrese para ejecutar el proyecto en el puerto configurado (localhost:8000):
    
    $ php bin/console server:run