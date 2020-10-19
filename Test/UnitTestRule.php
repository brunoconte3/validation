<?php

declare(strict_types=1);

namespace brunoconte3\Test;

use brunoconte3\Validation\Validator;
use PHPUnit\Framework\TestCase;

class UnitTestRule extends TestCase
{
    public function testAlpha(): void
    {
        $array = ['testError' => 'a@', 'testValid' => 'aeiouAÉIÓÚ'];
        $rules = ['testError' => 'alpha', 'testValid' => 'alpha'];

        $validator = new Validator();
        $validator->set($array, $rules);
        $this->assertCount(1, $validator->getErros());
    }

    public function testAlphaNoSpecial(): void
    {
        $array = ['testError' => 'aéiou', 'testValid' => 'aEiOU'];
        $rules = ['testError' => 'alphaNoSpecial', 'testValid' => 'alphaNoSpecial'];

        $validator = new Validator();
        $validator->set($array, $rules);
        $this->assertCount(1, $validator->getErros());
    }

    public function testAlphaNum(): void
    {
        $array = ['testError' => 'a1B2Éí3@', 'testValid' => 'a1B2Éí3'];
        $rules = ['testError' => 'alphaNum', 'testValid' => 'alphaNum'];

        $validator = new Validator();
        $validator->set($array, $rules);
        $this->assertCount(1, $validator->getErros());
    }

    public function testAlphaNumNoSpecial(): void
    {
        $array = ['testError' => 'AeioÚ123', 'testValid' => 'AeioU123'];
        $rules = ['testError' => 'alphaNumNoSpecial', 'testValid' => 'alphaNumNoSpecial'];

        $validator = new Validator();
        $validator->set($array, $rules);
        $this->assertCount(1, $validator->getErros());
    }

    public function testArray(): void
    {
        $array = ['testError' => 'a', 'testValid' => ['a' => 1, 'b' => 2]];
        $rules = ['testError' => 'array', 'testValid' => 'array'];

        $validator = new Validator();
        $validator->set($array, $rules);
        $this->assertCount(1, $validator->getErros());
    }

    public function testArrayValues(): void
    {
        $array = ['testError' => 'M', 'testValid' => 'S'];
        $rules = ['testError' => 'arrayValues:S-N-T', 'testValid' => 'arrayValues:S-N-T'];

        $validator = new Validator();
        $validator->set($array, $rules);
        $this->assertCount(1, $validator->getErros());
    }

    public function testBool(): void
    {
        $array = ['testError' => 'a123', 'testValid' => true];
        $rules = ['testError' => 'int', 'testValid' => 'bool'];

        $validator = new Validator();
        $validator->set($array, $rules);
        $this->assertCount(1, $validator->getErros());
    }

    public function testCompanyIdentification(): void
    {
        $array = ['testError' => '52186923000120', 'testValid' => '21111527000163'];
        $rules = ['testError' => 'companyIdentification', 'testValid' => 'companyIdentification'];

        $validator = new Validator();
        $validator->set($array, $rules);
        $this->assertCount(1, $validator->getErros());
    }

    public function testDateAmerican(): void
    {
        $array = ['testError' => '1990-04-31', 'testValid' => '1990-04-30'];
        $rules = ['testError' => 'dateAmerican', 'testValid' => 'dateAmerican'];

        $validator = new Validator();
        $validator->set($array, $rules);
        $this->assertCount(1, $validator->getErros());
    }

    public function testDateBrazil(): void
    {
        $array = ['testError' => '31042020', 'testValid' => '31052020'];
        $rules = ['testError' => 'dateBrazil', 'testValid' => 'dateBrazil'];

        $validator = new Validator();
        $validator->set($array, $rules);
        $this->assertCount(1, $validator->getErros());
    }

