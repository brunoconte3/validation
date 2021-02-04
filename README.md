# Validator

Permite validar vários tipos de dados.
Aplicado padrão das PSR.

# Adicionais

- Classe de Arrays
- Classe de Comparação
- Classe de Formatação
- Classe de Testes Unitários

# Instalação

via composer.json

```
"brunoconte3/validation": "4.28.0"
```

via composer.

```
$ composer require brunoconte3/validation
```

# Exemplo de Validação dos dados

`Dados`

```php
$datas = [
   'nome'  => 'brunoconte3',
   'email' => 'brunoconte3@gmail.com',
   'validarPassandoJson' => '@&451',
];
```

`Regras`

```php
$rules = [
   'nome'  => 'required|regex:/^[a-zA-Z\s]+$/',
   'email' => 'required|email|max:50',
   'validarPassandoJson' => '{"required":"true","type":"alpha"}',
];
```

`Validando os dados de acordo com as regras`

```php
  $validator = new brunoconte3\Validation\Validator();

  $validator->set($datas, $rules);

  //Verificando a validação
  if(!$validator->getErros()){
       echo 'Dados válidados com sucesso!';
   } else {
       var_dump($validator->getErros());
   }
```

# Usando

```php
<?php

require 'vendor/autoload.php';

use brunoconte3\Validation\Validator;

 $datas = [
    'sexo' => '',
    'telefone' => '44999696',
    'cpf' => '12547845874',
    'cnpj' => '34060696000163',
    'cnpjException' => '00000000000000',
    'nome' => 'a',
    'numero' => 12345678,
    'email' => 'bruno.com',
    'msgCustom' => 'abc',
    'validarPassandoJson' => '@&451',
    'tratandoTipoInt' => '12',
    'tratandoTipoFloat' => '9.63',
    'tratandoTipoBoolean' => 'true',
    'tratandoTipoNumeric' => '11',
    'validarValores' => 'SA',
    'validaJson' => '{
        "nome": "Bruno"
    }',
    'placaVeiculo' => 'AXI-3668'
];

/**
 * Validação 'CompanyIdentification'
 * Para passar um array com a(s) exceção(ões) permitida(s), basta usar o delimitador ';' entre os valores.
 * São permitidos números iguais de 0 até 9, conforme exemplos abaixo.
 *
 * 'CompanyIdentification:00000000000000' -> Será aceito CNPJ 00.000.000/0000-00
 * 'CompanyIdentification:00000000000000;11111111111111;22222222222222' -> Serão aceitos os CNPJ's 00.000.000/0000-00, 11.111.111/1111-11 e 22.222.222/2222-22
 */
 $rules = [
    'sexo' => 'required',
    'telefone' => 'required|phone',
    'cpf' => 'required|identifier',
    'cnpj' => 'CompanyIdentification',
    'cnpjException' => 'CompanyIdentification:00000000000000;11111111111111;22222222222222',
    'nome' => 'required|min:2',
    'numero' => 'max:5',
    'email' => 'email',
    'msgCustom' => 'required|min:5, Mensagem customizada aqui!|max:20',
    'validarPassandoJson' => '{"required":"true","type":"alpha"}',
    'tratandoTipoInt' => 'convert|int',
    'tratandoTipoFloat' => 'float|convert',
    'tratandoTipoBoolean' => 'convert|bool',
    'tratandoTipoNumeric' => 'numeric|convert',
    'validarValores' => 'arrayValues:S-N-T',  //Opções aceitas [S,N,T]
    'validaJson' => 'type:json',
    'placaVeiculo' => 'plate'
];

$validator = new Validator();
Format::convertTypes($datas, $rules);
$validator->set($datas, $rules);

if (!$validator->getErros()) {
    echo 'Dados válidados com sucesso!';
} else {
    echo '<pre>';
    print_r($validator->getErros());
}
```

# Validando Upload de Arquivo(s)

Com os validadores minUploadSize, maxUploadSize, mimeType e fileName, será possível definir o tamanho (bytes) mínimo e máximo do arquivo; extensões permitidas e validar o nome do arquivo.

`Exemplo:`

