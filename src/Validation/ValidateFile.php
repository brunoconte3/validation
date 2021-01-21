<?php

declare(strict_types=1);

namespace brunoconte3\Validation;

class ValidateFile
{
    private static function validateFileNoEmpty(array $file = [])
    {
        $arrayFileError = [];

        if (empty($file) || (count($file) <= 0)) {
            array_push($arrayFileError, 'Arquivo inválido!');
            return $arrayFileError;
        }
    }

    public static function validateFileErrorPhp(array $file = []): array
    {
        self::validateFileNoEmpty($file);

        $phpFileErrors = [
            UPLOAD_ERR_OK         => 'Não houve erro, o arquivo foi enviado com sucesso!',
            UPLOAD_ERR_INI_SIZE   => 'O arquivo enviado excede o limite definido na diretiva upload_max_filesize
                                      do php.ini!',
            UPLOAD_ERR_FORM_SIZE  => 'O arquivo excede o limite em MAX_FILE_SIZE no fomulário HTML!',
            UPLOAD_ERR_PARTIAL    => 'O upload do arquivo foi feito parcialmente!',
            UPLOAD_ERR_NO_FILE    => 'Nenhum arquivo foi enviado!',
            UPLOAD_ERR_NO_TMP_DIR => 'Pasta temporária ausênte!',
            UPLOAD_ERR_CANT_WRITE => 'Falha ao gravar arquivo no disco!',
            UPLOAD_ERR_EXTENSION  => 'Uma extensão PHP interrompeu o upload do arquivo!',
        ];

        $arrayFileError = [];

        foreach ($file['error'] as $key => $codeError) {
            $codeError = 8;
            $file['name'][$key] = 'nomeArquivo';
            if (($codeError > 0) && (array_key_exists($codeError, $phpFileErrors))) {
                $nameFile = empty($file['name'][$key]) ? '' : '[' . $file['name'][$key] . '] - ';
                $message =  $nameFile . $phpFileErrors[$codeError];
                array_push($arrayFileError, $message);
            }
        }
        return $arrayFileError;
    }

    public static function validateMaxUploadSize(int $rule = 0, array $file = []): array
    {
        self::validateFileNoEmpty($file);

        $arrayFileError = [];

        foreach ($file['size'] as $key => $size) {
            if ($size > $rule) {
                $message = 'O arquivo ' . $file['name'][$key] . ' deve conter, no máximo ' . $rule . ' bytes!';
                array_push($arrayFileError, $message);
            }
        }
        return $arrayFileError;
    }

    public static function validateMinUploadSize(int $rule = 0, array $file = []): array
    {
        self::validateFileNoEmpty($file);

        $arrayFileError = [];

        foreach ($file['size'] as $key => $size) {
            if ($size < $rule) {
                $message = 'O arquivo ' . $file['name'][$key] . ' deve conter, no mínimo ' . $rule . ' bytes!';
                array_push($arrayFileError, $message);
            }
        }
        return $arrayFileError;
    }

    public static function validateFileName(array $file = []): array
    {
        self::validateFileNoEmpty($file);

        $arrayFileError = [];

        foreach ($file['name'] as $fileName) {
            $dataName = explode('.', strtolower(trim($fileName)));

            if (preg_match('/\W/', reset($dataName))) {
                $message = "O nome do arquivo {$fileName}, não pode conter caracteres especiais e ascentos!";
                array_push($arrayFileError, $message);
            }
        }
        return $arrayFileError;
    }

    /**
     * @param string|array $rule
     */
    public static function validateMimeType($rule = '', array $file = []): array
    {
        self::validateFileNoEmpty($file);

        $arrayFileError = [];

        foreach ($file['name'] as $fileName) {
            $ext = explode('.', $fileName);
            $message = 'O arquivo ' . $fileName . ', contém uma extensão inválida!';

            if (is_string($rule) && (strtolower(end($ext)) != strtolower($rule))) {
                array_push($arrayFileError, $message);
                continue;
            }

            if (is_array($rule) && (!in_array(end($ext), $rule))) {
                array_push($arrayFileError, $message);
            }
        }
        return $arrayFileError;
    }
}
