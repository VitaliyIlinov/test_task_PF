<?php

namespace app\Exceptions;


use Throwable;

class MethodNotAllowedHttpException extends \Exception
{
    public function __construct(string $file, int $code = 400, Throwable $previous = null)
    {
        $message = "Method not found: {$file} in file:{$this->getFile()} Line: {$this->getLine()}";
        parent::__construct($message, $code, $previous);
    }

}