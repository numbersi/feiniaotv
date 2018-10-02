<?php

namespace App\Http\Controllers\Admin;


use App\DyData;
use App\Http\Controllers\Common\WriteConfigController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class actionController extends Controller
{
    private $config;
    private $core;

    public function __construct()
    {
        #初始化公共控制器
        parent::__construct();
        $this->config = new WriteConfigController();
        $this->core = $this->cj360;
    }

    #网站基本设置
    public function webSet(Request $request)
    {
        $path = CONFIG_PATH . 'webset.php';
        $res = $request->post();
        $res['weblogo'] = $this->upload($request);
        $msg = $this->config->writeConfig($res, $path);
        if ($msg == 'ok') {
            echo json_encode(['status' => 200, 'msg' => '修改成功', 'path' => $res['webdir']]);
        } else {
            echo json_encode(['status' => 400, 'msg' => '修改失败']);
        }

    }

    #播放接口设置
    public function jkSet(Request $request)
    {
        $path = CONFIG_PATH . 'jkset.php';
        $res = $request->post();
        $this->writeConfig($res, $path);

    }

    
    #autoCx
    public function autoCx(Request $request)
    {
        $path = CONFIG_PATH . 'autocxconfig.php';
        $res = $request->post();
        $this->writeConfig($res, $path);
    }

    public function playerSet(Request $request)
    {
        $path = CONFIG_PATH . 'playerconfig.php';
        $res = $request->post();
        $this->writeConfig($res, $path);
    }

    public function getCxList(Request $request)
    {
        $wd = $request['wd']?$request['wd']:$request['searchword'];
        $dizhi = $request['dizhi'];
        $data = $this->core->getCx($wd,$dizhi);
        return ['cxlist'=>$data,'status'=>200];
    }
    
    #增加电影
    public function addNewMovie(Request $request)
    {
        $res = $request->post();
        $dyData['dy_title']=$res['dyname'];
        $dyData['dy_img']=$res['dylogo'];
        $dyData['dy_desc']=$res['dydesc'];
        $dyData['dy_addr']=$res['dyaddr'];
        $dyData['dy_sort']=$res['dy_sort'];
        $dyData['dy_tag']=$res['dytag'];


        $msg = DyData::create($dyData);

        if ($msg ) {
            echo json_encode(['status' => 200, 'msg' => '增加成功']);
        } else {
            echo json_encode(['status' => 400, 'msg' => '增加失败']);
        }


    }

    #编辑电影
    public function editMovie(Request $request)
    {
        $res = $request->post();
        $dy=  DyData::where('dy_id', $res['dy_id'])->update($res);
        if ($dy) {
            echo json_encode(['status' => 200, 'msg' => '编辑成功']);
        } else {
            echo json_encode(['status' => 400, 'msg' => '编辑失败']);
        }

    }

    #删除电影
    public function deleteMovie(Request $request)
    {
        $id = $request->post('dyid');
        $msg = $this->config->writeDyData('', $id, 'delete');
        $mag=DyData::where('dy_id', $id)->delete();
        if ($msg) {
            echo json_encode(['status' => 200, 'msg' => '删除成功']);
        } else {
            echo json_encode(['status' => 400, 'msg' => '删除失败']);
        }
    }

    #添加友情连接
    public function addYqlink(Request $request)
    {
        $res = $request->post();
        $id = $this->setNum();//获得随机数
        $msg = $this->config->writeYqLink($res, $id);
        if ($msg == 'ok') {
            echo json_encode(['status' => 200, 'msg' => '增加成功']);
        } else {
            echo json_encode(['status' => 400, 'msg' => '增加失败']);
        }


    }
    #编辑友情链接
    public function editYqList(Request $request)
    {
        $res = $request->post();
        $msg = $this->config->writeYqLink($res, $res['yqid']);
        if ($msg == 'ok') {
            echo json_encode(['status' => 200, 'msg' => '编辑成功']);
        } else {
            echo json_encode(['status' => 400, 'msg' => '编辑失败']);
        }

    }
    #删除友情链接
    public function deleteYqLink(Request $request)
    {
        $id = $request->post('yqid');
        $msg = $this->config->writeYqLink('', $id, 'delete');
        if ($msg == 'ok') {
            echo json_encode(['status' => 200, 'msg' => '删除成功']);
        } else {
            echo json_encode(['status' => 400, 'msg' => '删除失败']);
        }
    }

    #添加直播
    public function addZb(Request $request)
    {
        $res = $request->post();
        $id = $this->setNum();//获得随机数
        $msg = $this->config->writeZb($res, $id);
        if ($msg == 'ok') {
            echo json_encode(['status' => 200, 'msg' => '增加成功']);
        } else {
            echo json_encode(['status' => 400, 'msg' => '增加失败']);
        }


    }
    #编辑直播
    public function editZb(Request $request)
    {
        $res = $request->post();
        $id = $res['zb_id'];
        $msg = $this->zbdata->find($id)->update($res);
        if ($msg) {
            echo json_encode(['status' => 200, 'msg' => '编辑成功']);
        } else {
            echo json_encode(['status' => 400, 'msg' => '编辑失败']);
        }

    }
    #删除直播
    public function deleteZb(Request $request)
    {
        $id = $request->post('zb_id');
        $msg = $this->config->writeZb('', $id, 'delete');
        if ($msg == 'ok') {
            echo json_encode(['status' => 200, 'msg' => '删除成功']);
        } else {
            echo json_encode(['status' => 400, 'msg' => '删除失败']);
        }
    }

    #添加侵权
    public function addQq(Request $request)
    {
        $res = $request->post();
        $id = $this->setNum();//获得随机数
        $msg = $this->config->writeData($res, $id,'','qqlist');
        if ($msg == 'ok') {
            echo json_encode(['status' => 200, 'msg' => '增加成功']);
        } else {
            echo json_encode(['status' => 400, 'msg' => '增加失败']);
        }
    }
    #编辑清泉
    public function editQq(Request $request)
    {
        $res = $request->post();
        $msg = $this->config->writeData($res, $res['qqid'],'','qqlist');
        if ($msg == 'ok') {
            echo json_encode(['status' => 200, 'msg' => '编辑成功']);
        } else {
            echo json_encode(['status' => 400, 'msg' => '编辑失败']);
        }

    }
    #删除侵权
    public function delQqLink(Request $request)
    {
        $id = $request->post('qqid');
        $msg = $this->config->writeData('', $id, 'delete','qqlist');
        if ($msg == 'ok') {
            echo json_encode(['status' => 200, 'msg' => '删除成功']);
        } else {
            echo json_encode(['status' => 400, 'msg' => '删除失败']);
        }
    }

    #添加轮播
    public function addBanner(Request $request)
    {
        $res = $request->post();
        $id = $this->setNum();//获得随机数
        $msg = $this->config->writeData($res, $id,'','bannerlist');
        if ($msg == 'ok') {
            echo json_encode(['status' => 200, 'msg' => '增加成功']);
        } else {
            echo json_encode(['status' => 400, 'msg' => '增加失败']);
        }
    }
    #编辑轮播
    public function editBanner(Request $request)
    {
        $res = $request->post();
        $msg =$this->banner->where('banner_id', $res['banner_id'])->update($res);
        $msg = $this->config->writeData($res, $res['banner_id'],'','bannerlist');
        if ($msg) {
            echo json_encode(['status' => 200, 'msg' => '编辑成功']);
        } else {
            echo json_encode(['status' => 400, 'msg' => '编辑失败']);
        }

    }
    #删除轮播
    public function delBanner(Request $request)
    {
        $id = $request->post('bannerid');
        $msg = $this->config->writeData('', $id, 'delete','bannerlist');
        if ($msg == 'ok') {
            echo json_encode(['status' => 200, 'msg' => '删除成功']);
        } else {
            echo json_encode(['status' => 400, 'msg' => '删除失败']);
        }
    }

    #添加导航
    public function addNav(Request $request)
    {
        $res = $request->post();
        $msg = $this->nav->fill($res)->save();
        if ($msg) {
            echo json_encode(['status' => 200, 'msg' => '增加成功']);
        } else {
            echo json_encode(['status' => 400, 'msg' => '增加失败']);
        }
    }
    #编辑导航
    public function editNav(Request $request)
    {
        $res = $request->post();
        $msg = $this->nav->where('nav_id', $request['nav_id'])->update($res);

        if ($msg ) {
            echo json_encode(['status' => 200, 'msg' => '编辑成功']);
        } else {
            echo json_encode(['status' => 400, 'msg' => '编辑失败']);
        }

    }
    #删除导航
    public function delNav(Request $request)
    {
        $id = $request->post('nav_id');
        $msg = $this->nav->find($id)->delete();
        if ($msg == 'ok') {
            echo json_encode(['status' => 200, 'msg' => '删除成功']);
        } else {
            echo json_encode(['status' => 400, 'msg' => '删除失败']);
        }
    }

    #生成短网址
    public function getShortUrl(Request $request)
    {
        $key = '3421048570';
        $url = $request->post('longurl');
        $api = 'http://api.t.sina.com.cn/short_url/shorten.json';
        $request_url = sprintf($api.'?source=%s&url_long=%s', $key, $url);
        $str = $this->common->curl_get_dwz($request_url);
        return $str;
        $short = json_decode($str,true);
        if (!empty($short)) {
            $res = ['status' => 200, 'shorturl' => $short[0]['url_short']];
        } else {
            $res = ['status' => 400];
        }

        return json_encode($res);
    }

    #设置微信信息
    public function setWeiXin(Request $request){
        $path = CONFIG_PATH . 'wxconfig.php';
        $res = $request->post();
        $msg = $this->config->writeConfig($res, $path);
        if ($msg == 'ok') {
            echo json_encode(['status' => 200, 'msg' => '修改成功']);
        } else {
            echo json_encode(['status' => 400, 'msg' => '修改失败']);
        }
    }
    #广告数据
    public function setAd(Request $request){
        $path = CONFIG_PATH . 'adconfig.php';
        $res = $request->post();
        $msg = $this->config->writeConfig($res, $path);
        if ($msg == 'ok') {
            echo json_encode(['status' => 200, 'msg' => '修改成功']);
        } else {
            echo json_encode(['status' => 400, 'msg' => '修改失败']);
        }
    }
    #APP数据
    public function appInfo(Request $request){
        $path = CONFIG_PATH . 'appconfig.php';
        $res = $request->post();
        $msg = $this->config->writeConfig($res, $path);
        if ($msg == 'ok') {
            echo json_encode(['status' => 200, 'msg' => '修改成功']);
        } else {
            echo json_encode(['status' => 400, 'msg' => '修改失败']);
        }
    }

    #上传网站logo
    private function upload(Request $request)
    {
        return config('webset.webtemplate').'/images/'.'logo.png';
        if (!$request->file('weblogo')) {
            return config('webset.webtemplate').'/images/'.'logo.png';
        }

        $disk = Storage::disk('qiniu');
        $disk =   Storage::fake('qiniu');
//        $path ='http://kuainiaobucket.numbersi.cn/'. $disk->put('postImage', $request->file('weblogo'));               //上传文件

        $path = $request->file('weblogo')->storeAs('/public/'.config('webset.webtemplate').'/images', 'logo.png');

        return $path;
    }
    #生成随机id
    private function setNum()
    {
        $str = '';
        for ($i = 0; $i < 10; $i++) {
            $str .= rand(0, 9);
        }
        return time() . $str;
    }

    #刷新缓存
    public function flushCache($action){
        $res = $this->common->flushIndex($action);
        if ($res) {
            echo json_encode(['status' => 200, 'msg' => '刷新成功']);
        } else {
            echo json_encode(['status' => 400, 'msg' => '刷新失败']);
        }
    }


    public function vod_xml_replace($url)
    {
        $array_url = array();
        $arr_ji = explode('#',str_replace('||','//',$url));
        foreach($arr_ji as $key=>$value){
            $urlji = explode('$',$value);
            if( count($urlji) > 1 ){
                $array_url[$key] = $urlji[0].'$'.trim($urlji[1]);
            }else{
                $array_url[$key] = trim($urlji[0]);
            }
        }
        return implode(',',$array_url);
    }
    #获取尝鲜数据
    public function getCx(Request $request)
    {

        $url = $request['url'];
        $type = $request['type'];
        if (strpos($url,'kuyun')) {
            $html  =$this->common->curl_get($url);
            $xml = @simplexml_load_string($html);
            $video = $xml->list->video;
            $videoData['dyname'] = (string)$video->name;
            $videoData['dydesc'] = (string)$video->des;
            $videoData['dylogo'] = (string)$video->pic;
            $videoData['dytag'] = (string)$video->note;
            foreach ($video->dl->dd as $dd) {

                if (strpos($dd['flag'], $type)) {
                    $res =  $this->vod_xml_replace((string)$dd);
                    $videoData = array_add($videoData ,'dyaddr',[0=>$res]);
                }
            }
            return  array_add($videoData ,'status',200);
        }
        $res = $this->core->getCxData($url)[0];
        $tag = $res['dytag'];
        $getCxLink = $this->core->getCxLink($url,$type,$tag);
        if ($getCxLink) {
            $res = array_add($res, 'dyaddr', $getCxLink);
            return response(        array_add($res ,'status',200));
        }
        return response(['status' => 404, 'msg' => '没有'. $type]);


    }
    #设置cc防御参数
    public function ccDefense(Request $request){
        $path = CONFIG_PATH . 'ccset.php';
        $arr = $request->post();
        $arr['cc_admin_ip']=explode('#',$arr['cc_admin_ip']);
        $msg = $this->config->writeConfig($arr,$path);
        if ($msg == 'ok') {
            echo json_encode(['status' => 200, 'msg' => '修改成功']);
        } else {
            echo json_encode(['status' => 400, 'msg' => '修改失败']);
        }
    }

    public function cacheSet(Request $request)
    {
        $path = CONFIG_PATH . 'cacheconfig.php';
        $arr = $request->post();
        $msg = $this->config->writeConfig($arr,$path);
        if ($msg == 'ok') {
            echo json_encode(['status' => 200, 'msg' => '修改成功']);
        } else {
            echo json_encode(['status' => 400, 'msg' => '修改失败']);
        }
    }

    /**
     * @param $res
     * @param $path
     */


    public function writeConfig($res, $path)
    {
        $msg = $this->config->writeConfig($res, $path);
        if ($msg == 'ok') {
            echo json_encode(['status' => 200, 'msg' => '修改成功']);
        } else {
            echo json_encode(['status' => 400, 'msg' => '修改失败']);
        }
    }
}