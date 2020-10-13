<?php

declare(strict_types=1);

namespace brunoconte3\Test;

use brunoconte3\Validation\Validator;
use PHPUnit\Framework\TestCase;

class UnitTestRule extends TestCase
{
    public function testOptional(): void
    {
        $validator = new Validator();
        $validator->set(['teste' => null], ['teste' => 'optional|min:2|int']);
        $this->assertFalse($validator->getErros());
    }

    public function testRequired(): void
    {
        $array = ['a' => '', 'b' => null, 'c' => false];
        $rules = ['a' => 'required', 'b' => 'required', 'c' => 'required'];

        $vR = new Validator();
        $vR->set($array, $rules);
        $this->assertCount(3, $vR->getErros());
    }
}
