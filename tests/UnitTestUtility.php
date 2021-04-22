<?php

declare(strict_types=1);

namespace brunoconte3\Test;

use brunoconte3\Validation\Utility;
use PHPUnit\Framework\TestCase;

class UnitTestUtility extends TestCase
{
    public function testCaptureClientIp(): void
    {
        $ip = Utility::captureClientIp();
        $this->assertNull($ip); //Phpunit not read global ambient
    }
}
