<?php 

class ErrorHandler {

    public static function errors(int $no, string $msg, string $file, string $line) {

        throw new ErrorException($msg, 0, $no, $file, $line);

    }

    public static function exceptions($error) {

        $message = $error->getMessage();
        $code    = $error->getCode();
        $file    = $error->getFile();
        $line    = $error->getLine();

        echo "<strong>Error Message:</strong> {$message}<br>";
        echo "<strong>Error Code:</strong> {$code}<br>";
        echo "<strong>Error File:</strong> {$file}<br>";
        echo "<strong>Error Line:</strong> {$line}<br>";
        exit;

    }

}