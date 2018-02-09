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
use think\Exception;

class Data extends Controller
{

    public function index(){

        echo 1;
        $this->pk10();
        $this->test();
        echo 2;
        $this->xyft();
        echo 3;
        $this->I15_gd();
        $this->I15_jx();
        $this->I15_sh();
        $this->I15_sd();
        echo 4;
//        $this->test();
        // 定时删除两天以前的数据
        if (strtotime(date('Y-m-d').'3:0:0') > time() && time() < strtotime(date('Y-m-d').'3:1:0')) {
            $time =strtotime('-2 day '. date('Y-m-d'));
            Db::name('data')->where(['opentime' => ['lt', $time]])->delete();
        }
    }

    /**
     * Pk10
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
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

    /**
     * 幸运飞艇
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
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


    /**
     * 幸运28
     * @author HomeGrace
     * 时间 2018年2月7日
     */
    protected function xu28()
    {
        try {
            $re = file_get_contents('http://www.caipiaoapi.com/hall/hallajax/getLotteryinfo?lotKey=xy28');
            $re = json_decode($re, true);
            $re = $re['result']['data'];
            if ($re['preDrawCode']) {
                $list =  Db::name('data')->field('expect')->where(['uid' => 7])->order('id desc')->find();
                if (empty($list) || $list['expect'] != $re['preDrawIssue']) {
                    $data = [
                        'uid'      => 7,
                        'expect'   => $re['preDrawIssue'],
                        'opencode' => $re['preDrawCode'],
                        'opentime' => strtotime($re['preDrawTime']),
                    ];
                    $r = Db::name('data')->insert($data);
                    if ($r) {
                        echo 'xu28:ok<br/>';
                    } else {
                        echo 'xu28:NO<br/>';
                    }
                } else {
                    echo 'xu28:Yes<br/>';
                }
            } else {
                throw new Exception('xu28:NO, Code');
            }
        } catch (\Exception $e) {
            echo $e->getFile().':'.$e->getLine().':'.$e->getMessage().'<br/>';
            try {
                echo '已挂执行第二次抓取:xu28↓<br/>';
                $re = file_get_contents('https://3cp9.com/lottery/trendChart/lotteryOpenNum.do?lotCode=PCEGG');
                $re = json_decode($re, true);
                $re = $re[0];
                if ($re['haoMa']) {
                    $list =  Db::name('data')->field('expect')->where(['uid' => 7])->order('id desc')->find();
                    if (empty($list) || $list['expect'] != $re['qiHao']) {
                        $data = [
                            'uid'      => 7,
                            'expect'   => $re['qiHao'],
                            'opencode' => $re['haoMa'],
                            'opentime' => substr($re['openTime'], 0, -3),
                        ];
                        $r = Db::name('data')->insert($data);
                        if ($r) {
                            echo 'xu28:ok<br/>';
                        } else {
                            echo 'xu28:NO<br/>';
                        }
                    }else {
                        echo 'xu28:Yes<br/>';
                    }
                } else {
                    throw new Exception('xu28:NO, Code');
                }
            } catch (\Exception $e) {
                echo $e->getFile().':'.$e->getLine().':'.$e->getMessage().'<br/>所有数据都挂了:xu28↑<br/>';
            }
        }
    }

