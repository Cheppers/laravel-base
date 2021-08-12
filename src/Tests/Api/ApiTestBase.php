<?php

/**
 * Test APIs.
 */

namespace Cheppers\LaravelBase\Tests\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Cheppers\LaravelBase\Tests\Constraints\IsValidJsonAPISchema;
use Cheppers\LaravelBase\Tests\Constraints\IsValidJsonSchema;
use Tests\TestCase;

class ApiTestBase extends TestCase
{
    use DatabaseMigrations;

    protected $loggedToken;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function createAndLoginActivatedUser()
    {
        $this->user = User::factory()->create([
            'name' => 'testuser',
            'email' => 'testuser@example.com',
            'password' => \Hash::make('123456'),
            'email_verified_at' => Carbon::now(),
        ]);
        return $this->login('testuser@example.com', '123456');
    }

    protected function login($email, $password)
    {
        $response = $this->postJsonRequest(
            'api/user/login',
            [
                'data' => [
                    'type' => 'user',
                    'attributes' => [
                        'email' => $email,
                        'password' => $password,
                    ],
                ],
            ]
        );
        $responseData = $response->decodeResponseJson();
        $this->loggedToken = $responseData['access_token'];
        return $responseData;
    }

    /**
     * Create a json request.
     *
     * @param string $type
     * @param string $url
     * @param array $jsonData
     * @param array $header
     */
    protected function jsonRequest($type, $url, $jsonData = [], $header = [])
    {
        $header = $header + $this->getHeaderData();
        $response = $this->json(
            $type,
            $url,
            $jsonData,
            $header
        );

        // If response with content.
        if ($response->getStatusCode() != Response::HTTP_NO_CONTENT) {
            $this->assertValidJsonAPIResponse($response->content());
        }
        return $response;
    }

    /**
     * Create a json post request.
     *
     * @param string $url
     * @param array $jsonData
     * @param array $header
     */
    protected function postJsonRequest($url, $jsonData, $header = [])
    {
        return $this->jsonRequest('POST', $url, $jsonData, $header);
    }

    /**
     * Create a json put request.
     *
     * @param string $url
     * @param array $jsonData
     * @param array $header
     */
    protected function putJsonRequest($url, $jsonData, $header = [])
    {
        return $this->jsonRequest('PUT', $url, $jsonData, $header);
    }

    /**
     * Create a json patch request.
     *
     * @param string $url
     * @param array $jsonData
     * @param array $header
     */
    protected function patchJsonRequest($url, $jsonData, $header = [])
    {
        return $this->jsonRequest('PATCH', $url, $jsonData, $header);
    }

    /**
     * Create a json put request.
     *
     * @param string $url
     * @param array $jsonData
     * @param array $header
     */
    protected function getJsonRequest($url, $jsonData = [], $header = [])
    {
        return $this->jsonRequest('GET', $url, $jsonData, $header);
    }

    /**
     * Create a json delete request.
     *
     * @param string $url
     * @param array $jsonData
     * @param array $header
     */
    protected function deleteJsonRequest($url, $jsonData = [], $header = [])
    {
        return $this->jsonRequest('DELETE', $url, $jsonData, $header);
    }

    /**
     * Get default header data.
     *
     * @return array
     */
    protected function getHeaderData()
    {
        $header = [
          'Content-Type' => 'application/vnd.api+json',
          'Accept' => 'application/vnd.api+json',
        ];
        if (!empty($this->loggedToken)) {
            $header['Authorization'] = 'Bearer ' . $this->loggedToken;
        }

        return $header;
    }

    public function assertValidJsonSchema($json, $schema)
    {
        self::assertThat($json, new IsValidJsonSchema($schema));
    }
    public function assertValidJsonAPIResponse($response)
    {
        self::assertThat($response, new IsValidJsonAPISchema());
    }
}
