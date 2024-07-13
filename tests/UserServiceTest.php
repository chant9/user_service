<?php

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Chant9\UserService\UserService;
use Chant9\UserService\User;

class UserServiceTest extends TestCase
{
    public function testGetUserById()
    {
        // Create a mock response for the client to return.
        $mock = new MockHandler([
            new Response(200, [], json_encode(['data' => [
                'id' => 1,
                'first_name' => 'George',
                'last_name' => 'Bluth',
                'email' => 'george.bluth@email.com'
            ]]))
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        // Run the service code to test.
        $userService = new UserService($client);
        $user = $userService->getUserById(1);

        // Perform assertions.
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals(1, $user->getId());
        $this->assertEquals('George', $user->getFirstName());
        $this->assertEquals('Bluth', $user->getLastName());
        $this->assertEquals('George Bluth', $user->getFullName());
        $this->assertEquals('george.bluth@email.com', $user->getEmail());
    }

    public function testGetInvalidUserById()
    {
        // Create a mock response for the client to return.
        $mock = new MockHandler([
            new Response(404, [], json_encode([]))
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        // Run the service code to test.
        $userService = new UserService($client);
        $user = $userService->getUserById(1);

        // Perform assertions.
        $this->assertEquals(null, $user);
    }

    public function testGetPaginatedUsers()
    {
        // Create a mock response for the client to return.
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'data' => [
                    [
                        'id' => 1,
                        'first_name' => 'George',
                        'last_name' => 'Bluth',
                        'email' => 'george.bluth@email.com'
                    ],
                    [
                        'id' => 2,
                        'first_name' => 'Janet',
                        'last_name' => 'Weaver',
                        'email' => 'janet.weaver@email.com'
                    ]
                ]
            ]))
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        // Run the service code to test.
        $userService = new UserService($client);
        $users = $userService->getPaginatedUsers(1);

        // Perform assertions.
        $this->assertIsArray($users);
        $this->assertCount(2, $users);
        $this->assertInstanceOf(User::class, $users[0]);
        $this->assertEquals(1, $users[0]->getId());
        $this->assertEquals('George', $users[0]->getFirstName());
        $this->assertEquals('Bluth', $users[0]->getLastName());
        $this->assertEquals('George Bluth', $users[0]->getFullName());
        $this->assertEquals('george.bluth@email.com', $users[0]->getEmail());
    }

    public function testCreateUserWithEmail()
    {
        // Create a mock response for the client to return.
        $mock = new MockHandler([
            new Response(201, [], json_encode([
                'id' => 100,
                'first_name' => 'Tom',
                'last_name' => 'Smith',
                'email' => 'tom.smith@email.com'
            ]))
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        // Run the service code to test.
        $userService = new UserService($client);
        $user = $userService->createUser('Tom', 'Smith', email: 'tom.smith@email.com');

        // Perform assertions.
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals(100, $user->getId());
        $this->assertEquals('Tom', $user->getFirstName());
        $this->assertEquals('Smith', $user->getLastName());
        $this->assertEquals('Tom Smith', $user->getFullName());
        $this->assertEquals('tom.smith@email.com', $user->getEmail());
    }

    public function testCreateUserWithJob()
    {
        // Create a mock response for the client to return.
        $mock = new MockHandler([
            new Response(201, [], json_encode([
                'id' => 100,
                'first_name' => 'Tom',
                'last_name' => 'Smith',
                'job' => 'Painter'
            ]))
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        // Run the service code to test.
        $userService = new UserService($client);
        $user = $userService->createUser('Tom', 'Smith', job: 'Painter');

        // Perform assertions.
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals(100, $user->getId());
        $this->assertEquals('Tom', $user->getFirstName());
        $this->assertEquals('Smith', $user->getLastName());
        $this->assertEquals('Tom Smith', $user->getFullName());
        $this->assertEquals('Painter', $user->getJob());
    }
}
