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

class Data extends Controller
{
    public function index(){
        echo 1;
        $this->pk10();
        echo 2;
        $this->xyft();
        echo 3;
        $this->test();
    }
    private function pk10(){
        $re = file_get_contents('http://e.apiplus.net/newly.do?token=t15e58a225c64f432k&code=bjpk10&format=json');
        $re1 = json_decode($re, true);
        $list =  Db::name('data')->field('expect')->where(['uid' => 1])->order('id desc')->find();
        if(empty($list) || $list['expect'] != $re1['data'][0]['expect']){
            $data = array(
                'uid' => 1,
                'expect' => $re1['data'][0]['expect'],
                'opencode' => $re1['data'][0]['opencode'],
                'opentime' => $re1['data'][0]['opentimestamp']
            );
            $r = Db::name('data')->insert($data);
        }
    }
    private function xyft(){
        $re = file_get_contents('http://api.caipiaokong.cn/lottery/?name=xyft&format=json&uid=824514&token=982456d50dfc3e4746bdef4c9009eba2d5e9557d');
        $re1 = json_decode($re, true);
        $list =  Db::name('data')->field('expect')->where(['uid' => 2])->order('id desc')->find();
        $i = 0;
        foreach($re1 as $k => $v){
            if($i == 0){
                $re2 = array(
                    'expect' => $k,
                    'opencode' => $v['number'],
                    'opentime' => strtotime($v['dateline']),
                );
            }
            $i ++;
        }
        if(empty($list) || $re2['expect'] != $list['expect']){
            $data = array(
                'uid' => 2,
                'expect' => $re2['expect'],
                'opencode' => $re2['opencode'],
                'opentime' => $re2['opentime']
            );
            $r = Db::name('data')->insert($data);
        }
    }
    private function test(){
        $re = file_get_contents('http://api.api68.com/pks/getLotteryPksInfo.do?lotCode=10001');
        $re1 = json_decode($re, true);
        $list =  Db::name('lottery_test')->order('id desc')->select();
        if(empty($list)){
            $re2 = explode(',',$re1['result']['data']['preDrawCode']);
            if($re2[0] > $re2[9]){
                $kj = '龙';
                $re3 = 1;
            }else{
                $kj = '虎';
                $re3 = 0;
            }
            $data = array(
                'num' => $re1['result']['data']['preDrawIssue'],
                'content' => $re1['result']['data']['preDrawCode'],
                'yc' => '龙',
                'is_win' => $re3,
                'kj' => $kj
            );
            Db::name('lottery_test')->insert($data);
            if($re3 ==  1){
                $yc = '龙';
            }else{
                $yc = '虎';
            }
            $data1 = array(
                'num' => $re1['result']['data']['preDrawIssue'] + 1,
                'yc' => $yc
            );
            Db::name('lottery_test')->insert($data1);
        }else{
            if($re1['result']['data']['preDrawIssue'] == $list[0]['num']){
                $re2 = explode(',',$re1['result']['data']['preDrawCode']);
                if($re2[0] > $re2[9]){
                    $kj = '龙';
                    if($list[0]['yc'] == '龙'){
                        $re3 = 1;
                    }else{
                        $re3 = 0;
                    }
                }else{
                    $kj = '虎';
                    if($list[0]['yc'] == '虎'){
                        $re3 = 1;
                    }else{
                        $re3 = 0;
                    }
                }
                $data = array(
                    'num' => $re1['result']['data']['preDrawIssue'],
                    'content' => $re1['result']['data']['preDrawCode'],
                    'is_win' => $re3,
                    'kj' => $kj
                );
                Db::name('lottery_test')->where(['id'=>$list[0]['id']])->update($data);
                if(count($list) > 2){
                    if($list[1]['kj'] == $kj){
                        $yc = $kj;
                    }elseif($list[1]['kj'] != $kj && $list[1]['kj'] == $list[2]['kj']){
                        $yc = $kj;
                    }else{
                        $yc = $list[1]['kj'];
                    }
                }else{
                    $yc = $kj;
                }
                $data1 = array(
                    'num' => $re1['result']['data']['preDrawIssue'] + 1,
                    'yc' => $yc
                );
                Db::name('lottery_test')->insert($data1);
            }
        }
        //
        //echo $re1['result']['data']['preDrawIssue'];
        $list1 =  Db::name('lottery_test1')->order('id desc')->find();
        $list =  Db::name('lottery_test')->order('id desc')->select();
        if($list1){
            if($re1['result']['data']['preDrawIssue'] == $list1['num']) {
                $re2 = explode(',', $re1['result']['data']['preDrawCode']);
                if ($re2[0] > $re2[9]) {
                    if ($list1['yc'] == '龙') {
                        $re3 = 1;
                    } else {
                        $re3 = 0;
                    }
                } else {
                    if ($list1['yc'] == '虎') {
                        $re3 = 1;
                    } else {
                        $re3 = 0;
                    }
                }
                $data = array(
                    'num' => $re1['result']['data']['preDrawIssue'],
                    'content' => $re1['result']['data']['preDrawCode'],
                    'is_win' => $re3
                );
                Db::name('lottery_test1')->where(['id' => $list1['id']])->update($data);
                if($list[2]['is_win'] == $list[1]['is_win']){
                    if($list[1]['is_win'] == 0){
                        if($list[0]['yc'] == '龙'){
                            $yc = '虎';
                        }else{
                            $yc = '龙';
                        }
                    }else{
                        if($list[0]['yc'] == '龙'){
                            $yc = '龙';
                        }else{
                            $yc = '虎';
                        }
                    }
                }else{
                    if($list[1]['is_win'] == 1){
                        if($list[0]['yc'] == '龙'){
                            $yc = '虎';
                        }else{
                            $yc = '龙';
                        }
                    }else{
                        $yc = $list[0]['yc'];
                    }
                }
                $data1 = array(
                    'num' => $re1['result']['data']['preDrawIssue'] + 1,
                    'yc' => $yc
                );
                Db::name('lottery_test1')->insert($data1);
            }
        }else{
            if($list[1]['is_win'] == 1){
                if($list[0]['yc'] == '龙'){
                    $yc = '虎';
                }else{
                    $yc = '龙';
                }
            }else{
                $yc = $list[0]['yc'];
            }
            $data1 = array(
                'num' => $re1['result']['data']['preDrawIssue'] + 1,
                'yc' => $yc
            );
            Db::name('lottery_test1')->insert($data1);
        }
    }
}