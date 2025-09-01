<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;


class BaseControllerTest extends TestCase
{

    protected BaseController $baseController;

    protected function setUp(): void
    {
        parent::setUp();
        $this->baseController = new class extends BaseController {
            public function testSuccessResponse()
            {
                return $this->successResponse(['data' => 'test'], 'Test message');
            }

            public function testErrorResponse()
            {
                return $this->errorResponse('Test error', 400);
            }

            public function testNotFoundResponse()
            {
                return $this->notFoundResponse('Resource not found');
            }

            public function testValidationErrorResponse()
            {
                return $this->validationErrorResponse(['field' => 'error message']);
            }

            public function testUnauthorizedResponse()
            {
                return $this->unauthorizedResponse('Unauthorized access');
            }

            public function testForbiddenResponse()
            {
                return $this->forbiddenResponse('Access forbidden');
            }

            public function testServerErrorResponse()
            {
                return $this->serverErrorResponse('Internal server error');
            }
        };
    }

    /** @test */
    public function it_can_return_success_response()
    {
        $response = $this->baseController->testSuccessResponse();
        
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        
        $data = json_decode($response->getContent(), true);
        $this->assertTrue($data['success']);
        $this->assertEquals('Test message', $data['message']);
        $this->assertEquals(['data' => 'test'], $data['data']);
        $this->assertArrayHasKey('timestamp', $data);
    }

    /** @test */
    public function it_can_return_error_response()
    {
        $response = $this->baseController->testErrorResponse();
        
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(400, $response->getStatusCode());
        
        $data = json_decode($response->getContent(), true);
        $this->assertFalse($data['success']);
        $this->assertEquals('Test error', $data['message']);
        $this->assertArrayHasKey('timestamp', $data);
    }

    /** @test */
    public function it_can_return_not_found_response()
    {
        $response = $this->baseController->testNotFoundResponse();
        
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(404, $response->getStatusCode());
        
        $data = json_decode($response->getContent(), true);
        $this->assertFalse($data['success']);
        $this->assertEquals('Resource not found', $data['message']);
        $this->assertEquals(404, $data['code']);
    }

    /** @test */
    public function it_can_return_validation_error_response()
    {
        $response = $this->baseController->testValidationErrorResponse();
        
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(422, $response->getStatusCode());
        
        $data = json_decode($response->getContent(), true);
        $this->assertFalse($data['success']);
        $this->assertEquals('Validation failed', $data['message']);
        $this->assertEquals(['field' => 'error message'], $data['errors']);
        $this->assertEquals(422, $data['code']);
    }

    /** @test */
    public function it_can_return_unauthorized_response()
    {
        $response = $this->baseController->testUnauthorizedResponse();
        
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(401, $response->getStatusCode());
        
        $data = json_decode($response->getContent(), true);
        $this->assertFalse($data['success']);
        $this->assertEquals('Unauthorized access', $data['message']);
        $this->assertEquals(401, $data['code']);
    }

    /** @test */
    public function it_can_return_forbidden_response()
    {
        $response = $this->baseController->testForbiddenResponse();
        
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(403, $response->getStatusCode());
        
        $data = json_decode($response->getContent(), true);
        $this->assertFalse($data['success']);
        $this->assertEquals('Access forbidden', $data['message']);
        $this->assertEquals(403, $data['code']);
    }

    /** @test */
    public function it_can_return_server_error_response()
    {
        $response = $this->baseController->testServerErrorResponse();
        
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(500, $response->getStatusCode());
        
        $data = json_decode($response->getContent(), true);
        $this->assertFalse($data['success']);
        $this->assertEquals('Internal server error', $data['message']);
        $this->assertEquals(500, $data['code']);
    }

    /** @test */
    public function it_includes_timestamp_in_all_responses()
    {
        $response = $this->baseController->testSuccessResponse();
        $data = json_decode($response->getContent(), true);
        
        $this->assertArrayHasKey('timestamp', $data);
        $this->assertIsString($data['timestamp']);
        $this->assertNotEmpty($data['timestamp']);
    }

    /** @test */
    public function it_includes_request_id_when_available()
    {
        // Simular request con ID
        $request = Request::create('/test', 'GET');
        $request->attributes->set('request_id', 'req_123');
        
        $this->app->instance('request', $request);
        
        $response = $this->baseController->testSuccessResponse();
        $data = json_decode($response->getContent(), true);
        
        $this->assertArrayHasKey('request_id', $data);
        $this->assertEquals('req_123', $data['request_id']);
    }

    /** @test */
    public function it_can_return_paginated_response()
    {
        $data = ['items' => [1, 2, 3]];
        $pagination = [
            'current_page' => 1,
            'per_page' => 10,
            'total' => 30,
            'last_page' => 3
        ];
        
        $response = $this->baseController->paginatedResponse($data, $pagination, 'Data retrieved successfully');
        
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        
        $responseData = json_decode($response->getContent(), true);
        $this->assertTrue($responseData['success']);
        $this->assertEquals('Data retrieved successfully', $responseData['message']);
        $this->assertEquals($data, $responseData['data']);
        $this->assertEquals($pagination, $responseData['pagination']);
    }

    /** @test */
    public function it_can_return_created_response()
    {
        $data = ['id' => 1, 'name' => 'Test'];
        
        $response = $this->baseController->createdResponse($data, 'Resource created successfully');
        
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());
        
        $responseData = json_decode($response->getContent(), true);
        $this->assertTrue($responseData['success']);
        $this->assertEquals('Resource created successfully', $responseData['message']);
        $this->assertEquals($data, $responseData['data']);
    }

    /** @test */
    public function it_can_return_no_content_response()
    {
        $response = $this->baseController->noContentResponse('Resource deleted successfully');
        
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(204, $response->getStatusCode());
        
        $responseData = json_decode($response->getContent(), true);
        $this->assertTrue($responseData['success']);
        $this->assertEquals('Resource deleted successfully', $responseData['message']);
    }
}
