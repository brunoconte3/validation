<?php

declare(strict_types=1);

namespace brunoconte3\Test;

use brunoconte3\Validation\Arrays;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertTrue;

class UnitTestArray extends TestCase
{
    public function testSearchKey(): void
    {
        $array = ['primeiro' => 15, 'segundo' => 25];
        $this->assertIsInt(Arrays::searchKey($array, 'primeiro'));
        $this->assertNull(Arrays::searchKey($array, 'nao-existe'));
    }

    public function testRenameKey(): void
    {
        $array = ['primeiro' => 10, 'segundo' => 20];
        $this->assertTrue(Arrays::renameKey($array, 'primeiro', 'novoNome'));
        $this->assertFalse(Arrays::renameKey($array, 'nao-existe', 'novoNome'));
    }

    public function testCheckExistIndexByValue(): void
    {
        $array = [
            'frutas' => [
                'fruta_1' => 'Maçã', 'fruta_2' => 'Pêra', 'fruta_3' => 'fruta', 'fruta_4' => 'Uva'
            ],
            'verduras' => ['verdura_1' => 'Rúcula', 'verdura_2' => 'Acelga', 'verdura_3' => 'Alface'],
            'legume' => 'Tomate'
        ];
        $this->assertTrue(Arrays::checkExistIndexByValue($array, 'Tomate'));
        $this->assertFalse(Arrays::checkExistIndexByValue($array, 'nao-existe'));
    }

    public function testFindValueByKey(): void
    {
        $array = [
            'frutas' => [
                'fruta_1' => 'Maçã', 'fruta_2' => 'Pêra', 'fruta_3' => 'fruta', 'fruta_4' => 'Uva'
            ],
            'verduras' => ['verdura_1' => 'Rúcula', 'verdura_2' => 'Acelga', 'verdura_3' => 'Alface'],
            'legume' => 'Tomate'
        ];
        $this->assertIsArray(Arrays::findValueByKey($array, 'fruta_2'));
    }

    public function testFindIndexByValue()
    {
        $array = [
            'frutas' => [
                'fruta_1' => 'Maçã', 'fruta_2' => 'Pêra', 'fruta_3' => 'fruta', 'fruta_4' => 'Uva'
            ],
            'verduras' => ['verdura_1' => 'Rúcula', 'verdura_2' => 'Acelga', 'verdura_3' => 'Alface'],
            'legume' => 'Tomate'
        ];
        $this->assertIsArray(Arrays::findValueByKey($array, 'Rúcula'));
    }
}
