<?php

require 'vendor/autoload.php';

use Chant9\UserService\UserService;

// Instantiate the UserService.
$userService = new UserService();

// Retrieve a paginated list of users.
try {
    // Basic get/set the options, validation handled in service.
    $page = filter_var($_GET['page'] ?? 1, FILTER_VALIDATE_INT);
    $perPage = filter_var($_GET['per_page'] ?? 5, FILTER_VALIDATE_INT);

    // Get the list of users.
    echo '<p><strong>Paginated users:</strong></p>';
    $users = $userService->getPaginatedUsers($page, $perPage);
    echo '<pre>' . json_encode($users, JSON_PRETTY_PRINT) . '</pre>';

    echo '<hr/>';

    // Create a user (names, email and job).
    echo '<p><strong>Creating a user (names, email and job):</strong></p>';
    $user = $userService->createUser('Tom', 'Smith', 'tom.smith@email.com', 'Painter');
    echo '<pre>' . json_encode($user, JSON_PRETTY_PRINT) . '</pre>';

    echo '<hr/>';

    // Create a user (names and job only).
    echo '<p><strong>Creating a user (names and job only):</strong></p>';
    $user = $userService->createUser('Tom', 'Smith', job: 'Painter');
    echo '<pre>' . json_encode($user, JSON_PRETTY_PRINT) . '</pre>';

    echo '<hr/>';

    // Retrieve a user by ID.
    echo '<p><strong>Retrieve a user by ID:</strong></p>';
    $user = $userService->getUserById(5);
    echo '<pre>' . json_encode($user, JSON_PRETTY_PRINT) . '</pre>';
}
catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