```html
<!DOCTYPE html>
<html lang="pt-BR">
  <head>
    ...
  </head>
  <body>
    <form method="POST" enctype="multipart/form-data">
      <!-- Upload de um único arquivo. -->
      <input type="file" name="fileUploadSingle" />

      <!-- Upload de um ou múltiplos arquivos. -->
      <input type="file" name="fileUploadMultiple[]" multiple="multiple" />

      <button type="submit">Upload</button>
    </form>
  </body>
</html>
```

```php
<?php
    /**
     * Observações
     *
     * maxFile: Deve ser um valor do tipo inteiro.
     * minFile: Deve ser um valor do tipo inteiro.
     * maxUploadSize: Deve ser um valor do tipo inteiro.
     * minUploadSize: Deve ser um valor do tipo inteiro.
     * mimeType: Para passar um array com as extensões permitidas, basta utilizar o delimitador ';' entre os valores.
     */
    if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'POST') {
        $fileUploadSingle = $_FILES['fileUploadSingle'];
        $fileUploadMultiple = $_FILES['fileUploadMultiple'];

        $datas = [
            'fileUploadSingle' => $fileUploadSingle,
            'fileUploadMultiple' => $fileUploadMultiple,
        ];

        $rules = [
            'fileUploadSingle' => 'requiredFile|fileName|mimeType:jpeg;png;jpg;txt;docx;xlsx;pdf|minUploadSize:10|maxUploadSize:100',
            'fileUploadMultiple' => 'fileName|mimeType:jpeg|minFile:1|maxFile:3|minUploadSize:10|maxUploadSize:100, Mensagem personalizada aqui!',
        ];

        $validator = new Validator();
        Format::convertTypes($datas, $rules);
        $validator->set($datas, $rules);

        if (!$validator->getErros()) {
            echo 'Dados válidados com sucesso!';
        } else {
            echo '<pre>';
            print_r($validator->getErros());
        }
    }
```

# Tipos de validação (validators)

- alpha: `Verifica se o campo contém somentes caracteres alfabéticos.`
- alphaNoSpecial: `Verifica se o campo contém caracteres texto regular, não pode ter ascentos.`
- alphaNum: `Verifica se o campo contém caracteres alfanuméricos.`
- alphaNumNoSpecial: `Verifica se o campo contém letras sem ascentos, números, não pode carácter especial.`
- array: `Verifica se a variável é um array.`
- arrayValues: `Verifica se a variável possui uma das opções do array especificado.`
- bool: `Valores do tipo lógico.` `Ex: true ou false, 1 ou 0, yes ou no.`
- companyIdentification: `Valida se o CNPJ é válido, passando CNPJ com ou sem mascara`
- dateAmerican: `Valida se a data americana é valida.`
- dateBrazil: `Valida se a data brasileira é valida.`
- email: `Verifica se é um email válido.`
- fileName: `Verifica se o nome do arquivo contém caracteres regular, não pode ter ascentos.`
- float: `Verifica se o valor é do tipo flutuante(valor real).`
- hour: `Valida se a hora é valida.`
- identifier: `Valida se o CPF é válido, passando CPF com ou sem mascara`
- identifierOrCompany: `Valida se o CPF ou CNPJ é válido, passando CPF ou CNPJ com ou sem mascara`
- int: `Verifica se o valor é do tipo inteiro.`
- ip: `Verifica se o valor é um endereço de IP válido.`
- json: `Verifica se o valor é um json válido.`
- lower: `Verifica se todos os caracteres são minúsculos.`
- mac: `Verifica se o valor é um endereço de MAC válido.`
- max: `Define o tamanho máximo do valor.`
- maxFile: `Define a quantidade máxima de arquivos para upload.`
- maxUploadSize: `Define o tamanho (bytes) máximo do arquivo.`
- min: `Define o tamanho mínimo do valor.`
- minFile: `Define a quantidade mínima de arquivos para upload.`
- mimeType: `Define a(s) extensão(ões) permitida(s) para upload.`
- minUploadSize: `Define o tamanho (bytes) mínimo do arquivo.`
- numeric: `Verifica se o valor contém apenas valores numéricos (Aceita zero a esquerda).`
- numMax: `Define um valor máximo.`
- numMin: `Define um valor mínimo.`
- numMonth: `Verifica se o valor é um mês válido (1 a 12).`
- notSpace: `Verifica se a string contém espaços.`
- noWeekend: `Verifica se a data (Brasileira ou Americada não é um Final de Semana).`
- optional: `Se inserido, só valida se o valor vier diferente de vazio, null ou false.`
- phone: `Verifica se o valor corresponde a um telefone válido. (DDD + NÚMEROS) 10 ou 11 dígitos`
- plate: `Verifica se o valor corresponde ao formato de uma placa de carro.`
- regex: `Define uma regra para o valor através de uma expressão regular.`
- required: `Define o campo como obrigatório.`
- requiredFile: `Define o campo do tipo 'File', como obrigatório.`
- upper: `Verifica se todos os caracteres são maiúsculas.`
- url: `Verifica se o valor é um endereço de URL válida.`
- zipCode: `Verifica se o valor corresponde ao formato de um CEP.`

