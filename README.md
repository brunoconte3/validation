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

``` php
- required              // Campo obrigatório
- min                   // Tamanho mínimo
- max                   // Tamanho máximo
- bool                  // Tipo lógico
- email                 // E-mail válido
- float                 // Tipo flutuante(valor real)
- int                   // Tipo inteiro
- ip                    // Endereço de IP válido
- mac                   // Endereço de MAC válido
- regex                 // Define uma regra através de uma expressão regular
- url                   // Endereço de URL válida
```

# Licença

O validator é uma aplicação open-source licenciado sob a [licença MIT](https://opensource.org/licenses/MIT).
