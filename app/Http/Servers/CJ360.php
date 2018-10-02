<?php
namespace App\Http\Servers;
/**
 * Created by PhpStorm.
 * User: SI
 * Date: 2018/9/10
 * Time: 13:54
 */
use App\Http\Controllers\Common\CommonController;
use function GuzzleHttp\Psr7\str;
use QL\QueryList;
class CJ360
{
    private $ql;
//    private $common;
    private $domin;
    private $opts = [
        // 伪造http头
        'headers' => [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36',
        ]
    ];

    //初始化采集类
    public function __construct()
    {
        $this->ql = new QueryList();
        $this->domin = "https://www.360kan.com";
        $this->common= new CommonController();

    }

    #生成首页电视数据
    public function getIndexDsj()
    {
        $data = $this->dsjList('all', 1);
        return $data;
    }

    #采集首页电视数据
    public function indexDsCollect()
    {
        $rules = [
            'title' => ['ul:eq(10) .s1', 'text', ''],
            'url' => ['ul:eq(10) a.js-link', 'href', ''],
            'img' => ['ul:eq(10) .js-playicon img', 'data-src', ''],
            'pf' => ['ul:eq(10) .s2', 'text', ''],
            'js' => ['ul:eq(10) .w-newfigure-hint', 'text'],
        ];

        $data = $this->ql->get($this->domin, '', $this->opts)->rules($rules)->query()->getData();
        return $data->all();
    }
    #采集电影列表数据
    public function dyList($cat, $page)
    {
        $url = $this->domin . '/dianying/list?rank=rankhot&cat=' . $cat . '&area=all&act=all&year=all&pageno=' . $page;
        $rules = [
            'title' => ['.title  .s1', 'text'],
            'url' => ['a.js-tongjic', 'href', '', function ($content) {
                return base64_encode($this->domin . $content);
            }],
            'img' => ['a.js-tongjic .cover.g-playicon img', 'src', ''],
            'pf' => ['.s2', 'text', ''],
            'year' => ['.hint', 'text'],
            'star' => ['.star', 'text', '']
        ];
        $data = $this->ql->get($url)->rules($rules)->query()->getData();
        $this->ql->destruct();
        return $data->all();
    }

    #采集电视剧列表数据
    public function dsjList($cat, $page)
    {
        $url = $this->domin . '/dianshi/list?rank=rankhot&cat=' . $cat . '&area=all&act=all&year=all&pageno=' . $page;
        $rules = [
            'title' => ['.list.g-clear .s1', 'text', ''],
            'url' => ['.list.g-clear a.js-tongjic', 'href', '', function ($content) {
                return base64_encode($this->domin . $content);
            }],
            'img' => ['.list.g-clear .cover.g-playicon img', 'src', ''],
            'js' => ['.list.g-clear .hint', 'text', ''],
            'star' => ['.list.g-clear .star', 'text', '']
        ];
        $data = $this->ql->get($url)->rules($rules)->query()->getData();
        return $data->all();
    }

    #采集综艺列表数据
    public function zyList($cat, $page)
    {
        $url = $this->domin . '/zongyi/list?rank=rankhot&cat=' . $cat . '&area=all&act=all&pageno=' . $page;
        $rules = [
            'title' => ['.list.g-clear .s1', 'text', ''],
            'url' => ['.list.g-clear a.js-tongjic', 'href', '', function ($content) {
                return base64_encode($this->domin . $content);
            }],
            'img' => ['.list.g-clear .cover.g-playicon img', 'src', ''],
            'js' => ['.list.g-clear .hint', 'text', ''],
            'star' => ['.list.g-clear .star', 'text', '']
        ];
        $data = $this->ql->get($url)->rules($rules)->query()->getData();
        return $data->all();
    }

    #采集动漫列表数据
    public function dmList($cat, $page)
    {
        $url = $this->domin . '/dongman/list?rank=rankhot&cat=' . $cat . '&area=all&act=all&pageno=' . $page;
        $rules = [
            'title' => ['.list.g-clear .s1', 'text', ''],
            'url' => ['.list.g-clear a.js-tongjic', 'href', '', function ($content) {
                return base64_encode($this->domin . $content);
            }],
            'img' => ['.list.g-clear .cover.g-playicon img', 'src', ''],
            'js' => ['.list.g-clear .hint', 'text', ''],
        ];
        $data = $this->ql->get($url)->rules($rules)->query()->getData();
        return $data->all();
    }

