<?php
/**
 * Created by PhpStorm.
 * User: monster
 * Date: 2017/10/19
 * Time: 9:46
 */

namespace app\open\home;
use think\Controller;
use think\Db;

class Datalist extends Controller
{
    public function index(){
        $data1 = input();
        $id = $data1['id'];
        $list =  Db::name('data')->field('expect,opencode,opentime')->where(['uid' => $id])->order('id desc')->limit(10)->select();
        $data = array(
            'state' => 'ok',
            'data' => $list
        );
        echo json_encode($data);
    }
}