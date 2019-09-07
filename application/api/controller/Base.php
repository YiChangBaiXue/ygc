<?php
namespace app\api\controller;

use think\Controller;
use think\Request;

class Base extends Controller
{

    public static $_method ;
    public static $_data ;

    public function initialize () {
        self::$_method = Request() -> method();
        self::$_data = Request() -> param();
    }

    //返回信息
    protected function _mess($status,$msg,$data=''){
        //return json(array('status'=>$status, 'msg'=>$msg, 'data'=>$data) );

        json(array('status'=>$status, 'msg'=>$msg, 'data'=>$data) )->send();
        exit;
    }




    public function delArrVal($arr,$val){
        $key = array_search($val, $arr);
        if ($key !== false)
          array_splice($arr, $key, 1);

        return $arr;
    }

    public function addArrVal($arr,$val){
        array_push($arr,$val);
        return $arr;
    }

    

}
