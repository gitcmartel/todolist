1 - Introduction

  - Project description : Todolist application
  - Project version : 2
  - Author : Christophe Martel
  - Creation date : 18/11/2016
  - Update date : 20/06/2024

2 - Requirements

  - Environnment : PHP 8.2, Composer, Symfony CLI, scoop
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

4 - Project structure (directories)

  - assets : contains the css, js and images files
  - bin : executables files
  - config : application configuration files
  - migrations : doctrine migration files
  - public : the application entry point
  - src : the project's PHP code
  - templates : twig files (views)
  - tests : the project's PHP tests code
  - translations : not used in this project
  - var : generated files (cache, logs etc...)
  - vendor : the third-party dependencies (bundles)

5 - Configuration

  - At the project's root, in the .env.local file, add your mysql database connection string (there is an exemple written inside, DATABASE_URL="...")
  - Do the same in the .env.test file, this will tell symfony to use a test database when the tests are performed. You can use the same connection string as above,
    symfony will prefix your actual database with the "test" word

6 - Modules and fonctionnalities

  - Controllers :
    -  Home : Display the home page
    -  Login : Display the authentication page, allow a user to log in or log out
    -  Security : Display the access denied page if a user does not have the required permissions
    -  Task : Display the tasks list, creation or edition pages. A user can mark a task as done and edit a task. A user as to be logged in to delete or create a task.
    -  User : Display the users list or creation pages. Only an admin user car access these functionalities.
      
  - Entities :
      In these classes you will find attributes above the properties. They define the database fields properties (length, unique...), or the validation rules. 
    - User : A user can be related to many tasks. This class implements the UserInterface and PasswordAuthenticatedUserInterface.
      The UserInterface is related to the symfony security system that manage the users connections and roles.
    - Task : A task is related to one user.
      
  - Form :
    These are the classes that build the pages forms
    - TaskFormType
    - UserFormType
   
  - Repository :
    These are the classes that handle the sql database operations. Through these classes you can select, insert ...
    - UserRepository
    - TaskRepository

  - Security :
    Classes to handle security operations.
    - AccessDeniedHandler
    - AuthenticationEntryPoint

  - Service :
    Utiliy classes.
    - TaskAuthorizationService : Check if the logged user can delete a task or not. The user has to be the task creator or has to be an admin if the task has
      been create by an anonymous user.

7 - Authentication system

    First of all, the SecurityBundle must be installed: composer require symfony/security-bundle.
    
    A user will need to enter a username and password to be able to access certain parts of the application.

    The page used for authentication is: templates/login/index.html.twig.
    It contains the authentication form.

    When the form is submitted by a user, Symfony manages the authentication processing, it verifies that the username and password information is correct.

    If so, Symfony creates a session for the user, logs them in and redirects the user to the home page.

    In case of failure Symfony redirects the user to the login_path route of the LoginController controller. In this route we retrieve the submitted user name and the error message via the AuthenticationUtils class then we display the authentication form with this data, the user can then make a new connection attempt.

    During the authentication process, multiple events are dispatched that allow you to hook into the process or customize the response sent back to the user. You can do this by creating an event listener or subscriber for these events. You can find the available events list in the security section of the symfony documentation : https://symfony.com/doc/6.4/security.html#authentication-identifying-logging-in-the-user

    Symfony is configured to manage authentication in the config/packages/security.yaml configuration file.

    In this file you can tell Symfony:

      - The name of the doctrine entity that manages users and its unique property that identifies a user: providers: app_user_provider: entity:

        class: App\Entity\User
        property : username
      
      - The route to display the login form: firewalls: main: form_login: login_path: app_login
      - The route to which Symfony will submit the authentication information for validation: firewalls: main: form_login: login_check: app_login
      - The route that will allow Symfony to log out a user: firewalls: main: logout: path: app_logout, and the redirection page after disconnection: target: app_home
      - The class name that the application should use to hash the user's password: password_hashers: Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface: 'auto'
      - When an unauthenticated user tries to access a protected page, this class adds a flash message and redirects them to the login page: firewalls: main: entry_point: App\Security\AuthenticationEntryPoint
      - When the user tries to access a resource for which he does not have the necessary permissions, this class redirects him to the page security\denied.html.twig: firewalls: main: access_denied_handler: App\Security\AccessDeniedHandler
      - Each user is assigned a role. In the application we have configured the following roles:

        o ROLE_ADMIN: he has access to all the pages of the application
        o ROLE_USER: it does not have access to the user management pages

        You can create other roles if you want and have them inherit rights from other roles. For example, the admin role has the rights assigned to the user role: ROLE_ADMIN: [ROLE_USER].

    In the UserController we have prohibited access to routes for users who do not have the Admin role. This is handled by adding the #[IsGranted('ROLE_ADMIN')] attribute above the class.

    If you want to prohibit access to a particular road, you must place the attribute above the road.

    In the database users are stored in the user entity.

8 - Tests

    Unit and functional tests were written in the “tests” directory. The subdirectories mirror those of the "src" folder and contain the tests of the corresponding classes.
    Each test class extends the webtestcase class. 
    The "Foundry" bundle is used to quickly create entities and populate them with test data.
    You can use this command to execute the tests : php bin/phpunit tests.
    You can generate a coverage report by executing this command : php bin/phpunit --coverage-html public/test-coverage


  
   

  
