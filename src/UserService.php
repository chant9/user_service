<?php

namespace Chant9\UserService;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

class UserService
{
    private Client $client;

    /**
     * @param \GuzzleHttp\Client|null $client
     */
    public function __construct(?Client $client = null)
    {
        // Initialise the Guzzle client.
        if (!$client) {
            $client = new Client([
                'base_uri' => 'https://reqres.in/api/',
                'timeout' => 5,
            ]);
        }
        $this->client = $client;
    }

    /**
     * Retrieve a User via the API, and return the User object.
     *
     * @param int $id
     * @return \Chant9\UserService\User|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getUserById(int $id): ?User
    {
        $response = $this->request(
            "users/{$id}",
            'get'
        );

        // If request was successful, transform the response into an array of User classes.
        if ($response->getStatusCode() === 200) {
            // Get the response into an array.
            $data = json_decode($response->getBody(), true)['data'];

            // Transform response into a User object.
            return $this->transformUser($data);
        }
        else {
            // An error occurred, check the logs.
            // *monolog/monolog & vlucas/phpdotenv could be used to manage this, but were not implemented at this stage.
            return null;
        }
    }

    /**
     * Return an array of User objects via the API.
     *
     * @param int $page
     * @param int $perPage
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPaginatedUsers(int $page = 1, int $perPage = 5): array
    {
        // Simple validation of the parameters.
        $page = filter_var($page, FILTER_VALIDATE_INT, [
            'options' => [
                'default' => 1,
                'min_range' => 1,
            ]
        ]);
        $perPage = filter_var($perPage, FILTER_VALIDATE_INT, [
            'options' => [
                'default' => 5,
                'min_range' => 1,
                'max_range' => 10
            ]
        ]);

        // Make the request to the API.
        $response = $this->request(
            'users',
            'get',
            [
                'query' => [
                    'page' => $page,
                    'per_page' => $perPage
                ],
            ]
        );

        // If the request was successful, transform the response into an array of User objects.
        if ($response->getStatusCode() === 200) {
            return $this->transformUsers($response);
        }
        else {
            // An error occurred, check the logs.
            // *monolog/monolog & vlucas/phpdotenv could be used to manage this, but were not implemented at this stage.
            return ['Sorry an error occurred'];
        }
    }

    /**
     * Use the API to create a User, and return the User object.
     *
     * @param string $firstName
     * @param string $lastName
     * @param string $email
     * @param string $job
     * @return \Chant9\UserService\User|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createUser(string $firstName, string $lastName, string $email = '', string $job = ''): ?User
    {
        // Simple validation of the email address.
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);

        $response = $this->request(
            'users',
            'post',
            [
                'json' => [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $email,
                    'job' => $job
                ],
            ]
        );

        // If the request was successful, transform the response into a User object.
        if ($response->getStatusCode() === 201) {
            // Get the response into an array.
            $data = json_decode($response->getBody(), true);

            // Transform response into a User object.
            return $this->transformUser($data);
        }
        else {
            // An error occurred, check the logs.
            // *monolog/monolog & vlucas/phpdotenv could be used to manage this, but were not implemented at this stage.
            return null;
        }
    }

    /**
     * Transform the array into a User object.
     *
     * @param array $item
     * @return \Chant9\UserService\User|null
     */
    private function transformUser(array $item): ?User
    {
        if (isset($item['id']) && isset($item['first_name']) && isset($item['last_name'])) {
            return new User(
                $item['id'],
                $item['first_name'],
                $item['last_name'],
                $item['email'] ?? '',
                $item['job'] ?? ''
            );
        }

        return null;
    }

    /**
     * Transform the response into an and array of User objects.
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return array
     */
    private function transformUsers(ResponseInterface $response): array
    {
        // Get the response into an array.
        $data = json_decode($response->getBody(), true)['data'];

        // Transform each item into a User object.
        return array_filter(array_map(function ($item) {
            return $this->transformUser($item);
        }, $data), fn($value) => !is_null($value));
    }

    /**
     * Perform the API request and catch any errors.
     *
     * @param string $url
     * @param string $method
     * @param array $options
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function request(string $url, string $method, array $options = []): ResponseInterface
    {
        try {
            switch ($method) {
                case 'get':
                    return $this->client->get($url, $options);
                case 'post':
                    return $this->client->post($url, $options);
                case 'put':
                    return $this->client->put($url, $options);
                case 'patch':
                    return $this->client->patch($url, $options);
                case 'delete':
                    return $this->client->delete($url, $options);
            }
        } catch (ClientException $e) {
            return new Response(400, [], 'Client error: ' . $e->getMessage());
        } catch (ServerException $e) {
            return new Response(500, [], 'Server error: ' . $e->getMessage());
        } catch (ConnectException $e) {
            return new Response(502, [], 'Connection error: ' . $e->getMessage());
        } catch (RequestException $e) {
            return new Response(500, [], 'Request error: ' . $e->getMessage());
        } catch (Exception $e) {
            return new Response(500, [], 'Unknown error: ' . $e->getMessage());
        }

        return new Response(500, [], 'Unknown error');
    }
}
