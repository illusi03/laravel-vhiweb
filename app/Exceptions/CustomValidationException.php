<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Validators\ValidationException as MaatwebsiteValidatorException;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Spatie\QueryBuilder\Exceptions\InvalidFieldQuery;
use Spatie\QueryBuilder\Exceptions\InvalidFilterQuery;
use Spatie\QueryBuilder\Exceptions\InvalidIncludeQuery;
use Spatie\QueryBuilder\Exceptions\InvalidSortQuery;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CustomValidationException extends Exception
{
    private $fieldName = null;
    private $errorMessage = null;

    /**
     * Create a new exception instance.
     *
     * @return void
     */
    public function __construct($fieldName, $errorMessage)
    {
        parent::__construct($errorMessage, '1');
        $this->fieldName = $fieldName;
        $this->errorMessage = $errorMessage;
    }

    public function getDataResponse()
    {
        return [
            'field' => $this->fieldName,
            'message' => $this->errorMessage
        ];
    }
}
