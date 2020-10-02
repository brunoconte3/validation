<?php

declare(strict_types=1);

namespace brunoconte3\Test;

use brunoconte3\Validation\Format;
use PHPUnit\Framework\TestCase;

class UnitTest extends TestCase
{
    public function testCompanyIdentification(): void
    {
        $this->assertEquals('76.027.484/0001-24', Format::companyIdentification('76027484000124'));
    }

    public function testIdentifier(): void
    {
        $this->assertEquals('894.213.600-10', Format::identifier('89421360010'));
    }
}
