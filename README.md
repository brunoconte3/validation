# Validation

O Validator é uma classe autônoma de validação de dados do PHP que torna a validação de qualquer dado fácil.

###### Instalando via composer

``` json
{
    "require": {
        "creedphp/validation": "1.1"
    }
}
```

Em seguida, abra o terminal no diretório do projeto e execute:

``` 
composer install
```

###### Instalando via terminal 

```
composer require creedphp/validation: 1.0
```
Antes abra o terminal no diretório do projeto e execute o comando acima. 


# Chamando a classe Validator

``` php
use Creed\Validation\Validator;
```

# Exemplo de Validação dos dados

###### Dados

``` php
$dados = [
   'nome'    => 'Mauricio',
   'email'   => 'mauricio.msp@mail.com',
   'senha'   => '123456',
   'c_senha' => '12345678'
];
```

###### Regras (rules)

``` php
$rules = [
   'nome'    => 'required|alpha|max_len:30',
   'email'   => 'required|email|max_len:30',
   'senha'   => 'required|min_len:4|max_len:12',
   'c_senha' => 'required|equals:senha'
];
 ```
 
 ###### Validando os dados de acordo com as regras
 
 ``` php
   $validando = Validator::make($dados, $rules);

   //Verificando a validação
   if(!is_array($validando)):
      echo 'Dados validados com sucesso!!!';
   else:
      echo implode($validando, '<br>');
   endif; 
 ```
 
 ***
 
 # Realize você mesmo um teste
 
 ###### index.php
 
 ``` php
 <?php
 
  require 'vendor/autoload.php';
 
  use Creed\Validation\Validator;
 
  //Dados
  $dados = [
     'nome'    => 'Mauricio',
     'email'   => 'mauricio.msp@mail.com',
     'senha'   => '123456',
     'c_senha' => '12345678'
  ];

  //Regras
  $rules = [
     'nome'    => 'required|alpha|max_len:30',
     'email'   => 'required|email|max_len:30',
     'senha'   => 'required|min_len:4|max_len:12',
     'c_senha' => 'required|equals:senha'
  ];

  //Validando os Dados
  $validando = Validator::make($dados, $rules);

  //Verificando a validação
  if(!is_array($validando)):
    echo 'Dados validados com sucesso!!!';
  else:
    echo implode($validando, '<br>');
  endif; 
```

***

# Tipos de validação

``` php
- is_required              // Campo obrigatório
- is_required_file         // Campo do tipo arquivo obrigatório
- is_extension             // Tipos de extensões
- is_min_len               // Tamanho mínimo
- is_max_len               // Tamanho máximo
- is_min_value             // Valor mínimo
- is_max_value             // Valor máximo
- is_email                 // E-mail
- is_url                   // Url
- is_numeric               // Tipo numérico
- is_integer               // Tipo inteiro
- is_float                 // Tipo flutuante(valor real)
- is_string                // Tipo string
- is_boolean               // Tipo lógico
- is_equals                // Valor equivalente
- is_not_equals            // Valor não equivalente
- is_date                  // Data
- is_alpha                 // Valores apenas alfabéticos 
- is_alpha_num             // Valores alfanuméricos
- is_phone                 // Telefone
- is_ip                    // IP
- is_ipv4                  // IPV4
- is_ipv6                  // IPV6
- is_zip_code              // CEP
- is_plate                 // Placa de carro
