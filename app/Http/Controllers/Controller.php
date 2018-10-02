<?php

namespace App\Http\Controllers;
use App\Banner;
use App\DyData;
use App\Http\Controllers\Common\CommonController;
use App\Http\Servers\CJ360;
use App\LiveData;
use App\Nav;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    protected $cj360;
    protected $webset;
    protected  $dyData;
    protected $common;
    protected $banner;
    protected $nav;
    protected $zbdata;
    public function __construct()
    {
        $this->webset = config('webset');
        $this->cj360 = new CJ360();
        $this->dyData = new DyData();
        $this->common = new CommonController();
        $this->banner = new  Banner();
        $this->nav = new  Nav();
        $this->zbdata = new LiveData();
    }
}
