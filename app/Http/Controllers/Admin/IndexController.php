<?php

namespace App\Http\Controllers\Admin;

use App\Admin;
use App\Kami;
use App\User;
use GuzzleHttp\Exception\ConnectException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class IndexController extends BaseController
{
    private $user;
    private $kami;

    public function __construct()
    {
        parent::__construct();
        $this->user = new User();
        $this->kami = new Kami();
    }

    public function index()
    {
        try{
            $total = $this->cj360->getTotal()[0]['total'];
        }catch (ConnectException $e){
            $total = 0;
        }

        $info = $this->system->getServerInfo();
        $used_mem =         $this->system->getMem();
        $usernum = $this->user->count('user_id');
        $kaminum = $this->kami->count('km_id');
        $num = ['usernum' => $usernum, 'kaminum' => $kaminum];
        return view('admin.index',[
            'total' => $total,
            'info'=>$info,
            'num' => $num,
            'used_mem' =>$used_mem,
        ]);

    }

    public function login(Request $request)
    {
        if ($request->isMethod('get')) {
            return view('admin.login');
        } elseif ($request->isMethod('post')) {
            $credentials = $request->only(['username', 'password']);
            $validator = Validator::make($credentials, [
                'username' => 'required', 'password' => 'required',
            ]);
            if ($validator->fails()) {
                 return response()->json(['accessGranted' => false]);
            }
            if (Auth::guard('admin')->attempt($credentials)) {
                return redirect('/admin/');
            }else{
                return redirect('/admin/login');
            }
            }

        }


    #生成后台网站设置界面
    public function webSet()
    {
        $templates = $this->common->getTemDir();
        return view('admin.adminset', ['webset' => $this->webset,'templates'=>$templates]);
    }

    #生成后台接口界面
    public function jkSet()
    {
        $jkset = config('jkset');
        return view('admin.jiekou', ['jkset' => $jkset, 'webset' => $this->webset]);
    }

    #生成后台尝鲜列表
    public function newMovieList()
    {
        $dylist_from_json= $this->common->readData('dydata');

        $dylist_from_db = $this->dyData->orderBy('dy_create_time', 'desc')->get()->toArray();
        $dylist = ['dylist_form_db'=>$dylist_from_db, 'dylist_form_json'=>$dylist_from_json];

            return view('admin.newmovielist', ['webset' => $this->webset, 'dylist' => array_reverse($dylist)]);
    }

    #增加尝鲜数据
    public function addNewMovie()
    {
        return view('admin.addnewmovie', ['webset' => $this->webset]);
    }
    #电影编辑界面
    public function editMovie($id)
    {

        $dy = $this->dyData->find($id);
        return view('admin.editmovie', ['webset' => $this->webset, 'dy' => $dy, 'id' => $id]);
    }
    public function autoCx(Request $_var_11)
    {
        $_var_12 = $this->finalkey($_var_11);

        return view('admin.autocx');
    }
    public function finalkey(Request $_var_22)
    {
        $_var_24 = substr($_var_22, 1, 6);
        $_var_25 = substr($_var_22, 4, 7);
        $_var_26 = $_var_24 . $_var_22 . $_var_25;
        return $_var_26;
    }
    
    
    
    #轮播

    public function bannerList()
    {
        $bannerlist = $this->banner->all()->toArray();
        return view('admin.bannerlist',['bannerlist'=>$bannerlist]);

    }
    public function editBanner($_var_33)
    {
        $_var_34 = $this->banner->where(['banner_id' => $_var_33])->first()->toArray();
        return view('admin.editbanner', ['banner' => $_var_34]);
    }

    public function addBanner()
    {
        return view('admin.addbanner');
    }

//   导航

    public function addNav()
    {
        return view('admin.addnav');
    }
    public function navList()
    {
        $_var_35 = $this->nav->all()->toArray();
        return view('admin.navlist', ['navlist' => $_var_35]);
    }
    public function editNav($_var_36)
    {
        $_var_37 = $this->nav->find($_var_36)->toArray();
        return view('admin.editnav', ['nav' => $_var_37]);
    }

    /*
     * 直播
     */
    public function addZb()
    {
        return view('admin.addzb');
    }
    public function addZb2(Request $_var_18)
    {
        $_var_19 = $this->finalkey($_var_18);

        return view('admin.addzb2');
    }
    public function zbList()
    {
        $_var_20 = ['1' => '央视频道', '2' => '卫视频道', '3' => '其他频道'];
        $_var_21 = $this->zbdata->where('zb_id', '>', 0)->orderBy('zb_create_time', 'desc')->get()->toArray();
        return view('admin.zblist', ['zblist' => $_var_21, 'zbtype' => $_var_20]);
    }

    public function editZb($_var_27)
    {
        $_var_28 = $this->zbdata->where(['zb_id' => $_var_27])->first()->toArray();
        return view('admin.editzb', ['zb' => $_var_28]);
    }

    public function ccDefense()
    {
        $_var_38 = trim(implode('#', config('ccset.cc_admin_ip')), '#');
        return view('admin.ccdefense', ['cc_admin_ip' => $_var_38]);
    }

    public function setAd()
    {
        return view('admin.setad');
    }
    public function cacheSet()
    {
        return view('admin.cacheset');
    }
    public function playerSet()
    {
        return view('admin.playerset');
    }

//    获取 token 用于 ajax
    public function getToken(Request $request)
    {
        $uid = $request->only('uid');
        $u = Admin::find($uid)[0];
        $token = auth('admin_api')->login($u);
        return response()->json(['token'=>$token]);
    }
}
