# Welcome to User Service

## Installation

If the package was submitted to Packagist you would run the command below, however please just clone this repo or use the provided zip file.

```bash
composer require chant9/user_service
```

## Basic Usage

Below shows some available calls, and there is an example.php where you can see them in action.

```php
<?php

use Chant9\UserService\UserService;

// Instantiate the UserService.
$userService = new UserService();

// Make the required calls to interact with the Reqres API.

$users = $userService->getPaginatedUsers(page: 1, perPage: 5);

$user = $userService->createUser('Tom', 'Smith', 'tom.smith@email.com', 'Painter');

$user = $userService->createUser('Tom', 'Smith', job: 'Painter');

$user = $userService->getUserById(5);

```

## Tests

Basic unit tests for the service can be run by using the command below.

```bash
vendor/bin/phpunit tests/UserServiceTest.php
```

### Author

Ashley Chant - <chant9@hotmail.com>