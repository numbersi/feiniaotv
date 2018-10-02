<?php
namespace App\Http\Controllers\Index;
use App\Banner;
use App\DyData;
use App\Http\Controllers\Common\CommonController;
use App\Http\Servers\Api360;
use App\YqLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class IndexController extends BaseController
{
    private $core;
    private $jk;
    private $fenlei;
    private $newlist;
    private $yqlist;
    private $user;
    private $yqlink;
    private $viplist;
    private $playtype = ['zhilian' => "\xe7\x9b\xb4\xe9\x93\xbe\xe6\x92\xad\xe6\x94\xbe\xe6\xba\x90", 'mp4' => "MP4\xe6\x92\xad\xe6\x94\xbe\xe6\xba\x90", 'm3u8' => "M3U8\xe6\x92\xad\xe6\x94\xbe\xe6\xba\x90"];

    public function __construct()
    {
        parent::__construct();
        #初始化采集核心
        $this->core = $this->cj360;
        #初始化公共控制器
        #初始化设置
        $this->jk = config('jkset');
        #初始化分类
        $this->fenlei = config('fenlei');
        #读取尝鲜列表
//        $this->newlist =
            #读取友链列表
//        $this->yqlist = $this->common->readData('yqlink');
        #读取轮播列表
//        $this->bannerlist = $this->common->readData('bannerlist');
        $this->bannerlist = $this->banner->all()->toArray();
        #读取导航列表
        $this->viplist = DyData::where('dy_id','>',0)->orderBy('dy_sort','desc')->get()->toArray();
        $this->nav = $this->common->navSort();
    }
    #生成首页
    public function index(Request $request, $action = '')
    {
        if(config('cacheconfig.cacheswitch'))
        {
            if(Cache::has('static_index')&& $action=='')
            {
                return Cache::get('static_index');
            }
        }
        $dytype = ($this->fenlei)['movie'];
        if(config('webset.randmovie'))
        {
            $ds=$this->core->dsjList('all',rand(1,24));
            $zy=$this->core->zyList('all',rand(1,24));
            $dm=$this->core->dmList('all',rand(1,24));
            $dy=$this->core->dyList('all',rand(1,24));
        }
        else
        {
            $ds=$this->core->dsjList('all',1);
            $zy=$this->core->zyList('all',1);
//            $dm=$this->core->dmList('all',1);
            $dm=[];
            $dy=$this->core->dyList('all',1);
        }
//        if(config('autocxconfig.is_autocx'))
//        {
//            $var_877=[];
//            $var_878=config('autocxconfig.dizhi');
//            $var_879=$this->core->AutoCxList($v_1,$var_878,rand(5,8),rand(1,3));
//            foreach($var_879 as $var_201)
//            {
//                $var_877[]=$var_201[0];
//            }
//            $this->newlist=$var_877;
//        }
        #渲染页面

        $res = view('template.' . config('webset.webtemplate') . '.index',
            ['dsjs' => $ds,
                'dys' => $dy,
                'zys' => $zy,
                'dms' => $dm,
                'index' => 1,
                'yqlist' => $this->yqlist,
                'videotype' => $this->fenlei,
//                'dydata' => array_reverse($this->newlist),
                'dytype' => $dytype,
                'bannerlist' => $this->bannerlist,
                'navlist' => $this->nav,
                'vipdata'=>$this->viplist
            ])->__toString();

        if(config('cacheconfig.cacheswitch'))
        {
            Cache::forever('static_index',$res);
        }
        return $res;
    }


    private function getZyList($v_19)
    {
        $var_928=$this->core->getZyPlay($v_19);
        return $var_928;
    }

    public function play(Request $request,$play)
    {
        $has = $this->common->filterQq($play);
        if($has==1){
            return view('template.'.config('webset.webtemplate').'.qqtip');
        }
        $history = $this->getHistroy($request);
        if ($history){
            krsort($history);
        }

        if(is_numeric($play)){
            $dy= $this->dyData->where('dy_id', $play)->first();

            $js = $this->common->getJs($dy['dy_addr']);
            $dy['dy_addr'] = $js;
            return view('template.'.config('webset.webtemplate').'.otherplay',['cxs'=>$dy,'yqlist'=>$this->yqlist,'history'=>$history,'navlist'=>$this->nav]);
        }
        $url = base64_decode($play);
        if(strpos($url,'om/m/')!==false){
            #判断是否为电影
            $res = $this->core->getDyPlay($url);
            return view('template.'.config('webset.webtemplate').'.mplay',['desc'=>$res[0]['desc'],'pm'=>$res[0]['title'],'dyplay'=>$res,'jk'=>$this->jk,'yqlist'=>$this->yqlist,'history'=>$history,'navlist'=>$this->nav]);
        }
        elseif (strpos($url,'om/tv/')!==false){
            #判断是否为电视
            $playlist = $this->getTvList($url,2);//获取电视剧列表
            $res = $this->core->getDsjPlay($url);
            if($playlist){
                return view('template.'.config('webset.webtemplate').'.tvplay',['desc'=>$res[0]['desc'],'pm'=>$res[0]['title'],'js'=>$playlist,'jk'=>$this->jk,'yqlist'=>$this->yqlist,'history'=>$history,'navlist'=>$this->nav]);
            }

        }
        elseif (strpos($url,'om/ct/')!==false){
            #判断是否为动漫
            $playlist = $this->getTvList($url,4);//获取动漫列表
            $res = $this->core->getDsjPlay($url);
            if($playlist){
                return view('template.'.config('webset.webtemplate').'.dmplay',['desc'=>$res[0]['desc'],'pm'=>$res[0]['title'],'js'=>$playlist,'jk'=>$this->jk,'yqlist'=>$this->yqlist,'history'=>$history,'navlist'=>$this->nav]);
            }

        }
        elseif (strpos($url,'om/va/')!==false){
            #判断是否为综艺
            $res = $this->getZyList($url);
            return view('template.'.config('webset.webtemplate').'.zyplay',['desc'=>$res[0]['desc'],'pm'=>$res[0]['bt'],'zylist'=>$res,'zd'=>$res[0]['zd'],'jk'=>$this->jk,'yqlist'=>$this->yqlist,'history'=>$history,'navlist'=>$this->nav]);
        }


    }

    public function getTvList($url,$type)
    {
        $v_17=str_replace('https://','',$url);
        $var_920=explode('/',$v_17);
        $var_921=str_replace('.html','',$var_920[2]);
        $var_920=['youku'=>'优酷视频','qq'=>'腾讯视频','imgo'=>'芒果TV','qiyi'=>'爱奇艺','levp'=>'乐视视频','cntv'=>'CNTV','sohu'=>'搜狐视频','tudou'=>'土豆视频','pptv'=>'PPTV'];
        $var_922=[];
        foreach($var_920 as $var_923=>$var_924)
        {
            $var_925='https://www.360kan.com/cover/switchsite?site='.$var_923.'&id='.$var_921.'&category='.$type;
            $var_926=json_decode($this->common->curl_get($var_925),true);
            if($var_926['error']==0)
            {
                $var_927['name']=$var_924;
                $var_927['data']=$var_926['data'];
                $var_922[]=$var_927;
            }
        }
        return $var_922;


    }


    public function tv(Request $request,$cat,$page)
    {


        $dsj = $this->core->dsjList($cat, $page);
        $pagehtml = $this->getPageHtml($page,24,$cat,'tvlist');
        return view('template.'.config('webset.webtemplate').'.tv', ['dsj' => $dsj,'pagehtml'=>$pagehtml,'tvtype'=>($this->fenlei)['tv'],'yqlist'=>$this->yqlist,'navlist'=>$this->nav]);
    }

    public function dy($v_3='all',$v_4='1')
    {
        $var_449=$this->core->dyList($v_3,$v_4);
        $var_882=$this->common->getPageHtml($v_4,24,$v_3,'movielist');
        return view('template.'.config('webset.webtemplate').'.movie',['dys'=>$var_449,'pagehtml'=>$var_882,'dytype'=>($this->fenlei)['movie'],'yqlist'=>$this->yqlist,'navlist'=>$this->nav,'cat'=>$v_3]);
    }
    public function zy($v_10='all',$v_11='1')
    {
        $var_893=$this->core->zyList($v_10,$v_11);
        $var_894=$this->common->getPageHtml($v_11,24,$v_10,'zylist');
        return view('template.'.config('webset.webtemplate').'.zy',['zys'=>$var_893,'pagehtml'=>$var_894,'zytype'=>($this->fenlei['zy']),'yqlist'=>$this->yqlist,'navlist'=>$this->nav,'cat'=>$v_10]);
    }

    public function test(Request $request)
    {

        $api360 = new  Api360();
        $tvs = $api360->getTV();
        return view('numbersi.index', [
            'bannerlist' => $this->bannerlist,
            'videotype' => $this->fenlei,
            'tvs' => $tvs->datas,
            'navlist' => $this->nav,
        ]);


    }

    public function tvPlay(Request $request,$play)
    {
        $url = 'https://www.360kan.com/tv/'.$play.'.html';
        $playlist = $this->getTvList($url,2);//获取电视剧列表
        $res = $this->core->getDsjPlay($url);
        $history = $this->getHistroy($request);
        if ($history){
            krsort($history);
        }
        if($playlist){
            return view('template.'.config('webset.webtemplate').'.tvplay',['desc'=>$res[0]['desc'],'pm'=>$res[0]['title'],'js'=>$playlist,'jk'=>$this->jk,'yqlist'=>$this->yqlist,'history'=>$history,'navlist'=>$this->nav]);
        }
    }



    #分页
    private function getPageHtml($xzv_0, $xzv_4,$cat,$type)
    {
        $xzv_6 = 5;
        $xzv_1='';
        $xzv_0 = $xzv_0 < 1 ? 1 : $xzv_0;
        $xzv_0 = $xzv_0 > $xzv_4 ? $xzv_4 : $xzv_0;
        $xzv_4 = $xzv_4 < $xzv_0 ? $xzv_0 : $xzv_4;
        $xzv_3 = $xzv_0 - floor($xzv_6 / 2);
        $xzv_3 = $xzv_3 < 1 ? 1 : $xzv_3;
        $xzv_2 = $xzv_0 + floor($xzv_6 / 2);
        $xzv_2 = $xzv_2 > $xzv_4 ? $xzv_4 : $xzv_2;
        $xzv_5 = $xzv_2 - $xzv_3 + 1;
        if ($xzv_5 < $xzv_6 && $xzv_3 > 1) {
            $xzv_3 = $xzv_3 - ($xzv_6 - $xzv_5);
            $xzv_3 = $xzv_3 < 1 ? 1 : $xzv_3;
            $xzv_5 = $xzv_2 - $xzv_3 + 1;
        }
        if ($xzv_5 < $xzv_6 && $xzv_2 < $xzv_4) {
            $xzv_2 = $xzv_2 + ($xzv_6 - $xzv_5);
            $xzv_2 = $xzv_2 > $xzv_4 ? $xzv_4 : $xzv_2;
        }
        if ($xzv_0 > 1) {
            if(config('webset.webtemplate')=='wapian'){
                $xzv_1 .= '<li><a  title="上一页" href="' . '/'.$type.'/'.$cat.'/'. ($xzv_0 - 1).'.html' . '"">上一页</a></li>';
            }
            else{
                $xzv_1 .= '<a  title="上一页" href="' . '/'.$type.'/'.$cat.'/'. ($xzv_0 - 1).'.html' . '"">上一页</a>';
            }

        }
        for ($xzv_8 = $xzv_3; $xzv_8 <= $xzv_2; $xzv_8++) {
            if ($xzv_8 == $xzv_0) {
                if(config('webset.webtemplate')=='wapian') {
                    $xzv_1 .= '<li><a style="background:#ff6651;"><font color="#fff">' . $xzv_8 . '</font></a></li>';
                }
                else{
                    $xzv_1 .= '<a style="background:#ff6651;"><font color="#fff">' . $xzv_8 . '</font></a>';
                }
            } else {
                if(config('webset.webtemplate')=='wapian') {
                    $xzv_1 .= '<li><a href="' . '/' . $type . '/' . $cat . '/' . $xzv_8 . '.html' . '">' . $xzv_8 . '</a></li>';
                }
                else{
                    $xzv_1 .= '<a href="' . '/' . $type . '/' . $cat . '/' . $xzv_8 . '.html' . '">' . $xzv_8 . '</a>';
                }
            }
        }
        if ($xzv_0 < $xzv_2) {
            if(config('webset.webtemplate')=='wapian') {
                $xzv_1 .= '<li><a  title="下一页" href="' . '/' . $type . '/' . $cat . '/' . ($xzv_0 + 1) . '.html' . '"">下一页</a></li>';
            }
            else{
                $xzv_1 .= '<a  title="下一页" href="' . '/' . $type . '/' . $cat . '/' . ($xzv_0 + 1) . '.html' . '"">下一页</a>';
            }
        }
        return $xzv_1;
    }

    public function jzAd()
    {

    }

    private function getHistroy($request)
    {

        $history = $request->cookie('history');
        if($history){
            return $history;
        }
    }




}
