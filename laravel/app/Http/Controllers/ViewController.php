<?php namespace App\Http\Controllers;

use App\Http\Controllers\Controller as BaseController;

class ViewController extends BaseController
{
    public function getIndex()
    {
        return view('app-bootstrap-md');
    }
}