    public function testEmail(): void
    {
        $array = ['testError' => 'bruno.com', 'testValid' => 'brunoconte3@gmail.com'];
        $rules = ['testError' => 'email', 'testValid' => 'email'];

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

    public function testInt(): void
    {
        $array = ['testError' => 'a123', 'testValid' => 123];
        $rules = ['testError' => 'int', 'testValid' => 'int'];

        $validator = new Validator();
        $validator->set($array, $rules);
        $this->assertCount(1, $validator->getErros());
    }

    public function testIp(): void
    {
        $array = ['testError' => '1.1.0', 'testValid' => '10.202.0.58'];
        $rules = ['testError' => 'ip', 'testValid' => 'ip'];

        $validator = new Validator();
        $validator->set($array, $rules);
        $this->assertCount(1, $validator->getErros());
    }

    public function testFloat(): void
    {
        $array = ['testError' => 'a1', 'testValid' => '10.125'];
        $rules = ['testError' => 'float', 'testValid' => 'float'];

        $validator = new Validator();
        $validator->set($array, $rules);
        $this->assertCount(1, $validator->getErros());
    }

    public function testHour(): void
    {
        $array = ['testError' => '24:03', 'testValid' => '21:03'];
        $rules = ['testError' => '{"type":"hour"}', 'testValid' => '{"type":"hour"}'];

        $validator = new Validator();
        $validator->set($array, $rules);
        $this->assertCount(1, $validator->getErros());
    }

    public function testLower(): void
    {
        $array = ['testError' => 'Abcdção', 'testValid' => 'abcdção'];
        $rules = ['testError' => '{"type":"lower"}', 'testValid' => '{"type":"lower"}'];

        $validator = new Validator();
        $validator->set($array, $rules);
        $this->assertCount(1, $validator->getErros());
    }

    public function testMac(): void
    {
        $array = ['testError' => '00:00', 'testValid' => '00-D0-56-F2-B5-12'];
        $rules = ['testError' => 'mac', 'testValid' => 'mac'];

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

    public function testNoWeekend(): void
    {
        $array = ['testError' => '10/10/2020', 'testValid' => '16/10/2020'];
        $rules = ['testError' => 'noWeekend', 'testValid' => 'noWeekend'];

        $validator = new Validator();
        $validator->set($array, $rules);
        $this->assertCount(1, $validator->getErros());
    }

    public function testNumeric(): void
    {
        $array = ['testError' => 'a', 'testValid' => 123];
        $rules = ['testError' => 'numeric', 'testValid' => 'numeric'];

        $validator = new Validator();
        $validator->set($array, $rules);
        $this->assertCount(1, $validator->getErros());
    }

    public function testNumMax(): void
    {
        $array = ['testError' => 32, 'testValid' => 31];
        $rules = ['testError' => 'numMax:31', 'testValid' => 'numMax:31'];

        $validator = new Validator();
        $validator->set($array, $rules);
        $this->assertCount(1, $validator->getErros());
    }

    public function testNumMin(): void
    {
        $array = ['testError' => 2, 'testValid' => 8];
        $rules = ['testError' => 'numMin:5', 'testValid' => 'numMin:5'];

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

    public function testParamJson(): void
    {
        $array = [
            'testError' => '@&451',
            'testValid' => 123
        ];
        $rules = [
            'testError' => '{"required":"true","type":"alpha"}',
            'testValid' => '{"required":"true","type":"int"}'
        ];

        $validator = new Validator();
        $validator->set($array, $rules);
        $this->assertCount(1, $validator->getErros());
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

    public function testUpper(): void
    {
        $array = ['testError' => 'AbcDçÃo', 'testValid' => 'ABCDÇÃO'];
        $rules = ['testError' => 'upper', 'testValid' => 'upper'];

        $validator = new Validator();
        $validator->set($array, $rules);
        $this->assertCount(1, $validator->getErros());
    }

    public function testUrl(): void
    {
        $array = ['testError' => 'ww.test.c', 'testValid' => 'https://www.google.com.br'];
        $rules = ['testError' => 'url', 'testValid' => 'url'];

        $validator = new Validator();
        $validator->set($array, $rules);
        $this->assertCount(1, $validator->getErros());
    }

    public function testZipcode(): void
    {
        $array = ['testError' => '870475', 'testValid' => '87047510'];
        $rules = ['testError' => 'zipcode', 'testValid' => 'zipcode'];

        $validator = new Validator();
        $validator->set($array, $rules);
        $this->assertCount(1, $validator->getErros());
    }

    public function testCustomMessage(): void
    {
        $msg = 'Mensagem customizada aqui!';
        $array = [
            'textoError' => 'abc',
            'textoValid' => 'abcde'
        ];
        $rules = [
            'textoError' => 'required|min:5, ' . $msg . '|max:20',
            'textoValid' => 'required|min:5, ' . $msg . '|max:20'
        ];

        $validator = new Validator();
        $validator->set($array, $rules);

        $this->assertCount(1, $validator->getErros());
        $this->assertEquals($msg, $validator->getErros()['textoError']);
    }

    public function testValidateSpace(): void
    {
        $array = ['validarEspacoError' => 'BRU C', 'validarEspacoValid' => 'BRUC'];
        $rules = ['validarEspacoError' => 'notSpace', 'validarEspacoValid' => 'notSpace'];

        $validator = new Validator();
        $validator->set($array, $rules);

        $this->assertCount(1, $validator->getErros());
    }

    public function testValidateJson(): void
    {
        $array = ['validaJsonError' => '"nome": "Bruno"}', 'validaJsonValid' => '{"nome": "Bruno"}'];
        $rules = ['validaJsonError' => 'type:json', 'validaJsonValid' => 'type:json'];

        $validator = new Validator();
        $validator->set($array, $rules);

        $this->assertCount(1, $validator->getErros());
    }

    public function testValidateNumMonth(): void
    {
        $array = ['validaMesError' => 13, 'validaMesValid' => 10];
        $rules = ['validaMesError' => 'numMonth', 'validaMesValid' => 'numMonth'];

        $validator = new Validator();
        $validator->set($array, $rules);

        $this->assertCount(1, $validator->getErros());
    }

    public function testValidateIdentifierOrCompany(): void
    {
        $array = ['cpfOuCnpnError' => '96.284.092.0001/59', 'cpfOuCnpnValid' => '96.284.092/0001-58'];
        $rules = ['cpfOuCnpnError' => 'identifierOrCompany', 'cpfOuCnpnValid' => 'identifierOrCompany'];

        $validator = new Validator();
        $validator->set($array, $rules);

        $this->assertCount(1, $validator->getErros());
    }
}
