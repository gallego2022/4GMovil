<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\NotificationService;
use App\Services\LoggingService;
use App\Services\CacheService;
use App\Notifications\BaseNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;


class NotificationServiceTest extends TestCase
{

    protected NotificationService $notificationService;
    protected LoggingService $loggingService;
    protected CacheService $cacheService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loggingService = new LoggingService();
        $this->cacheService = new CacheService($this->loggingService);
        $this->notificationService = new NotificationService($this->loggingService, $this->cacheService);
    }

    /** @test */
    public function it_can_send_email()
    {
        $to = 'test@example.com';
        $subject = 'Test Subject';
        $view = 'emails.test';
        $data = ['name' => 'John Doe'];

        Mail::shouldReceive('to')
            ->once()
            ->with($to)
            ->andReturnSelf();

        Mail::shouldReceive('send')
            ->once()
            ->andReturn(true);

        $result = $this->notificationService->sendEmail($to, $subject, $view, $data);
        
        $this->assertTrue($result);
    }

    /** @test */
    public function it_can_send_welcome_email()
    {
        $to = 'test@example.com';
        $name = 'John Doe';
        $data = ['company' => 'Test Company'];

        Mail::shouldReceive('to')
            ->once()
            ->with($to)
            ->andReturnSelf();

        Mail::shouldReceive('send')
            ->once()
            ->andReturn(true);

        $result = $this->notificationService->sendWelcomeEmail($to, $name, $data);
        
        $this->assertTrue($result);
    }

    /** @test */
    public function it_can_send_order_confirmation_email()
    {
        $to = 'test@example.com';
        $orderNumber = 'ORD-12345';
        $orderData = ['total' => 99.99, 'items' => 3];

        Mail::shouldReceive('to')
            ->once()
            ->with($to)
            ->andReturnSelf();

        Mail::shouldReceive('send')
            ->once()
            ->andReturn(true);

        $result = $this->notificationService->sendOrderConfirmationEmail($to, $orderNumber, $orderData);
        
        $this->assertTrue($result);
    }

    /** @test */
    public function it_can_send_password_reset_email()
    {
        $to = 'test@example.com';
        $resetLink = 'https://example.com/reset-password?token=abc123';

        Mail::shouldReceive('to')
            ->once()
            ->with($to)
            ->andReturnSelf();

        Mail::shouldReceive('send')
            ->once()
            ->andReturn(true);

        $result = $this->notificationService->sendPasswordResetEmail($to, $resetLink);
        
        $this->assertTrue($result);
    }

    /** @test */
    public function it_can_send_sms()
    {
        $phone = '+1234567890';
        $message = 'Test SMS message';

        $result = $this->notificationService->sendSMS($phone, $message);
        
        $this->assertIsBool($result);
    }

    /** @test */
    public function it_can_send_push_notification()
    {
        $userId = 123;
        $title = 'Test Push';
        $body = 'Test push notification body';
        $data = ['action' => 'test'];

        $result = $this->notificationService->sendPushNotification($userId, $title, $body, $data);
        
        $this->assertIsBool($result);
    }

    /** @test */
    public function it_can_send_laravel_notification()
    {
        $notifiable = (object) ['id' => 1, 'email' => 'test@example.com'];
        $notification = new class extends BaseNotification {
            protected function getSubject(): string { return 'Test Subject'; }
            protected function getContent(): string { return 'Test Content'; }
        };

        Notification::shouldReceive('send')
            ->once()
            ->with($notifiable, $notification)
            ->andReturn(true);

        $result = $this->notificationService->sendNotification($notifiable, $notification);
        
        $this->assertTrue($result);
    }

    /** @test */
    public function it_can_send_bulk_notifications()
    {
        $notifiables = [
            (object) ['id' => 1, 'email' => 'user1@example.com'],
            (object) ['id' => 2, 'email' => 'user2@example.com']
        ];
        
        $notification = new class extends BaseNotification {
            protected function getSubject(): string { return 'Bulk Test'; }
            protected function getContent(): string { return 'Bulk Test Content'; }
        };

        Notification::shouldReceive('send')
            ->twice()
            ->andReturn(true);

        $result = $this->notificationService->sendBulkNotification($notifiables, $notification);
        
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
    }

    /** @test */
    public function it_can_check_delivery_status()
    {
        $messageId = 'msg_123456';

        $result = $this->notificationService->checkDeliveryStatus($messageId);
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('status', $result);
        $this->assertArrayHasKey('message_id', $result);
    }

    /** @test */
    public function it_can_get_notification_templates()
    {
        $result = $this->notificationService->getNotificationTemplates();
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('welcome', $result);
        $this->assertArrayHasKey('order_confirmation', $result);
        $this->assertArrayHasKey('password_reset', $result);
    }

    /** @test */
    public function it_handles_email_sending_errors()
    {
        $to = 'test@example.com';
        $subject = 'Test Subject';
        $view = 'emails.test';

        Mail::shouldReceive('to')
            ->once()
            ->andThrow(new \Exception('Mail server error'));

        $result = $this->notificationService->sendEmail($to, $subject, $view);
        
        $this->assertFalse($result);
    }

    /** @test */
    public function it_handles_sms_sending_errors()
    {
        $phone = 'invalid-phone';
        $message = 'Test message';

        $result = $this->notificationService->sendSMS($phone, $message);
        
        $this->assertIsBool($result);
    }

    /** @test */
    public function it_handles_push_notification_errors()
    {
        $userId = 0;
        $title = '';
        $body = '';

        $result = $this->notificationService->sendPushNotification($userId, $title, $body);
        
        $this->assertIsBool($result);
    }
}
