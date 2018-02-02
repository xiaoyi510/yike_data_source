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
        $this->test();
        echo 2;
        $this->xyft();
        echo 3;
        $this->I15_gd(); //11选五
        $this->I15_jx(); //11选五
        $this->I15_sh(); //11选五
        $this->I15_sd(); //11选五
        echo 4;
//        $this->test();
    }
    private function pk10(){
        $re = file_get_contents('http://e.apiplus.net/newly.do?token=t15e58a225c64f432k&code=bjpk10&format=json');
        $re1 = json_decode($re, true);
        if ($re1['data'][0]['opencode']) {
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

    //广东11选五
    private function I15_gd(){
        $re = file_get_contents('http://www.gdlottery.cn/odata/zst11xuan5.jspx');
        $r1 ='/<span style="width:140px">.*?<strong>(.*?)<\/strong><\/span>/ism';//取开奖号码
        $r2 ='/<td height="20" align="center" bgcolor="#FFFFFF">(.*?)<\/td>/ism';//取期数
        preg_match_all($r1, $re,$da1);
        preg_match_all($r2, $re,$da2);
        $da1 = trim(end($da1[1]));
        $da2 = trim(end($da2[1]));
        $da1 = explode('，',$da1);
        $da1 = implode(',', $da1);
        if ($da1) {
            $list =  Db::name('data')->field('expect')->where(['uid' => 3])->order('id desc')->find();
            if(empty($list) || $list['expect'] != $da2){
                $data = array(
                    'uid' => 3,
                    'expect' => $da2,
                    'opencode' => $da1,
                    'opentime' => time()
                );
                $r = Db::name('data')->insert($data);
            }
        }
    }

    //江西11选五
    public function I15_jx(){
        $re = file_get_contents('http://www.jxlottery.cn/dlc.php');
        $r1 ='/<table border="0" align="center" cellpadding="0" cellspacing="0">.*?<tr>(.*?)<\/tr>.*?<\/table>/ism';//取开奖号码
        $r11 = '/<td class="kj_hm">(.*?)<\/td>/ism';
        $r2 ='/<td align="center" bgcolor="#FFFFFF">(.*?)<\/td>/ism';//取期数
        $r3 ='/<td height="25" align="center" bgcolor="#FFFFFF">(.*?)<\/td>/ism';//取开奖时间
        preg_match_all($r1, $re,$da1);
        preg_match_all($r11, end($da1[1]),$da11);
        preg_match_all($r2, $re,$da2);
        preg_match_all($r3, $re,$da3);
        $da1 = implode(',', $da11[1]);
        $da2 = trim(end($da2[1]));
        $da3 = strtotime(trim(end($da3[1])));
        if ($da1) {
            $list =  Db::name('data')->field('expect')->where(['uid' => 4])->order('id desc')->find();
            if(empty($list) || $list['expect'] != $da2){
                $data = array(
                    'uid' => 4,
                    'expect' => $da2,
                    'opencode' => $da1,
                    'opentime' => $da3
                );
                $r = Db::name('data')->insert($data);
            }
        }
    }

    //上海11选五
    public function I15_sh(){
        $re = file_get_contents('http://caipiao.gooooal.com/shtc!bc115.action?ln=2018013018');

        $r1 ='/<td align="center" bgcolor="#fff6c2" class="red2">(.*?)<\/td>/ism';//取开奖号码
        $r2 ='/<td align="center" bgcolor="#fff6c2">(.*?)<\/td>/ism';//取期数
        preg_match_all($r1, $re,$da1);
        preg_match_all($r2, $re,$da2);
        $da1 = $da1[1][0];
        $da2 = $da2[1][0];
        if ($da1) {
            $list =  Db::name('data')->field('expect')->where(['uid' => 5])->order('id desc')->find();
            if(empty($list) || $list['expect'] != $da2){
                $data = array(
                    'uid' => 5,
                    'expect' => $da2,
                    'opencode' => $da1,
                    'opentime' => time()
                );
                $r = Db::name('data')->insert($data);
            }
        }
    }

    //山东11选五
    private function I15_sd(){
        $re = file_get_contents('https://pgkai.com/index.php?c=api2&a=getOthers&_=0.8742121351843366');
        $re = json_decode($re, true);
        $da1 = $re['list'][14]['c_r'];
        $da2 = $re['list'][14]['c_t'];
        $da3 = strtotime($re['list'][14]['c_d']);
        if ($da1) {
            $list =  Db::name('data')->field('expect')->where(['uid' => 6])->order('id desc')->find();
            if(empty($list) || $list['expect'] != $da2){
                $data = array(
                    'uid' => 6,
                    'expect' => $da2,
                    'opencode' => $da1,
                    'opentime' => $da3
                );
                $r = Db::name('data')->insert($data);
            }
        }
    }

    private function test(){
        $re = file_get_contents('http://date.yike1908.com/public/index.php/open/datalist/index/id/1');
        $re1 = json_decode($re, true);
        $list =  Db::name('lottery_test')->order('id desc')->select();
        if(empty($list)){
            $re2 = explode(',',$re1['data'][0]['opencode']);
            if($re2[0] > $re2[9]){
                $kj = '龙';
                $re3 = 1;
            }else{
                $kj = '虎';
                $re3 = 0;
            }
            $data = array(
                'num' => $re1['data'][0]['expect'],
                'content' => $re1['data'][0]['opencode'],
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
                'num' => $re1['data'][0]['expect'] + 1,
                'yc' => $yc
            );
            Db::name('lottery_test')->insert($data1);
        }else{
            if($re1['data'][0]['expect'] >= $list[0]['num']){
                $re2 = explode(',',$re1['data'][0]['opencode']);
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
                    'num' => $re1['data'][0]['expect'],
                    'content' => $re1['data'][0]['opencode'],
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
                    'num' => $re1['data'][0]['expect'] + 1,
                    'yc' => $yc
                );
                Db::name('lottery_test')->insert($data1);
            }
        }

        $list =  Db::name('lottery_test1')->order('id desc')->select();
        if(empty($list)){
            $re2 = explode(',',$re1['data'][0]['opencode']);
            if($re2[0] > $re2[9]){
                $kj = '龙';
                $re3 = 1;
            }else{
                $kj = '虎';
                $re3 = 0;
            }
            $data = array(
                'num' => $re1['data'][0]['expect'],
                'content' => $re1['data'][0]['opencode'],
                'yc' => '龙',
                'is_win' => $re3,
                'kj' => $kj
            );
            Db::name('lottery_test1')->insert($data);
            if($re3 ==  1){
                $yc = '龙';
            }else{
                $yc = '虎';
            }
            $data1 = array(
                'num' => $re1['data'][0]['expect'] + 1,
                'yc' => $yc
            );
            Db::name('lottery_test1')->insert($data1);
        }else{
            if($re1['data'][0]['expect'] >= $list[0]['num']){
                $re2 = explode(',',$re1['data'][0]['opencode']);
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
                    'num' => $re1['data'][0]['expect'],
                    'content' => $re1['data'][0]['opencode'],
                    'is_win' => $re3,
                    'kj' => $kj
                );
                Db::name('lottery_test1')->where(['id'=>$list[0]['id']])->update($data);
                if(count($list) > 2){
                    if($list[1]['kj'] == $kj){
                        $yc = $list[0]['yc'];
                    }elseif($list[1]['kj'] != $kj && $list[1]['kj'] == $list[2]['kj']){
                        $yc = $kj;
                    }else{
                        $yc = $list[1]['kj'];
                    }
                }else{
                    $yc = $kj;
                }
                $data1 = array(
                    'num' => $re1['data'][0]['expect'] + 1,
                    'yc' => $yc
                );
                Db::name('lottery_test1')->insert($data1);
            }
        }
        //
        //echo $re1['data']['data']['expect'];
//        $list1 =  Db::name('lottery_test1')->order('id desc')->find();
//        $list =  Db::name('lottery_test')->order('id desc')->select();
//        if($list1){
//            if($re1['data'][0]['expect'] >= $list1['num']) {
//                $re2 = explode(',', $re1['data'][0]['opencode']);
//                if ($re2[0] > $re2[9]) {
//                    if ($list1['yc'] == '龙') {
//                        $re3 = 1;
//                    } else {
//                        $re3 = 0;
//                    }
//                } else {
//                    if ($list1['yc'] == '虎') {
//                        $re3 = 1;
//                    } else {
//                        $re3 = 0;
//                    }
//                }
//                $data = array(
//                    'num' => $re1['data'][0]['expect'],
//                    'content' => $re1['data'][0]['opencode'],
//                    'is_win' => $re3
//                );
//                Db::name('lottery_test1')->where(['id' => $list1['id']])->update($data);
//                if($list[2]['is_win'] == $list[1]['is_win']){
//                    if($list[1]['is_win'] == 0){
//                        if($list[0]['yc'] == '龙'){
//                            $yc = '虎';
//                        }else{
//                            $yc = '龙';
//                        }
//                    }else{
//                        if($list[0]['yc'] == '龙'){
//                            $yc = '龙';
//                        }else{
//                            $yc = '虎';
//                        }
//                    }
//                }else{
//                    if($list[1]['is_win'] == 1){
//                        if($list[0]['yc'] == '龙'){
//                            $yc = '虎';
//                        }else{
//                            $yc = '龙';
//                        }
//                    }else{
//                        $yc = $list[0]['yc'];
//                    }
//                }
//                $data1 = array(
//                    'num' => $re1['data'][0]['expect'] + 1,
//                    'yc' => $yc
//                );
//                Db::name('lottery_test1')->insert($data1);
//            }
//        }else{
//            if($list[1]['is_win'] == 1){
//                if($list[0]['yc'] == '龙'){
//                    $yc = '虎';
//                }else{
//                    $yc = '龙';
//                }
//            }else{
//                $yc = $list[0]['yc'];
//            }
//            $data1 = array(
//                'num' => $re1['data'][0]['expect'] + 1,
//                'yc' => $yc
//            );
//            Db::name('lottery_test1')->insert($data1);
//        }
    }







    //浙江11选五
    public function I15_zj(){
        $re = file_get_contents('https://www.zjlottery.com/zsfx2/?flag=Z');
        var_dump($re);exit;
//        $r1 ='/<td align="center" bgcolor="#fff6c2" class="red2">(.*?)<\/td>/ism';//取开奖号码
//        $r2 ='/<td align="center" bgcolor="#fff6c2">(.*?)<\/td>/ism';//取期数
//        preg_match_all($r1, $re,$da1);
//        preg_match_all($r2, $re,$da2);
//        $da1 = $da1[1][0];
//        $da2 = $da2[1][0];
//        if ($da1) {
//            $list =  Db::name('data')->field('expect')->where(['uid' => 5])->order('id desc')->find();
//            if(empty($list) || $list['expect'] != $da2){
//                $data = array(
//                    'uid' => 5,
//                    'expect' => $da2,
//                    'opencode' => $da1,
//                    'opentime' => time()
//                );
//                $r = Db::name('data')->insert($data);
//            }
//        }
    }



}