    #获取电影播放列表
    public function getDyPlay($url)
    {
        $rules = [
            'title'=>['.title-left.g-clear h1','text',''],
            'desc' => ['.item-desc', 'text', ],
            'playname' => ['.top-list-zd.g-clear a[data-daochu^="to="]', 'text', '-span'],
            'play' => ['.top-list-zd.g-clear a[data-daochu^="to="]', 'href', '', function ($content) {
                if (strpos($content, 'cps')&&strpos($content, 'youku')) {
                    $arr = explode('&', $content);
                    $url = str_replace('url=', '', $arr['1']);
                    return $url;

                } else {
                    if(strpos($content, '?')){
                        $lenth = strpos($content, '?');
                        return substr($content, 0, $lenth);
                    }
                    else{
                        return $content;
                    }

                }

            }]
        ];
        $data = $this->ql->get($url)->rules($rules)->query()->getData();
        return $data->all();
    }

    #获取电视剧播放列表
    public function getDsjPlay($url)
    {
        $rules = [
            'title'=>['.title-left.g-clear h1','text',''],
            'desc' => ['.item-desc', 'text'],
        ];
        $data = $this->ql->get($url)->rules($rules)->query()->getData();
        return $data->all();
    }

    #获取综艺播放剧集
    public function getZyPlay($url)
    {
        $rules = [
            'bt'=>['.title-left.g-clear h1','text',''],
            'zd' => ['.ea-site', 'text', ''],
            'desc' => ['.item-desc', 'text'],
            'title' => ['.w-newfigure', 'title', ''],
            'href' => ['a.js-link', 'href'],
            'time' => ['.w-newfigure-hint', 'text', ''] ,
//            'title' => ['.js-year-page .s1', 'text', ''],
//            'href' => ['.js-year-page a.js-link', 'href'],
//            'time' => ['.js-year-page .w-newfigure-hint', 'text', '']
        ];
        $data = $this->ql->get($url)->rules($rules)->query()->getData();
        return $data->all();
    }

    #获取搜索结果
    public function getSearch($key){
        $url = 'https://so.360kan.com/index.php?kw='.$key;
        $rules = [
            'title'=>['.b-mainpic a','title',''],
            'url'=>['.b-mainpic a','href','',function($content){
                $url = str_replace('http://','https://',$content);
                return base64_encode($url);
            }],
            'img'=>['.b-mainpic img','src',''],
            'type'=>['.cont .playtype','text',''],
            'desc'=>['.js-b-fulldesc','data-full']
        ];
        $data = $this->ql->get($url)->rules($rules)->query()->getData();
        return $data->all();
    }

    #------后台核心逻辑--------
    #获取影片总数
    public function getTotal(){
        $url = $this->domin.'/dianshi/list.php';
        $rules = [
            'total'=>['.app span','text','']
        ];
        $data = $this->ql->get($url)->rules($rules)->query()->getData();
        return $data->all();
    }

    #获取尝鲜url
    public function getCx($key,$dizhi){
        if (strpos($dizhi, 'kuyun')) {

            /*
             * <video>
             * <last>2018/9/16 16:55:47</last>
             * <id>7410</id>
             * <tid>31</tid>
             * <name><!--[CDATA[名侦探柯南2018TV版]]--></name><
             * type>动漫剧场</type>
             * <dt>kkm3u8</dt>
             * <note><!--[CDATA[第913集]]--></note>
             * </video>
             */
            $url = 'http://www.kuyun9.com/inc/ldg_kkm3u8.asp?ac=list&t=&pg=1&h=&ids=&wd='.$key;
            $HTML = $this->common->curl_get($url);
            $xml = @simplexml_load_string($HTML);
            $videoList =[];
            $arrInex = 0;
            if ($xml) {
                foreach ($xml->list->video as $k =>$video) {
                    $videoList[$arrInex]=[
                        'title' => (string)$video->name,
                        'url' => 'http://www.kuyun9.com/inc/ldg_kkm3u8.asp?ac=videolist&t=&pg=&h=&ids=' . $video->id . '&wd=',
                    ];
                    $arrInex++;
                }

            }
            return $videoList;
        }else{
            $url = 'http://'.$dizhi.'/index.php?m=vod-search';
        }

        $arr = ['wd'=>$key,'submit'=>'search'];
        $html = $this->common->curl_post($url,$arr);

        $rules = [
            'url'=>['.xing_vb4 a','href',''],
            'title' => ['.xing_vb4 a', 'text', '-span'],
        ];
        $data = $this->ql->html($html)->rules($rules)->query()->getData();
        $this->ql->destruct();
        $res = $data->all();
        foreach ($res as $k => $val) {
            $val['url'] = 'http://' . $dizhi . $val['url'];
            $res[$k]['url'] = $val['url'];
        }
        return $res;

    }

//    public function getCxData($url){
//        $rules = [
//            'dyname'=>['.vodh h2','text'],
//            'dydesc'=>['.vodplayinfo:eq(1)','text',''],
//            'dylogo'=>['.lazy','src'],
//            'dyaddr'=>['ul:eq(6) li','text']
//        ];
//        $data = $this->ql->get($url)->rules($rules)->query()->getData();
//        return $data->all();
//    }

