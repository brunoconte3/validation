# Validator

O Validator é uma classe de validação baseada em PHP que permite validar quaisquer dados.

# Instalação

via composer.

```
$ composer require mammoth-php/validation
``` 

# Exemplo de Validação dos dados

###### Dados

``` php
$datas = [
   'nome'  => 'Mauricio',
   'email' => 'mauricio.msp@mail.com',
   'senha' => '123456'
];
```

###### Regras

``` php
$rules = [
   'nome'  => 'required|regex:/^[a-zA-Z]+$/',
   'email' => 'required|email|max:50',
   'senha' => 'required|min:8|max:12'
];
 ```
 
 ###### Validando os dados de acordo com as regras
 
 ``` php
   $validator = new Mammoth\Validation\Validator();

   $validator->set($datas, $rules);
   
   //Verificando a validação
   if(!$validator->getErros()){
        echo 'Dados válidados com sucesso!';
    } else {
        var_dump($validator->getErros());
    }
 ```
 
 # Usando
 
 ``` php
 <?php
 
    require 'vendor/autoload.php';
   
    $validator = new Mammoth\Validation\Validator();
    
    $datas = [
        'nome'  => 'mauricio',
        'email' => 'mauricio.web@gmail.com',
        'senha' => '12345678'
    ];
    
    $validator->set($datas, [
        'nome'  => 'required|regex:/^[a-zA-Z]+$/',
        'email' => 'required|email|max:50',
        'senha' => 'required|min:8|max:12'
    ]);
    
    if(!$validator->getErros()){
        echo 'Dados válidados com sucesso!';
    } else {
        var_dump($validator->getErros());
    }
```

# Tipos de validação

* required:              ` Define o campo como obrigatório. `
* min:                   ` Define o tamanho mínimo do valor. `
* max:                   ` Define o tamanho mínimo do valor. `
* bool:                  ` Valores do tipo lógico. ` `Ex: true ou false, 1 ou 0, yes ou no.`
* email:                 ` Verfica se é um e-mail válido. `
* float:                 ` Define o valor como tipo flutuante(valor real). `
* int:                   ` Define o valor como tipo inteiro. `
* ip:                    ` Verifica se o valor é um endereço de IP válido. `
* mac:                   ` Verifica se o valor é um endereço de MAC válido. `
* regex:                 ` Define uma regra para o valor através de uma expressão regular. `
* url:                   ` Verifica se o valor é um endereço de URL válida. `

# Licença

O validator é uma aplicação open-source licenciado sob a [licença MIT](https://opensource.org/licenses/MIT).
