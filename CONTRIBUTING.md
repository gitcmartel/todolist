# Contribute to the Todolist project

Thank you for your interest in contributing to this Todolist project! This document explains how you can contribute effectively.


## Installation

1. Fork the repository to your GitHub account by clicking on the “Fork” button.

2. Read the README.md file for requirements and installation instructions.


## Suggest changes

1. If you have a feature idea or bug fix, open an issue to discuss it first.

2. Follow the steps in the Git Workflow section to create and submit a Pull Request.


## Git Workflow

1. Create a new branch to work on your feature or fix

    git checkout -b feature/feature-name

2. Make clear commits for each change you make

    git add .
    git commit -m "Description of your modification"

3. Push your modifications

    git push origin feature/feature-name

4. Create a Pull Request

    Once your changes are pushed, create a Pull Request from your forked repository to the main repository.


## Tests

Before submitting a Pull Request, make sure all changes are tested.

To run PHP tests : php bin/phpunit tests


## Code style

Follow PSR-12 coding conventions for PHP. Use php-cs-fixer to format your code.

Run the following command to install php-cs-fixer : composer require --dev friendsofphp/php-cs-fixer

Run the following command to execute php-cs-fixer on your src directory : php vendor/bin/php-cs-fixer fix src


## Support

For any questions or help, feel free to open an issue or contact the project maintainers.

Thank you for your contributions !