    /**
     * 重庆时时彩
     * @author HomeGrace
     * 时间  2018年2月7日
     * 修改时间  2018年2月9日 HomeGrace
     */
    protected function cqSsc()
    {
        try {
            $re = file_get_contents('https://www.7caiapi.com/lottery/3.html');
            preg_match('/<tr class="cq">(.*?)<\/tr>/ism', $re, $result);  // 取得重庆时时彩
            $result = $result[1];

            preg_match('/<td><span.*?id="qs_54">第(.*?)期<\/span><\/td>/ism', $result, $result1); // 取得期数
            $result1 = $result1[1];

            preg_match_all('/<div.*?class=".*?">(.*?)<\/div>/ism', $result, $result2); // 取得开奖号码
            $result2 = join(',', $result2[1]);

            preg_match('/<span class="xs0" title="(.*?)">(.*?)<\/span>/ism', $result, $result3); // 取得开奖时间
            unset($result3[0]);
            $result3 = join(' ', $result3);

            if ($result2) {
                $list =  Db::name('data')->field('expect')->where(['uid' => 8])->order('id desc')->find();
                if (empty($list) || $list['expect'] != $result1) {
                    $data = [
                        'uid'      => 8,
                        'expect'   => $result1,
                        'opencode' => $result2,
                        'opentime' => strtotime($result3),
                    ];
                    $r = Db::name('data')->insert($data);
                    if ($r) {
                        echo 'cqSsc:ok<br/>';
                    } else {
                        echo 'cqSsc:NO<br/>';
                    }
                }else {
                    echo 'cqSsc:Yes<br/>';
                }
            } else {
                throw new Exception('cqSsc:NO, Code');
            }
        } catch (\Exception $e) {
            echo $e->getFile().':'.$e->getLine().':'.$e->getMessage().'<br/>';
            try {
                echo '已挂执行第二次抓取:cqSsc↓<br/>';
                $re = file_get_contents('http://www.caipiaoapi.com/hall/hallajax/getLotteryInfo?lotKey=ssc&lotCode=10002');
                $re = json_decode($re, true);
                $re = $re['result']['data'];
                if ($re['preDrawCode']) {
                    $list =  Db::name('data')->field('expect')->where(['uid' => 8])->order('id desc')->find();
                    if (empty($list) || $list['expect'] != $re['preDrawIssue']) {
                        $data = [
                            'uid'      => 8,
                            'expect'   => $re['preDrawIssue'],
                            'opencode' => $re['preDrawCode'],
                            'opentime' => strtotime($re['preDrawTime']),
                        ];
                        $r = Db::name('data')->insert($data);
                        if ($r) {
                            echo 'cqSsc:ok<br/>';
                        } else {
                            echo 'cqSsc:NO<br/>';
                        }
                    } else {
                        echo 'cqSsc:Yes<br/>';
                    }
                } else {
                    throw new Exception('cqSsc:NO, Code');
                }
            } catch (\Exception $e) {
                echo $e->getFile().':'.$e->getLine().':'.$e->getMessage().'<br/>所有数据都挂了:cqSsc↑<br/>';
            }
        }
    }

    /**
     * 天津时时彩
     * @author HomeGrace
     * 时间 2018年2月9日
     */
    protected function tjSsc()
    {
        try {
            $re = file_get_contents('http://www.caipiaoapi.com/hall/hallajax/getLotteryInfo?lotKey=tjssc&lotCode=10002');
            $re = json_decode($re, true);
            $re = $re['result']['data'];
            if ($re['preDrawCode']) {
                $list =  Db::name('data')->field('expect')->where(['uid' => 9])->order('id desc')->find();
                if (empty($list) || $list['expect'] != $re['preDrawIssue']) {
                    $data = [
                        'uid'      => 9,
                        'expect'   => $re['preDrawIssue'],
                        'opencode' => $re['preDrawCode'],
                        'opentime' => strtotime($re['preDrawTime']),
                    ];
                    $r = Db::name('data')->insert($data);
                    if ($r) {
                        echo 'tjSsc:ok<br/>';
                    } else {
                        echo 'tjSsc:NO<br/>';
                    }
                } else {
                    echo 'tjSsc:Yes<br/>';
                }
            } else {
                throw new Exception('tjSsc:NO, Code');
            }
        } catch (\Exception $e) {
            echo $e->getFile().':'.$e->getLine().':'.$e->getMessage().'<br/>所有数据都挂了:tjSsc↑<br/>';
        }
    }

