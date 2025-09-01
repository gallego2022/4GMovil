<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\CacheService;
use App\Services\LoggingService;
use Illuminate\Support\Facades\Cache;


class CacheServiceTest extends TestCase
{

    protected CacheService $cacheService;
    protected LoggingService $loggingService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loggingService = new LoggingService();
        $this->cacheService = new CacheService($this->loggingService);
    }

    /** @test */
    public function it_can_set_cache_value()
    {
        $key = 'test_key';
        $value = 'test_value';
        $ttl = 3600;

        Cache::shouldReceive('put')
            ->once()
            ->with($key, $value, $ttl)
            ->andReturn(true);

        $result = $this->cacheService->set($key, $value, $ttl);
        
        $this->assertTrue($result);
    }

    /** @test */
    public function it_can_get_cache_value()
    {
        $key = 'test_key';
        $expectedValue = 'test_value';

        Cache::shouldReceive('get')
            ->once()
            ->with($key)
            ->andReturn($expectedValue);

        $result = $this->cacheService->get($key);
        
        $this->assertEquals($expectedValue, $result);
    }

    /** @test */
    public function it_can_get_cache_value_with_default()
    {
        $key = 'test_key';
        $default = 'default_value';

        Cache::shouldReceive('get')
            ->once()
            ->with($key, $default)
            ->andReturn($default);

        $result = $this->cacheService->get($key, $default);
        
        $this->assertEquals($default, $result);
    }

    /** @test */
    public function it_can_check_if_key_exists()
    {
        $key = 'test_key';

        Cache::shouldReceive('has')
            ->once()
            ->with($key)
            ->andReturn(true);

        $result = $this->cacheService->has($key);
        
        $this->assertTrue($result);
    }

    /** @test */
    public function it_can_forget_cache_key()
    {
        $key = 'test_key';

        Cache::shouldReceive('forget')
            ->once()
            ->with($key)
            ->andReturn(true);

        $result = $this->cacheService->forget($key);
        
        $this->assertTrue($result);
    }

    /** @test */
    public function it_can_remember_cache_value()
    {
        $key = 'test_key';
        $ttl = 3600;
        $expectedValue = 'test_value';
        $callback = function () {
            return 'test_value';
        };

        Cache::shouldReceive('remember')
            ->once()
            ->with($key, $ttl, $callback)
            ->andReturn($expectedValue);

        $result = $this->cacheService->remember($key, $ttl, $callback);
        
        $this->assertEquals($expectedValue, $result);
    }

    /** @test */
    public function it_can_remember_forever()
    {
        $key = 'test_key';
        $expectedValue = 'test_value';
        $callback = function () {
            return 'test_value';
        };

        Cache::shouldReceive('rememberForever')
            ->once()
            ->with($key, $callback)
            ->andReturn($expectedValue);

        $result = $this->cacheService->rememberForever($key, $callback);
        
        $this->assertEquals($expectedValue, $result);
    }

    /** @test */
    public function it_can_increment_cache_value()
    {
        $key = 'test_key';
        $value = 5;
        $expectedValue = 10;

        Cache::shouldReceive('increment')
            ->once()
            ->with($key, $value)
            ->andReturn($expectedValue);

        $result = $this->cacheService->increment($key, $value);
        
        $this->assertEquals($expectedValue, $result);
    }

    /** @test */
    public function it_can_decrement_cache_value()
    {
        $key = 'test_key';
        $value = 3;
        $expectedValue = 7;

        Cache::shouldReceive('decrement')
            ->once()
            ->with($key, $value)
            ->andReturn($expectedValue);

        $result = $this->cacheService->decrement($key, $value);
        
        $this->assertEquals($expectedValue, $result);
    }

    /** @test */
    public function it_can_flush_all_cache()
    {
        Cache::shouldReceive('flush')
            ->once()
            ->andReturn(true);

        $result = $this->cacheService->flush();
        
        $this->assertTrue($result);
    }

    /** @test */
    public function it_can_get_cache_stats()
    {
        $result = $this->cacheService->getStats();
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('hits', $result);
        $this->assertArrayHasKey('misses', $result);
        $this->assertArrayHasKey('keys', $result);
        $this->assertArrayHasKey('memory_usage', $result);
    }


}