# Definindo mensagem personalizada

Após definir algumas de nossas regras aos dados você também pode adicionar uma mensagem personalizada usando o delimitador ',' em alguma regra específica ou usar a mensagem padrão.

`Exemplo:`

```php
<?php

    $validator->set($datas, [
        'nome'  => 'required, O campo nome não pode ser vazio.',
        'email' => 'email, O campo email esta incorreto.|max:50',
        'senha' => 'min:8, no mínimo 8 caracteres.|max:12, no máximo 12 caracteres.',
    ]);
```

# Formatação Exemplos

```php
<?php

require 'vendor/autoload.php';

use brunoconte3\Validation\Format;

echo Format::companyIdentification('39678379000129') . '<br>'; //CNPJ ==> 39.678.379/0001-29
Format::convertTypes($datas, $rules); //Converte o valor para o tipo correto dele ['bool', 'float', 'int', 'numeric',]
echo Format::currency('1123.45') . '<br>'; //Moeda padrão BR ==>  1.123,45
echo Format::currencyUsd('1123.45') . '<br>'; //Moeda padrão USD ==> 1,123.45
echo Format::dateAmerican('12-05-2020') . '<br>'; //Data ==>  2020-05-12
echo Format::emptyToNull(['test' => 'null']) . '<br>'; //['test' => null]
echo Format::dateBrazil('2020-05-12') . '<br>'; //Data ==>  12/05/2020
echo Format::identifier('73381209000') . '<br>';  //CPF ==>  733.812.090-00
echo Format::identifierOrCompany('30720870089') . '<br>'; //CPF/CNPJ ==> 307.208.700-89
echo Format::falseToNull(false) . '<br>'; //Retorna ==> null
echo Format::lower('CArrO') . '<br>'; //Minusculo ==> carro - o segundo parametro escolhe o charset, UTF-8 default
//[Aplicar qualquer tipo de Mascara, aceita espaço, pontos e outros]
echo Format::mask('#### #### #### ####', '1234567890123456') . '<br>'; //Mascara ==> 1234 5678 9012 3456
echo Format::maskStringHidden('065.775.009.96', 3, 4, '*'); //Marcarar uma string ==> 065.***.009.96
echo Format::onlyNumbers('548Abc87@') . '<br>'; //Retorna apenas números ==> 54887;
echo Format::onlyLettersNumbers('548Abc87@') . '<br>'; //Retorna apenas letras e números ==> 548Abc87;
echo Format::pointOnlyValue('1.350,45') . '<br>'; //Moeda para gravação no BD ==>  1350.45
echo Format::removeAccent('Açafrão') . '<br>'; //Remove acentos e o caracter 'ç' ==> Acafrao
echo Format::returnPhoneOrAreaCode('44999998888', false) . '<br>'; //Retorna apenas o número do telefone ==> 999998888
echo Format::returnPhoneOrAreaCode('44999998888', true) . '<br>'; //Retorna apenas o DDD do telefone ==> 44
echo Format::reverse('Abacaxi') . '<br>'; //Retorna string invertida ==> ixacabA
echo Format::telephone('44999998888') . '<br>';  //Telefone ==> (44) 99999-8888
echo Format::ucwordsCharset('aÇafrÃo maCaRRão') . '<br>'; //Primeira letra maiuscula ==> Açafrão Macarrão
echo Format::upper('Moto') . '<br>'; //Mauiusculo ==> MOTO - o segundo parametro escolhe o charset, UTF-8 default
echo Format::zipCode('87030585') . '<br>'; //CEP ==>  87030-585
echo Format::writeDateExtensive('06/11/2020') . '<br>'; //Data por Extenso ==> sexta-feira, 06 de novembro de 2020
echo Format::writeCurrencyExtensive(1.97) . '<br>'; //Moeda por Extenso ==> um real e noventa e sete centavos

$array = [
    0 => '1',
    1 => '123',
    'a' => '222',
    'b' => 333,
    'c' => '',
];

$arrayComNull = Format::emptyToNull($array); //Converte vazio para null
[
  0 => 1,
  1 => 123,
  'a' => 222,
  'b' => 333,
  'c' => null,
]

//$value = Format::arrayToInt($array); ==> Opção para sem ser por Referencia
Format::arrayToIntReference($array); //Formata valores do array em inteiro ==>
[
  0 => 1,
  1 => 123,
  'a' => 222,
  'b' => 333,
  'c' => 0,
]

```

