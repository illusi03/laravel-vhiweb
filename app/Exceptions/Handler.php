<?php

namespace App\Exceptions;

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

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        // Url Not Found
        if ($exception instanceof NotFoundHttpException) {
            return response()->json([
                'status' => 'error',
                'data' => 'url not found',
            ], 400);
        }
        // Method Not Found
        if ($exception instanceof MethodNotAllowedHttpException) {
            return response()->json([
                'status' => 'error',
                'data' => 'method not found or please check http / https',
            ], 400);
        }
        // Model Not found
        if ($exception instanceof ModelNotFoundException) {
            return response()->json([
                'status' => 'success',
                'data' => (object) [],
            ], 400);
        }
        // Exception SQL
        if ($exception instanceof QueryException) {
            return response()->json([
                'status' => 'error',
                'data' => $exception->errorInfo[2],
            ], 400);
        }
        // Not Authorize
        if ($exception instanceof UnauthorizedException) {
            return response()->json([
                'status' => 'error',
                'data' => 'not authorize permissions',
            ], 401);
        }
        // SPATIE Exception
        // Spatie QueryBuilder -> Query Params is invalid
        if ($exception instanceof InvalidFieldQuery) {
            return response()->json([
                'status' => 'error',
                'data' => [
                    'message' => $exception->getMessage(),
                    'accept_values' => $exception->allowedFields,
                ],
            ], 400);
        }
        if ($exception instanceof InvalidSortQuery) {
            return response()->json([
                'status' => 'error',
                'data' => [
                    'message' => $exception->getMessage(),
                    'accept_values' => $exception->allowedSorts,
                ],
            ], 400);
        }
        if ($exception instanceof InvalidIncludeQuery) {
            return response()->json([
                'status' => 'error',
                'data' => [
                    'message' => $exception->getMessage(),
                    'accept_values' => $exception->allowedIncludes,
                ],
            ], 400);
        }
        if ($exception instanceof InvalidFilterQuery) {
            return response()->json([
                'status' => 'error',
                'data' => [
                    'message' => $exception->getMessage(),
                    'accept_values' => $exception->allowedFilters,
                ],
            ], 400);
        }
        // Message Laravel Import Validator
        if ($exception instanceof MaatwebsiteValidatorException) {
            $tmpErrors = $exception->errors();
            $tmpErrors = array_map(function ($item) {
                return $item[0];
            }, $tmpErrors);
            return response()->json([
                'status' => 'error',
                'data' => [
                    'message' => $tmpErrors,
                ],
            ], 400);
        }
        // Email Not Found (HttpException)
        if ($exception instanceof HttpException) {
            return response()->json([
                'status' => 'error',
                'data' => 'email has not verified or something went wrong on request',
            ], 400);
        }

        return parent::render($request, $exception);
    }

    protected function unauthenticated($request, \Illuminate\Auth\AuthenticationException $exception)
    {
        // Unauthenticated
        return response()->json([
            'status' => 'error',
            'data' => 'token not found / expired',
        ], 401);
    }

    private function transformErrors(ValidationException $exception)
    {
        // Fields Not Complete (VALIDATION) -> Private
        $errors = [];
        foreach ($exception->errors() as $field => $message) {
            $errors[] = [
                'field' => $field,
                'message' => $message[0],
            ];
        }
        return $errors;
    }

    public function invalidJson($request, ValidationException $exception)
    {
        // Fields Not Complete (VALIDATION)
        return response()->json([
            'status' => 'error',
            'data' => $this->transformErrors($exception),
        ], $exception->status);
    }
}
