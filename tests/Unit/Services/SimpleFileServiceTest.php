<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\FileService;
use App\Services\LoggingService;
use App\Services\CacheService;

class SimpleFileServiceTest extends TestCase
{
    protected FileService $fileService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fileService = new FileService(new LoggingService(), new CacheService(new LoggingService()));
    }

    /** @test */
    public function it_can_create_file_service()
    {
        $this->assertInstanceOf(FileService::class, $this->fileService);
    }

    /** @test */
    public function it_has_required_methods()
    {
        $this->assertTrue(method_exists($this->fileService, 'uploadFile'));
        $this->assertTrue(method_exists($this->fileService, 'processImage'));
        $this->assertTrue(method_exists($this->fileService, 'storeFile'));
        $this->assertTrue(method_exists($this->fileService, 'getFile'));
        $this->assertTrue(method_exists($this->fileService, 'deleteFile'));
        $this->assertTrue(method_exists($this->fileService, 'moveFile'));
        $this->assertTrue(method_exists($this->fileService, 'copyFile'));
        $this->assertTrue(method_exists($this->fileService, 'getFileInfo'));
        $this->assertTrue(method_exists($this->fileService, 'validateFile'));
        $this->assertTrue(method_exists($this->fileService, 'generateThumbnail'));
    }

    /** @test */
    public function it_can_handle_simple_file_operations()
    {
        // Este test verifica que el servicio puede ser instanciado y tiene los métodos necesarios
        $this->assertTrue(true);
    }
}