# Formatação Upload de Arquivo(s)

`Exemplo: Upload de um único arquivo.`

```php
<?php

$fileUploadSingle = [
    'name' => 'JPG - Validação upload v.1.jpg',
    'type' => 'image/jpeg',
    'tmp_name' => '/tmp/phpODnLGo',
    'error' => 0,
    'size' => 8488,
];

Format::restructFileArray($fileUploadSingle); // Chamada do método responsável por normalizar o array.
[
    0 => [
        'name' => 'jpg___validacao_upload_v_1.jpg',
        'type' => 'image/jpeg',
        'tmp_name' => '/tmp/phpBmqX1i',
        'error' => 0,
        'size' => 8488,
        'name_upload' => '22-01-2021_13_1830117018768373446425980271611322393600ad419619ec_jpg___validacao_upload_v_1.jpg',
    ]
]

```

`Exemplo: Upload de múltiplos arquivos.`

```php
<?php

$fileUploadMultiple = [
	'name' => [
		'0' => 'JPG - Validação upload v.1.jpg',
		'1' => 'PDF - Validação upload v.1.pdf',
		'2' => 'PNG - Validação upload v.1.png',
	],
	'type' => [
		'0' => 'image/jpeg',
		'1' => 'application/pdf',
		'2' => 'image/png',
	],
	'tmp_name' => [
		'0' => '/tmp/phpODnLGo',
		'1' => '/tmp/phpfmb0tL',
		'2' => '/tmp/phpnoejk8',
	],
	'error' => [
		'0' => 0,
		'1' => 0,
		'2' => 0,
	],
	'size' => [
		'0' => 8488,
		'1' => 818465,
		'2' => 1581312,
	],
];

Format::restructFileArray($fileUploadMultiple); // Chamada do método responsável por normalizar o array.
[
	0 => [
		'name' => 'jpg___validacao_upload_v_1.jpg',
		'type' => 'image/jpeg',
		'tmp_name' => '/tmp/phpBmqX1i',
		'error' => 0,
		'size' => 8488,
		'name_upload' => '22-01-2021_13_1830117018768373446425980271611322393600ad419619ec_jpg___validacao_upload_v_1.jpg',
	],
	1 => [
		'name' => 'pdf___validacao_upload_v_1.pdf',
		'type' => 'application/pdf',
		'tmp_name' => '/tmp/phpYo0w7c',
		'error' => 0,
		'size' => 818465,
		'name_upload' => '22-01-2021_13_170624609160164419213582611971611322393600ad41961a5a_pdf___validacao_upload_v_1.pdf',
	],
	2 => [
		'name' => 'png___validacao_upload_v_1.png',
		'type' => 'image/png',
		'tmp_name' => '/tmp/phpme7Yf7',
		'error' => 0,
		'size' => 1581312,
		'name_upload' => '22-01-2021_13_8675237129330338531328755051611322393600ad41961ac8_png___validacao_upload_v_1.png',
	],
]

```

# Comparações Exemplos

