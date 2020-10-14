<?php

declare(strict_types=1);

namespace brunoconte3\Test;

use brunoconte3\Validation\Validator;
use PHPUnit\Framework\TestCase;

class UnitTestRule extends TestCase
{
    public function testCompanyIdentification(): void
    {
        $array = ['testError' => '52186923000120', 'testValid' => '21111527000163'];
        $rules = ['testError' => 'companyIdentification', 'testValid' => 'companyIdentification'];

        $validator = new Validator();
        $validator->set($array, $rules);
        $this->assertCount(1, $validator->getErros());
    }

    public function testIdentifier(): void
    {
        $array = ['testError' => '06669987788', 'testValid' => '55634405831'];
        $rules = ['testError' => 'identifier', 'testValid' => 'identifier'];

        $validator = new Validator();
        $validator->set($array, $rules);
        $this->assertCount(1, $validator->getErros());
    }

    public function testMax(): void
    {
        $array = ['testError' => 123, 'testValid' => 1234];
        $rules = ['testError' => 'max:2', 'testValid' => 'max:4'];

        $validator = new Validator();
        $validator->set($array, $rules);
        $this->assertCount(1, $validator->getErros());
    }

    public function testMin(): void
    {
        $array = ['testError' => '123', 'testValid' => '1234'];
        $rules = ['testError' => 'min:5', 'testValid' => 'min:4'];

        $validator = new Validator();
        $validator->set($array, $rules);
        $this->assertCount(1, $validator->getErros());
    }

    public function testOptional(): void
    {
        $validator = new Validator();
        $validator->set(['test' => null], ['test' => 'optional|min:2|int']);
        $this->assertFalse($validator->getErros());
    }

    public function testPhone(): void
    {
        $array = ['testError' => '444569874', 'testValid' => '4433467847', 'testMask' => '(44) 99932-5847'];
        $rules = ['testError' => 'phone', 'testValid' => 'phone', 'testMask' => 'phone'];

        $validator = new Validator();
        $validator->set($array, $rules);
        $this->assertCount(1, $validator->getErros());
    }

    public function testRequired(): void
    {
        $array = ['a' => '', 'b' => null, 'c' => false];
        $rules = ['a' => 'required', 'b' => 'required', 'c' => 'required'];

        $validator = new Validator();
        $validator->set($array, $rules);
        $this->assertCount(3, $validator->getErros());
    }
}
