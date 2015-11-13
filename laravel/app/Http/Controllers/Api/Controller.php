<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller as BaseController;
use Illuminate\Http\Request;

class Controller extends BaseController
{
    var $request;

    function __construct(Request $request)
    {
        $this->request = $request;
    }

    protected function buildResponse($message = '', $data = [], $redirect = null, $level = 'success')
    {
        if (is_null($redirect)) {
            $redirect = '[NOT_REDIRECT]';
            if ($this->request->has('_success_redirect')) {
                $redirect = $this->request->get('_success_redirect');
            }
        }

        if ($this->request->ajax() || $this->request->wantsJson() || $this->request->isMethod('GET')) {
            return response()->json([
                'level'    => $level,
                'message'  => $message,
                'redirect' => $redirect,
                'data'     => $data,
            ]);
        }

        return view('redirect', [
            'level'    => $level,
            'message'  => $message,
            'redirect' => $redirect,
            'data'     => $data,
        ]);
    }
}
