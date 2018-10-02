<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Common\SystemCotroller;
use App\Http\Controllers\Controller;

class BaseController extends Controller
{
    //
    protected $system;
    public function __construct()
    {
        parent::__construct();
        $this->system = new SystemCotroller();
    }

}
