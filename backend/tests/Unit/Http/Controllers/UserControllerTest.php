<?php

namespace Tests\Unit\Http\Controllers;

use App\Http\Controllers\UserController;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\LoginUserRequest;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Mockery;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    /**
     * @var \Mockery\MockInterface
     * 
     */
    protected $userServiceMock;

    protected function setUp(): void
    {
        parent::setUp();
        // Mock the UserService
        $this->userServiceMock = Mockery::mock(UserService::class);
        $this->app->instance(UserService::class, $this->userServiceMock);
    }

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testRegisterReturnsSuccessResponse()
    {
        // Mock request with validated data
        $request = Mockery::mock(RegisterUserRequest::class);
        $request->shouldReceive('validated')->once()->andReturn([
            'firstname' => 'John',
            'lastname' => 'Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password123',
        ]);

        // Mock service response
        $this->userServiceMock
            ->shouldReceive('registerUser')
            ->once()
            ->with(['firstname' => 'John', 'lastname' => 'Doe', 'email' => 'john.doe@example.com', 'password' => 'password123'])
            ->andReturn(response()->json([
                'success' => true,
                'message' => 'Registration successful',
            ], 201));

        $controller = new UserController();
        $response = $controller->register($request, $this->userServiceMock);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['success']);
        $this->assertEquals('Registration successful', $responseData['message']);
    }

    public function testRegisterHandlesException()
    {
        // Mock request with validated data
        $request = Mockery::mock(RegisterUserRequest::class);
        $request->shouldReceive('validated')->once()->andReturn([
            'firstname' => 'John',
            'lastname' => 'Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password123',
        ]);

        // Mock service to throw exception
        $this->userServiceMock
            ->shouldReceive('registerUser')
            ->once()
            ->andThrow(new \Exception('Registration failed'));

        $controller = new UserController();
        $response = $controller->register($request, $this->userServiceMock);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(500, $response->getStatusCode());
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['error']);
        $this->assertEquals('An error occurred', $responseData['message']);
    }

    public function testLoginReturnsSuccessResponse()
    {
        // Mock request with validated data
        $request = Mockery::mock(LoginUserRequest::class);
        $request->shouldReceive('validated')->once()->andReturn([
            'email' => 'john.doe@example.com',
            'password' => 'password123',
        ]);

        // Mock service response with token
        $this->userServiceMock
            ->shouldReceive('loginUser')
            ->once()
            ->with(['email' => 'john.doe@example.com', 'password' => 'password123'])
            ->andReturn(response()->json([
                'success' => true,
                'message' => 'Login successful',
                'token' => 'test-token-123',
            ], 200));

        $controller = new UserController();
        $response = $controller->login($request, $this->userServiceMock);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['success']);
        $this->assertEquals('Login successful', $responseData['message']);
        $this->assertEquals('test-token-123', $responseData['token']);
    }

    public function testLoginHandlesException()
    {
        // Mock request with validated data
        $request = Mockery::mock(LoginUserRequest::class);
        $request->shouldReceive('validated')->once()->andReturn([
            'email' => 'john.doe@example.com',
            'password' => 'password123',
        ]);

        // Mock service to throw exception
        $this->userServiceMock
            ->shouldReceive('loginUser')
            ->once()
            ->andThrow(new \Exception('Login failed'));

        $controller = new UserController();
        $response = $controller->login($request, $this->userServiceMock);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(500, $response->getStatusCode());
        $responseData = $response->getData(true);
        $this->assertTrue($responseData['error']);
        $this->assertEquals('An error occurred', $responseData['message']);
    }
}
