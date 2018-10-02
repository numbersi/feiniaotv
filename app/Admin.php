<?php
/**
 * Created by PhpStorm.
 * User: echo
 * Date: 2018/3/23
 * Time: 16:04
 */

namespace App;


use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
class Admin extends Authenticatable implements JWTSubject
{
    use Notifiable;


    #定义表名
    protected $table = "admin";
    #指定主键
    protected $primaryKey= 'admin_id';

    #定义关闭自动写入时间戳
    public $timestamps = false;



    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }


}