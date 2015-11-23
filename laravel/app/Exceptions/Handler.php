<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        HttpException::class,
        ModelNotFoundException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception $e
     * @return void
     */
    public function report(Exception $e)
    {
        return parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        $view = parent::render($request, $e);
        if (!$this->isHttpException($e))
            header('Access-Control-Allow-Origin: *');
        if (!$request->ajax() && !$request->wantsJson())
            return $view;

        $level      = 'error';
        $statusCode = $view->getStatusCode();
        $errors     = [];

        switch (get_class($e)) {
            case \App\Exceptions\NotFoundException::class:
            case \App\Exceptions\SignUpException::class:
            case \App\Exceptions\SignInException::class:
            case \App\Exceptions\FileUploadException::class:
            case \App\Exceptions\DataException::class:
            case \App\Exceptions\SecurityException::class:
                $level = 'warning';
                break;
            case \App\Exceptions\RequestValidationException::class:
                $level = 'warning';
                /** @var RequestValidationException $e */
                $errors = $e->getErrors();
                break;
        }

        $message = $e->getMessage();
        if (!$message) {
            switch (get_class($e)) {
                case \Illuminate\Session\TokenMismatchException::class:
                    $message = 'TokenMismatchException';
                    break;
                default:
                    $message = class_basename($e);
            }
        }

        if (config('app.debug'))
            return new JsonResponse([
                'exception' => class_basename($e),
                'code'      => $e->getCode(),
                'level'     => $level,
                'message'   => $message,
                'errors'    => $errors,
                'trace'     => $e->getTraceAsString(),
            ], $statusCode);

        return new JsonResponse([
            'exception' => class_basename($e),
            'code'      => $e->getCode(),
            'level'     => $level,
            'message'   => $message,
            'errors'    => $errors,
        ], $statusCode);
    }
}
