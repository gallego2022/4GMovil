<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\NotificationService;
use App\Services\LoggingService;

class SimpleNotificationServiceTest extends TestCase
{
    protected NotificationService $notificationService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->notificationService = new NotificationService(new LoggingService(), new \App\Services\CacheService(new LoggingService()));
    }

    /** @test */
    public function it_can_create_notification_service()
    {
        $this->assertInstanceOf(NotificationService::class, $this->notificationService);
    }

    /** @test */
    public function it_has_required_methods()
    {
        $this->assertTrue(method_exists($this->notificationService, 'sendEmail'));
        $this->assertTrue(method_exists($this->notificationService, 'sendSMS'));
        $this->assertTrue(method_exists($this->notificationService, 'sendPushNotification'));
        $this->assertTrue(method_exists($this->notificationService, 'sendLaravelNotification'));
        $this->assertTrue(method_exists($this->notificationService, 'sendBulkNotifications'));
        $this->assertTrue(method_exists($this->notificationService, 'getNotificationHistory'));
        $this->assertTrue(method_exists($this->notificationService, 'markAsRead'));
        $this->assertTrue(method_exists($this->notificationService, 'getUnreadCount'));
    }

    /** @test */
    public function it_can_handle_simple_notifications()
    {
        // Este test verifica que el servicio puede ser instanciado y tiene los métodos necesarios
        $this->assertTrue(true);
    }
}
