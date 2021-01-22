<?php

declare(strict_types=1);

namespace brunoconte3\Test;

use brunoconte3\Validation\Format;
use brunoconte3\Validation\Validator;

require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>brunoconte3/validation</title>

    <style>
        body {
            padding: 0px;
            margin: 0px;
            background: #F8F9FA;
        }

        .container {
            width: auto;
            max-width: 1280px;
            padding: 15px 15px;
            margin-right: auto;
            margin-left: auto;
        }

        .container>header#body-title-page>h1 {
            margin: 0px;
            text-align: center;
            color: green;
        }

        .container>header#body-title-page>small {
            display: block;
            width: 100%;
            text-align: center;
            margin-bottom: 15px;
        }

        section.body-section-class {
            background-color: white;
            padding: 15px;
            min-height: 400px;
        }

        div#bd-form-upload {
            position: relative;
            display: flex;
            width: 100%;
            flex-direction: row;
        }

        div#bd-form-upload>form {
            display: flex;
            flex-flow: row wrap;
            align-items: stretch;
            width: 100%;
        }

        div#bd-form-upload>form>div {
            display: flex;
            flex-flow: row wrap;
            align-items: center;
            width: 100%;
            padding: 10px;
        }

        div#bd-form-upload>form>div:last-child {
            align-items: flex-end !important;
        }

        div#bd-form-upload>form>div>label {
            display: block;
            margin-bottom: 10px;
            width: 100%;
        }

        div#bd-form-upload>form>div>input[type='file'] {
            width: 100%;
            border: 1px solid #ccc;
        }
    </style>
</head>

<body>
    <div class="container">
        <header id="body-title-page">
            <h1>Brunoconte3/Validation</h1>
            <small>Espaço para fazer seus testes</small>
        </header>

        <section class="body-section-class">
            <div class="item-section-class">
                <div>
                    <?php

                    echo '<p>Aqui vem os seus testes!</p>';

                    $array = ['cpfOuCnpj' => '04764334879'];
                    $rules = ['cpfOuCnpj' => 'identifierOrCompany'];

                    $validator = new Validator();
                    $validator->set($array, $rules);

                    echo '<pre>';
                    print_r($validator->getErros());
                    ?>
                    <hr />
                </div>

                <div>
                    <?php

                    if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'POST') {
                        $fileUploadSingle = $_FILES['fileUploadSingle'];
                        $fileUploadMultiple = $_FILES['fileUploadMultiple'];

                        $array = [
                            'fileUploadSingle' => $fileUploadSingle,
                            'fileUploadMultiple' => $fileUploadMultiple
                        ];

                        $rules = [
                            'fileUploadSingle' => 'fileName|mimeType:jpeg;png;jpg|minUploadSize:10|maxUploadSize:100',
                            'fileUploadMultiple' => 'fileName|mimeType:jpeg|minUploadSize:10|maxUploadSize:100, Msg',
                        ];

                        $validator = new Validator();
                        $validator->set($array, $rules);

                        echo '<pre>';
                        print_r($validator->getErros());

                        echo '<hr>';

                        echo '<pre>';
                        print_r(Format::restructFileArray($fileUploadSingle));
                        print_r(Format::restructFileArray($fileUploadMultiple));
                    }

                    ?>

                    <div id="bd-form-upload">
                        <form method="POST" enctype="multipart/form-data">
                            <!-- Upload de um único arquivo. -->
                            <div>
                                <label for="fileUploadSingle">Upload de um arquivo</label>
                                <input type="file" name="fileUploadSingle" />
                            </div>
                            <!-- Upload de um ou multiplos arquivos. -->
                            <div>
                                <label for="fileUploadSingle">Upload de multiplos arquivo</label>
                                <input type="file" name="fileUploadMultiple[]" multiple="multiple">
                            </div>
                            <div>
                                <button type="submit">Upload</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
</body>

</html>
