<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Foundation\Validation\ValidationException;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Whoops\Handler\PrettyPageHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
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
     * @return Response
     */
    public function render($request, Exception $e)
    {
        $view = parent::render($request, $e);

        if (!$request->ajax() && !$request->wantsJson()) {
            if ($this->isHttpException($e)) {
                return $view;
            }

            if (config('app.debug')) {
                return $this->renderExceptionWithWhoops($view, $e);
            }

            return $view;
        }


        $level      = 'error';
        $statusCode = $view->getStatusCode();
        $errors     = [];

        switch (get_class($e)) {
            case \App\Exceptions\NotFoundException::class:
            case \App\Exceptions\SignUpException::class:
            case \App\Exceptions\SignInException::class:
            case \App\Exceptions\FileUploadException::class:
            case \App\Exceptions\DataException::class:
                $level = 'warning';
                break;
            case \App\Exceptions\SecurityException::class:
                $level = 'warning';
                if ($e->getCode() == \App\Exceptions\SecurityException::LoginFist)
                    $statusCode = 401;
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
            return response()->json([
                'exception' => class_basename($e),
                'code'      => $e->getCode(),
                'level'     => $level,
                'message'   => $message,
                'errors'    => $errors,
                'trace'     => $e->getTraceAsString(),
            ], $statusCode, $view->headers->all());

        return response()->json([
            'exception' => class_basename($e),
            'code'      => $e->getCode(),
            'level'     => $level,
            'message'   => $message,
            'errors'    => $errors,
        ], $statusCode, $view->headers->all());
    }

    /**
     * Map exception into an illuminate response.
     *
     * @param  \Symfony\Component\HttpFoundation\Response $response
     * @param  \Exception $e
     * @return Response
     */
    protected function toIlluminateResponse($response, Exception $e)
    {
        $response = parent::toIlluminateResponse($response, $e);
        if (!$response->headers->get('Access-Control-Allow-Origin'))
            $response->headers->set('Access-Control-Allow-Origin', \Request::header('Origin'));
        if (!$response->headers->get('Access-Control-Allow-Credentials'))
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
        return $response;
    }

    /**
     * Render an exception using Whoops.
     *
     * @param Response $view
     * @param Exception $e
     * @return Response
     */
    protected function renderExceptionWithWhoops(Response $view, Exception $e)
    {
        $whoops = new \Whoops\Run;
        $whoops->pushHandler(new PrettyPageHandler());

        return new Response(
            $whoops->handleException($e),
            $view->getStatusCode(),
            $view->headers
        );
    }
}
