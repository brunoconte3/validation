<?php

declare(strict_types=1);

namespace brunoconte3\Validation;

use PhpParser\Node\Stmt\Break_;

class ValidateFile
{
    private static function validateFileTransformSingleToMultiple(array &$file = []): void
    {
        if (isset($file['name']) && !is_array($file['name'])) {
            foreach ($file as $paramFile => $value) {
                $file[$paramFile] = [$value];
            }
        }
    }

    private static function validateFileCount(array $file = []): int
    {
        if ((count($file) > 0) && (isset($file['name']))) {
            switch (is_array($file['name'])) {
                case 1:
                    if ((count($file['name']) == 1) && empty($file['name'][0])) {
                        return (count($file['name']) - 1);
                    }
                    return count($file['name']);
                    break;

                case 0:
                    return (is_string($file['name']) && !empty($file['name'])) ? 1 : 0;
                    break;
            }
        }

        return 0;
    }

    public static function validateFileErrorPhp(array &$file = [], string $message = null): array
    {
        self::validateFileTransformSingleToMultiple($file);

        $phpFileErrors = [
            UPLOAD_ERR_OK         => 'Arquivo enviado com sucesso!',
            UPLOAD_ERR_INI_SIZE   => 'O arquivo enviado excede o limite definido na diretiva UPLOAD_MAX_FILESIZE
                                      do php.ini!',
            UPLOAD_ERR_FORM_SIZE  => 'O arquivo excede o limite definido em MAX_FILE_SIZE, no fomulário HTML!',
            UPLOAD_ERR_PARTIAL    => 'O upload do arquivo, foi realizado parcialmente!',
            UPLOAD_ERR_NO_FILE    => 'Nenhum arquivo foi enviado!',
            UPLOAD_ERR_NO_TMP_DIR => 'Pasta temporária ausênte!',
            UPLOAD_ERR_CANT_WRITE => 'Falha ao gravar arquivo no disco!',
            UPLOAD_ERR_EXTENSION  => 'Uma extensão PHP interrompeu o upload do arquivo!',
        ];

        $arrayFileError = [];

        foreach ($file['error'] as $key => $codeError) {
            if (($codeError > 0) && (array_key_exists($codeError, $phpFileErrors))) {
                $nameFile = empty($file['name'][$key]) ? '' : '[' . $file['name'][$key] . '] - ';
                $message = (!empty($message)) ? $nameFile . $message : $nameFile . $phpFileErrors[$codeError];

                array_push($arrayFileError, $message);
            }
        }
        return $arrayFileError;
    }

    public static function validateMaxUploadSize(
        int $rule = 0,
        array $file = [],
        string $message = null
    ): array {
        self::validateFileTransformSingleToMultiple($file);

        $arrayFileError = [];

        foreach ($file['size'] as $key => $size) {
            if ($size > $rule) {
                $messageMaxSize = 'O arquivo ' . $file['name'][$key] . ' deve conter, no máximo ' . $rule . ' bytes!';
                $messageMaxSize = (!empty($message)) ? $message : $messageMaxSize;

                array_push($arrayFileError, $messageMaxSize);
            }
        }
        return $arrayFileError;
    }

    public static function validateMinUploadSize(
        int $rule = 0,
        array $file = [],
        string $message = null
    ): array {
        self::validateFileTransformSingleToMultiple($file);

        $arrayFileError = [];

        foreach ($file['size'] as $key => $size) {
            if ($size < $rule) {
                $messageMinSize = 'O arquivo ' . $file['name'][$key] . ' deve conter, no máximo ' . $rule . ' bytes!';
                $messageMinSize = (!empty($message)) ? $message : $messageMinSize;

                array_push($arrayFileError, $messageMinSize);
            }
        }
        return $arrayFileError;
    }

    public static function validateFileName(array $file = [], string $message = null): array
    {
        self::validateFileTransformSingleToMultiple($file);

        $arrayFileError = [];

        foreach ($file['name'] as $fileName) {
            $dataName = explode('.', strtolower(trim($fileName)));

            if (preg_match('/\W/', reset($dataName))) {
                $messageFileName = "O nome do arquivo {$fileName}, não pode conter caracteres especiais e ascentos!";
                $messageFileName = (!empty($message)) ? $message : $messageFileName;

                array_push($arrayFileError, $messageFileName);
            }
        }
        return $arrayFileError;
    }

    /**
     * @param string|array $rule
     */
    public static function validateMimeType(
        $rule = '',
        array $file = [],
        string $message = null
    ): array {
        self::validateFileTransformSingleToMultiple($file);

        $arrayFileError = [];
        $rule = (is_array($rule)) ? array_map('trim', $rule) : trim($rule);

        foreach ($file['name'] as $fileName) {
            $ext = explode('.', $fileName);

            $messageMimeType = 'O arquivo ' . $fileName . ', contém uma extensão inválida!';
            $messageMimeType = (!empty($message)) ? $message : $messageMimeType;

            if (is_string($rule) && (strtolower(end($ext)) != strtolower($rule))) {
                array_push($arrayFileError, $messageMimeType);
                continue;
            }

            if (is_array($rule) && (!in_array(end($ext), $rule))) {
                array_push($arrayFileError, $messageMimeType);
            }
        }
        return $arrayFileError;
    }

    public static function validateFileUploadMandatory(
        string $field = '',
        array $file = [],
        $message = null
    ): array {
        $arrayFileError = [];
        $message = (!empty($message)) ? $message : "O campo {$field} é obrigatório!";

        if ((count($file) > 0) && (isset($file['error']))) {
            switch (is_array($file['error'])) {
                case 1:
                    if (isset($file['error'][0]) && ($file['error'][0] === UPLOAD_ERR_NO_FILE)) {
                        array_push($arrayFileError, $message);
                    }
                    break;
                case 0:
                    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
                        array_push($arrayFileError, $message);
                    }
                    break;
            }
        }

        return $arrayFileError;
    }

    public static function validateMaximumFileNumbers(
        $rule = 0,
        $field = '',
        array $file = [],
        $message = null
    ): array {
        $arrayFileError = [];
        $message = (!empty($message)) ? $message : "O campo {$field} deve conter, no máximo {$rule} arquivo(s)!";

        if (validateFile::validateFileCount($file) > $rule) {
            array_push($arrayFileError, $message);
        }

        return $arrayFileError;
    }

    public static function validateMinimumFileNumbers(
        $rule = 0,
        $field = '',
        array $file = [],
        $message = null
    ): array {
        $arrayFileError = [];
        $message = (!empty($message)) ? $message : "O campo {$field} deve conter, no mínimo {$rule} arquivo(s)!";

        if (validateFile::validateFileCount($file) < $rule) {
            array_push($arrayFileError, $message);
        }

        return $arrayFileError;
    }
}
