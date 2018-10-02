<?php
/**
 * Created by PhpStorm.
 * User: SI
 * Date: 2018/9/22
 * Time: 12:03
 */

namespace App\Http\Servers;
use App\Http\Controllers\Common\CommonController;
use GuzzleHttp\Client;


class Api360
{

    public function getTV()
    {
        $url = 'http://android.api.360kan.com/channel/?cid=3&tid=3&start=0&count=20&method=channel.datas&ss=4';
        $common = new CommonController();
        $html = $common->curl_get($url);
        $jsonObject = substr($html,32);
        return json_decode($jsonObject)->data->data;
    }
}