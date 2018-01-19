## A simple blog system written in PHP as a school project
Made with this differents courses:

* ["Programmez en orienté objet en PHP"](https://openclassrooms.com/courses/programmez-en-oriente-objet-en-php) by OpenClassroom
* ["Learn OO"](https://knpuniversity.com/tracks/oo) by Knp University
* and mostly ["Mise en pratique de la POO en PHP"](https://www.grafikart.fr/formations/mise-pratique-poo) by Grafikart


## Installation

### Installation requirements:

* LAMP system
* Git
* Composer

1. Clone the repository in `/path/to/your/blog`
2. Install the dependencies with composer
3. Define configuration constants in `/path/to/your/blog/config/config-dist.php` then rename it config.php
4. Set up your database and fill it with data by using phinx in a dev environment (http://docs.phinx.org/en/latest/)
    * Create your database with the same name you used in config.php
    
    * Create the tables with phinx migrate.
    
            $ ENV=dev vendor/bin/phinx migrate
    
    * Fill it with data with phinx seed:run. (To avoid foreign keys check trouble, seed the user first, then the categories, then the posts)
            
            $ ENV=dev vendor/bin/phinx seed:run -s UserSeeder
            
            $ ENV=dev vendor/bin/phinx seed:run -s CategorySeeder
            
            $ ENV=dev vendor/bin/phinx seed:run -s PostSeeder
            
        Note that the seeds are optionnals but you'll need to add your own data.
5. Start your local server using php in a dev environment

        ENV=dev php -S localhost:8000 -t /public
 
### Issues:
The project is still under development.

1. Contact Module

    For the contact module to work you must also edit `src/Framework/SwiftMailerFactory`
    and define your Swift_SmtpTransport. It works with a low protection gmail account, for exemple.
    
2. File upload

    If you upload the same image in multiple articles
    you will end up with something like that
    `your_file_copy_copy_copy_copy_copy.jpg`.
    
    Improve this behavior (i.e allow the same image to be used multiple time) is on my todolist.
    
3. Users role

    All new users are 'admin' when created and you can't yet change an user role on the fly in the dashboard.