    /**
     * 新疆时时彩
     * @author HomeGrace
     * 时间 2018年2月9日
     */
    protected function xjSsc()
    {
        try {
            $re = file_get_contents('http://www.caipiaoapi.com/hall/hallajax/getLotteryInfo?lotKey=xjssc&lotCode=10002');
            $re = json_decode($re, true);
            $re = $re['result']['data'];
            if ($re['preDrawCode']) {
                $list =  Db::name('data')->field('expect')->where(['uid' => 10])->order('id desc')->find();
                if (empty($list) || $list['expect'] != $re['preDrawIssue']) {
                    $data = [
                        'uid'      => 10,
                        'expect'   => $re['preDrawIssue'],
                        'opencode' => $re['preDrawCode'],
                        'opentime' => strtotime($re['preDrawTime']),
                    ];
                    $r = Db::name('data')->insert($data);
                    if ($r) {
                        echo 'xjSsc:ok<br/>';
                    } else {
                        echo 'xjSsc:NO<br/>';
                    }
                } else {
                    echo 'xjSsc:Yes<br/>';
                }
            } else {
                throw new Exception('xjSsc:NO, Code');
            }
        } catch (\Exception $e) {
            echo $e->getFile().':'.$e->getLine().':'.$e->getMessage().'<br/>所有数据都挂了:xjSsc↑<br/>';
        }
    }

    /**
     * 云南时时彩
     * @author HomeGrace
     * 时间 2018年2月9日
     */
    protected function ynSsc()
    {
        try {
            $re = file_get_contents('http://www.caipiaoapi.com/hall/hallajax/getLotteryInfo?lotKey=ynssc&lotCode=10002');
            $re = json_decode($re, true);
            $re = $re['result']['data'];
            if ($re['preDrawCode']) {
                $list =  Db::name('data')->field('expect')->where(['uid' => 11])->order('id desc')->find();
                if (empty($list) || $list['expect'] != $re['preDrawIssue']) {
                    $data = [
                        'uid'      => 11,
                        'expect'   => $re['preDrawIssue'],
                        'opencode' => $re['preDrawCode'],
                        'opentime' => strtotime($re['preDrawTime']),
                    ];
                    $r = Db::name('data')->insert($data);
                    if ($r) {
                        echo 'ynSsc:ok<br/>';
                    } else {
                        echo 'ynSsc:NO<br/>';
                    }
                } else {
                    echo 'ynSsc:Yes<br/>';
                }
            } else {
                throw new Exception('ynSsc:NO, Code');
            }
        } catch (\Exception $e) {
            echo $e->getFile().':'.$e->getLine().':'.$e->getMessage().'<br/>所有数据都挂了:ynSsc↑<br/>';
        }
    }

    /**
     * 极速时时彩
     * @author HomeGrace
     * 时间 2018年2月9日
     */
    protected function jsSsc()
    {
        try {
            $re = file_get_contents('http://www.caipiaoapi.com/hall/hallajax/getLotteryInfo?lotKey=jsssc&lotCode=10002');
            $re = json_decode($re, true);
            $re = $re['result']['data'];
            if ($re['preDrawCode']) {
                $list =  Db::name('data')->field('expect')->where(['uid' => 12])->order('id desc')->find();
                if (empty($list) || $list['expect'] != $re['preDrawIssue']) {
                    $data = [
                        'uid'      => 12,
                        'expect'   => $re['preDrawIssue'],
                        'opencode' => $re['preDrawCode'],
                        'opentime' => strtotime($re['preDrawTime']),
                    ];
                    $r = Db::name('data')->insert($data);
                    if ($r) {
                        echo 'jsSsc:ok<br/>';
                    } else {
                        echo 'jsSsc:NO<br/>';
                    }
                } else {
                    echo 'jsSsc:Yes<br/>';
                }
            } else {
                throw new Exception('jsSsc:NO, Code');
            }
        } catch (\Exception $e) {
            echo $e->getFile().':'.$e->getLine().':'.$e->getMessage().'<br/>所有数据都挂了:jsSsc↑<br/>';
        }
    }



    //浙江11选五
    protected function I15_zj(){
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