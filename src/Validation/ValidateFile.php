<?php

declare(strict_types=1);

namespace brunoconte3\Validation;

use brunoconte3\Validation\{
    Format,
};

class ValidateFile
{
    private static function validateFormatFileName(string $fileName = ''): string
    {
        $dataName = explode('.', strtolower(trim($fileName)));
        $ext  = end($dataName);

        if (count($dataName) > 1) {
            unset($dataName[count($dataName) - 1]);
        }

        $dataName = implode('_', $dataName);
        $dataName = preg_replace('/\W/', '_', Format::removeAccent($dataName));

        return "{$dataName}.{$ext}";
    }

    private static function validateGenerateFileName(string $nameFile = ''): string
    {
        return date("d-m-Y_s_") . sha1(uniqid("", true)) . '_' . $nameFile;
    }

    public static function validateRestructFileArray(array $file = []): array
    {
        $arrayFile = [];
        foreach ($file['name'] as $key => $name) {
            $name = self::validateFormatFileName($name);

            $params = [
                'name'     => $name,
                'type'     => $file['type'][$key],
                'tmp_name' => $file['tmp_name'][$key],
                'error'    => $file['error'][$key],
                'size'     => $file['size'][$key],
                'name_upload' => self::validateGenerateFileName($name)
            ];
            array_push($arrayFile, $params);
        }
        return $arrayFile;
    }

    public static function validateFileError(array $file = []): array
    {
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
            if (($codeError > 0) && (array_key_exists($codeError, $phpFileErrors))) {
                $message = $file['name'][$key] . ': ' . $phpFileErrors[$codeError];
                array_push($arrayFileError, $message);
            }
        }
        return $arrayFileError;
    }

    public static function validateMaxUploadSize(int $rule = 0, array $file = []): array
    {
        $file = self::validateRestructFileArray($file);
        $arrayFileError = [];

        foreach ($file as $arquivo) {
            if ($arquivo['size'] > $rule) {
                $message = 'O arquivo ' . $arquivo['name'] . ' deve conter, no máximo ' . $rule . ' bytes!';
                array_push($arrayFileError, $message);
            }
        }
        return $arrayFileError;
    }

    public static function validateMinUploadSize(int $rule = 0, array $file = []): array
    {
        $file = self::validateRestructFileArray($file);
        $arrayFileError = [];

        foreach ($file as $arquivo) {
            if ($arquivo['size'] < $rule) {
                $message = 'O arquivo ' . $arquivo['name'] . ' deve conter, no mínimo ' . $rule . ' bytes!';
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
        $file = self::validateRestructFileArray($file);
        $arrayFileError = [];

        foreach ($file as $arquivo) {
            $ext = explode('.', $arquivo['name']);
            $message = 'O arquivo ' . $arquivo['name'] . ', contém uma extensão inválida!';

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
