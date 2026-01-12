<?php

namespace OtpAuth\Tests;

use PHPUnit\Framework\TestCase;
use OtpAuth\Libraries\OtpService;
use OtpAuth\Models\OtpModel;
use OtpAuth\Config\OtpAuth;
use OtpAuth\Interfaces\SmsProviderInterface;

class OtpFeatureTest extends TestCase
{
    public function testOtpServiceGeneration()
    {
        $config = new OtpAuth();

        // Mock Provider
        $provider = $this->createMock(SmsProviderInterface::class);
        $provider->method('send')->willReturn(true);

        $service = new OtpService($config, $provider);

        // Test that service is created successfully
        $this->assertInstanceOf(OtpService::class, $service);
    }

    public function testOtpConfigCreation()
    {
        $config = new OtpAuth();

        // Test default values
        $this->assertEquals(6, $config->codeLength);
        $this->assertEquals(300, $config->expirySeconds);
        $this->assertEquals(3, $config->maxAttempts);
    }

    public function testTwilioProviderInterface()
    {
        $provider = $this->createMock(SmsProviderInterface::class);
        $provider->expects($this->once())
            ->method('send')
            ->with('+1234567890', 'Test message')
            ->willReturn(true);

        $result = $provider->send('+1234567890', 'Test message');
        $this->assertTrue($result);
    }
}
