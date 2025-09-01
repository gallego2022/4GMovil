<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\ValidationService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;


class ValidationServiceTest extends TestCase
{

    protected ValidationService $validationService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validationService = new ValidationService(new \App\Services\LoggingService());
    }

    /** @test */
    public function it_can_validate_data_with_rules()
    {
        $data = ['email' => 'test@example.com', 'password' => 'password123'];
        $rules = ['email' => 'required|email', 'password' => 'required|min:8'];

        $result = $this->validationService->validate($data, $rules);
        
        $this->assertEquals($data, $result);
    }

    /** @test */
    public function it_throws_exception_for_invalid_data()
    {
        $this->expectException(ValidationException::class);

        $data = ['email' => 'invalid-email'];
        $rules = ['email' => 'required|email'];

        $this->validationService->validate($rules, $data);
    }

    /** @test */
    public function it_can_validate_user_data()
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $result = $this->validationService->validateUser($userData);
        
        $this->assertEquals($userData, $result);
    }

    /** @test */
    public function it_can_validate_product_data()
    {
        $productData = [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 99.99,
            'category_id' => 1
        ];

        $result = $this->validationService->validateProduct($productData);
        
        $this->assertEquals($productData, $result);
    }

    /** @test */
    public function it_can_validate_address_data()
    {
        $addressData = [
            'street' => '123 Main St',
            'city' => 'Test City',
            'state' => 'Test State',
            'postal_code' => '12345',
            'country' => 'Test Country'
        ];

        $result = $this->validationService->validateAddress($addressData);
        
        $this->assertEquals($addressData, $result);
    }

    /** @test */
    public function it_can_validate_payment_data()
    {
        $paymentData = [
            'amount' => 99.99,
            'currency' => 'USD',
            'payment_method' => 'stripe'
        ];

        $result = $this->validationService->validatePayment($paymentData);
        
        $this->assertEquals($paymentData, $result);
    }

    /** @test */
    public function it_can_validate_search_parameters()
    {
        $searchData = [
            'query' => 'test search',
            'category' => 'electronics',
            'min_price' => 10,
            'max_price' => 100
        ];

        $result = $this->validationService->validateSearch($searchData);
        
        $this->assertEquals($searchData, $result);
    }

    /** @test */
    public function it_can_validate_file_upload()
    {
        $fileData = [
            'file' => 'test.jpg',
            'max_size' => 2048,
            'allowed_types' => ['jpg', 'png', 'gif']
        ];

        $result = $this->validationService->validateFile($fileData);
        
        $this->assertEquals($fileData, $result);
    }
}
