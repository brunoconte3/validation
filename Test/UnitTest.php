<?php

declare(strict_types=1);

namespace brunoconte3\Test;

use brunoconte3\Validation\Format;
use PHPUnit\Framework\TestCase;

class UnitTest extends TestCase
{
    /**
     * Formatações
     */
    public function testCompanyIdentification(): void
    {
        $this->assertEquals('76.027.484/0001-24', Format::companyIdentification('76027484000124'));
    }

    public function testIdentifier(): void
    {
        $this->assertEquals('894.213.600-10', Format::identifier('89421360010'));
    }

    public function testIdentifierOrCompany(): void
    {
        $this->assertEquals('307.208.700-89', Format::identifierOrCompany('30720870089'));
        $this->assertEquals('12.456.571/0001-14', Format::identifierOrCompany('12456571000114'));
    }

    public function testTelephone(): void
    {
        $this->assertEquals('(44) 99999-8888', Format::telephone(44999998888));
    }

    public function testRemoveAccent(): void
    {
        $this->assertEquals('Acafrao', Format::removeAccent('Açafrão'));
    }

    public function testZipCode(): void
    {
        $this->assertEquals('87047-590', Format::zipCode('87047590'));
    }

    public function testDateBrazil(): void
    {
        $this->assertEquals('10/10/2020', Format::dateBrazil('2020-10-10'));
    }

    public function testDateAmerican(): void
    {
        $this->assertEquals('2020-10-10', Format::dateAmerican('10/10/2020'));
    }

    public function testArrayToInt(): void
    {
        $arrayProcessed = [
            0 => 1,
            1 => 123,
            'a' => 222,
            'b' => 333,
            'c' => 0
        ];
        $this->assertEquals($arrayProcessed, Format::arrayToInt([
            0 => '1',
            1 => '123',
            'a' => '222',
            'b' => 333,
            'c' => ''
        ]));
    }

    public function testCurrency(): void
    {
        $this->assertEquals('1.123,45', Format::currency('1123.45'));
    }

    public function testCurrencyUsd(): void
    {
        $this->assertEquals('1,123.45', Format::currencyUsd('1123.45'));
    }

    public function testReturnPhoneOrAreaCode(): void
    {
        $this->assertEquals('44', Format::returnPhoneOrAreaCode('44999998888', true));
        $this->assertEquals('999998888', Format::returnPhoneOrAreaCode('44999998888'));
    }

    public function testUcwordsCharset(): void
    {
        $this->assertEquals('Açafrão Macarrão', Format::ucwordsCharset('aÇafrÃo maCaRRão'));
    }

    public function testPointOnlyValue(): void
    {
        $this->assertEquals('1350.45', Format::pointOnlyValue('1.350,45'));
    }

    public function testEmptyToNull(): void
    {
        $array = [
            0 => '1',
            'a' => '222',
            'b' => 333,
            'c' => null
        ];

        $this->assertEquals($array, Format::emptyToNull([
            0 => '1',
            'a' => '222',
            'b' => 333,
            'c' => ''
        ]));
    }

    public function testMask(): void
    {
        $this->assertEquals('1234 5678 9012 3456', Format::mask('#### #### #### ####', '1234567890123456'));
    }

    public function testOnlyNumbers(): void
    {
        $this->assertEquals('54887', Format::onlyNumbers('548Abc87@'));
    }

    public function testOnlyLettersNumbers(): void
    {
        $this->assertEquals('548Abc87', Format::onlyLettersNumbers('548Abc87@'));
    }

    public function testUpper(): void
    {
        $this->assertEquals('CARRO', Format::upper('CArrO'));
    }

    public function testLower(): void
    {
        $this->assertEquals('carro', Format::lower('CArrO'));
    }

    public function testReverse(): void
    {
        $this->assertEquals('ixacabA', Format::reverse('Abacaxi'));
    }

    public function testFalseToNull(): void
    {
        $this->assertEquals(null, Format::falseToNull(false));
    }

    /**
     * Regras
     */

    /**
     * Comparações
     */
}
