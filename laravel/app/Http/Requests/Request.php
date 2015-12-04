<?php

namespace App\Http\Requests;

use App\Exceptions\RequestValidationException;
use Illuminate\Foundation\Http\FormRequest;

abstract class Request extends FormRequest
{
    public function response(array $errors)
    {
        if ($this->ajax() || $this->wantsJson() || $this->isMethod('GET')) {
            throw new RequestValidationException(RequestValidationException::ValidationFail, $errors);
        }

        return $this->redirector->to($this->getRedirectUrl())
            ->withInput($this->except($this->dontFlash))
            ->withErrors($errors, $this->errorBag);
    }
}