```php
<?php

require 'vendor/autoload.php';

use brunoconte3\Validation\Compare;

//Retorna +30 (+30 dias de diferença)
echo Compare::daysDifferenceBetweenData('31/05/2020', '30/06/2020') . '<br>'; //Aceita data Americana também

//Compara se a data inicial é menor que a data final => Retorna [bool]
echo Compare::startDateLessThanEnd('30/07/2020', '30/06/2020') . '<br>'; //Aceita data Americana também

//Diferença entre horas ==> 01:36:28 [Horas exibe negativo e positivo a diferença]
echo Compare::differenceBetweenHours('10:41:55', '12:18:23') . '<br>';

//Compara se a hora inicial é menor que a hora final (3º parâmetro, aceita mensagem customizada)
echo Compare::startHourLessThanEnd('12:05:01', '10:20:01') . '<br>';

//Compara a data com a data atual, e retorna a idade da pessoa
echo Compare::calculateAgeInYears('20/05/1989');

//Compara igualdade dos campos, retorna booleano;
//terceiro parametro opcional, false para não comparar caseSensitive, default true
var_dump(Compare::checkDataEquality('AçaFrão', 'Açafrão'));

//Compara se o conteudo desejado existe na String, retorna booleano
var_dump(Compare::contains('AçaFrão', 'çaF'));

```

# Manipular Arrays

```php
<?php

require 'vendor/autoload.php';

use brunoconte3\Validation\Array;

echo '<br><br>Manipular Arrays<hr>';

$array = ['primeiro' => 15, 'segundo' => 25];
var_dump(Arrays::searchKey($array, 'primeiro'));   // Procura chave no array, e retorna a posição ==> returns 0
var_dump(Arrays::searchKey($array, 'segundo'));    // Procura chave no array, e retorna a posição ==> returns 1
var_dump(Arrays::searchKey($array, 'nao-existe')); // Procura chave no array, e retorna a posição ==> returns null

$array = ['primeiro' => 10, 'segundo' => 20];
Arrays::renameKey($array, 'primeiro', 'novoNome');
var_dump($array); //Renomeia a chave do array ==> ['novoNome' => 10, 'segundo' => 20];

$array = [
    'frutas' => ['fruta_1' => 'Maçã', 'fruta_2' => 'Pêra', 'fruta_3' => 'fruta', 'fruta_4' => 'Uva'],
    'verduras' => ['verdura_1' => 'Rúcula', 'verdura_2' => 'Acelga', 'verdura_3' => 'Alface'],
    'legume' => 'Tomate'
];

// Verifica no array, se existe algum índice com o valor desejado
var_dump(Arrays::checkExistIndexByValue($array, 'Tomate'));

// Realiza a busca no array, através da key e retorna um array com todos índices localizados
var_dump(Arrays::findValueByKey($array, 'verduras'));

// Realiza a busca no array, através de um valor e rotorna um array com todos itens localizados
var_dump(Arrays::findIndexByValue($array, 'Tomate'));

$xml = new SimpleXMLElement('<root/>');
Arrays::convertArrayToXml($array, $xml); // Converte array em Xml
var_dump($xml->asXML());

$array = [
    'frutas' => ['fruta_1' => 'Maçã', 'fruta_2' => 'Pêra', 'fruta_3' => 'fruta', 'fruta_4' => 'Uva'],
    'verduras' => '{"verdura_1": "Rúcula", "verdura_2": "Acelga", "verdura_3": "Alface"}'
];

// Verifica no array, se possui algum índice com JSON e o transforma em array
Arrays::convertJsonIndexToArray($array);
var_dump($array);

$array = [
            'pessoa' => [
                'pedidos' => ['pedido1', 'pedido2'],
                'categorias' => [
                    'subcategorias' => [
                        'subcategoria1' => 'valor teste'
                    ]
                ]
            ]
        ];

// Verifica se existe um índice específico em um array multinível
var_dump(Arrays::checkExistIndexArrayRecursive($array, 'subcategoria1')); // Retorna true

```

# Utilidades

```php
<?php

require 'vendor/autoload.php';

use brunoconte3\Validation\Utility;

echo Utility::captureClientIp(); //Retorna o IP do usuário, captura por camada disponível, Ex: 201.200.25.40

```

# Arquivo com exemplos de Testes

- /Test/UnitTest.php deixamos um arquivo com testes unitários para facilitar nosso controle, fique a vontade em rodar!

# Licença

O validator é uma aplicação open-source licenciado sob a [licença MIT](https://opensource.org/licenses/MIT).

`Créditos a mammoth-php/validation que foi fork de lá`
