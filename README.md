1 - Introduction

- Project description : Todolist application
- Project version : 2
- Author : Christophe Martel
- Creation date : 18/11/2016
- Update date : 20/06/2024

2 - Requirements

- Environnment : PHP 8.2, Composer, Symfony CLI, scoop, Git
- PHP Framework : Symfony 6.4
- Tests bundles : dama/doctrine-test-bundle, phpunit/phpunit, zenstruck/foundry

3 - Installation instructions

A - If the following tools are not installed on your computer then folow the links below to install them :

  - Git : https://git-scm.com/
  - Composer : https://getcomposer.org/
  - Scoop : https://scoop.sh/
  - Symfony CLI : https://symfony.com/download


B - Start the git bash application

C - In the directory of your choice on your web server, clone the github repository, here is the command : git clone https://github.com/gitcmartel/todolist.git

D - Run the following command to get the project dependencies : composer update

E - Create the mysql database 'todolist' and execute this command to create the table structure : php bin/console doctrine:migrations:migrate

