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

    public function test(){
        $list =  Db::name('lottery_test')->order('id desc')->limit(100)->select();
        $cuo = 0;
        $dui = 0;
        foreach($list as $k => $v){
            if($k == 0){
                echo '<div>期号：'.$v['num'].' 预测：'.$v['yc'].' <span>?</span></div>';
            }else{
                echo '<div>期号：'.$v['num'].' 预测：'.$v['yc'].' <span ';
                if($v['is_win'] == 0){
                    $cuo ++;
                    echo 'style="color: red;">错</span>';
                }else{
                    $dui ++;
                    echo 'style="color: green;">对</span>';
                }
                echo '</div>';
            }
        }
        echo '<div>正确：'.$dui.' 错：'.$cuo.' 正确率：'.($dui/($dui + $cuo) * 100).'%</div>';
    }
    public function test1(){
        $list =  Db::name('lottery_test1')->order('id desc')->limit(100)->select();
        $cuo = 0;
        $dui = 0;
        foreach($list as $k => $v){
            if($k == 0){
                echo '<div>期号：'.$v['num'].' 预测：'.$v['yc'].' <span>?</span></div>';
            }else{
                echo '<div>期号：'.$v['num'].' 预测：'.$v['yc'].' <span ';
                if($v['is_win'] == 0){
                    $cuo ++;
                    echo 'style="color: red;">错</span>';
                }else{
                    $dui ++;
                    echo 'style="color: green;">对</span>';
                }
                echo '</div>';
            }
        }
        echo '<div>正确：'.$dui.' 错：'.$cuo.' 正确率：'.($dui/($dui + $cuo) * 100).'%</div>';
    }
}