    public function getCxData($_var_87)
    {
        $_var_88 = ['dyname' => ['.vodh h2', 'text'], 'dydesc' => ['.vodplayinfo:eq(1)', 'text', ''], 'dylogo' => ['.lazy', 'src'],'dytag' => ['.vodh span', 'text']];
        if (strpos($_var_87, 'www.go1977.com') !== false) {
            $_var_88 = ['dyname' => ['.vodh h2', 'text'], 'dydesc' => ['.vodplayinfo:eq(0)', 'text', ''], 'dylogo' => ['.lazy', 'src'], 'dytag' => ['.vodh span', 'text']];
        }
        $_var_89 = $this->ql->get($_var_87)->rules($_var_88)->query()->getData();
        $this->ql->destruct();
        return $_var_89->all();

    }

    public function getCxLink($_var_90, $_var_91 = 'total',$tag='')
    {
        $_var_92 = [];
        $_var_93 = [];
        $_var_94 = [];
        $_var_95 = [];
        $_var_96 = 1;
        $_var_97 = 1;
        $_var_98 = 1;
        $_var_99 = ['dyaddr' => ['input', 'value', '']];
        $html = $this->ql->get($_var_90);
        $_var_100 = $html->rules($_var_99)->range('.vodplayinfo ul>li')->query()->getData();
        $this->ql->destruct();
        $_var_101 = $_var_100->all();

        if (count($_var_101) > 3) {
            foreach ($_var_101 as $_var_102) {
                $_var_94[] = '第' . $_var_98++ . '集$' . $_var_102['dyaddr'];

                if (strpos($_var_102['dyaddr'], 'm3u8')) {
//                    $_var_93[] = '第' . $_var_96++ . '集$' . (config('playerconfig.m3u8') ?? '/public/player/player.php?url=') . $_var_102['dyaddr'];
                    $_var_93[] = '第' . $_var_96++ . '集$' .$_var_102['dyaddr'];
                } elseif (strpos($_var_102['dyaddr'], 'mp4')) {
//                    $_var_95[] = '第' . $_var_97++ . '集$' . (config('playerconfig.mp4') ?? '/public/player/player.php?url=') . $_var_102['dyaddr'];
                    $_var_95[] = '第' . $_var_97++ . '集$'  . $_var_102['dyaddr'];
                } else {
                    $_var_94[] = '第' . $_var_98++ . '集$' . $_var_102['dyaddr'];
                }
            }
        } else {

            foreach ($_var_101 as $_var_102) {
                if (strpos($_var_102['dyaddr'], 'm3u8')) {
                    $_var_93[] = $tag.'$'.$_var_102['dyaddr'];

                } elseif (strpos($_var_102['dyaddr'], 'mp4')) {
                    $_var_95[] = $tag.'$'.$_var_102['dyaddr'];
                } else {
                    $_var_94[] = $tag.'$'.$_var_102['dyaddr'];
                }
            }
        }
        switch ($_var_91) {
            case 'm3u8':
                return $_var_93;
                break;
            case 'mp4':
                return $_var_95;
                break;
            case 'zhilian':
                return $_var_94;
                break;
            case 'total':
                if ($_var_93) {
                    $_var_92['m3u8'] = $_var_93;
                }
                if ($_var_95) {
                    $_var_92['mp4'] = $_var_95;
                }
                if ($_var_94) {
                    $_var_92['zhilian'] = $_var_94;
                }
                return $_var_92;
                break;
        }
    }
}