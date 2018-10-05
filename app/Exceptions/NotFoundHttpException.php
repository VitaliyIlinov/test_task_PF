<?php

namespace app\Exceptions;


use Throwable;

class NotFoundHttpException extends \Exception
{
    public function __construct(string $message = null, int $code = 404, Throwable $previous = null)
    {
        $message = $message ?? "Route doesn't exist. File:{$this->getFile()} Line: {$this->getLine()}";
        parent::__construct($message, $code, $previous);
    }

}