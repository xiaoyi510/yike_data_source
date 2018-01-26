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


    private function I15()
    {

    }









    /**
     * 抓取数据  https 或 http 形式
     * @param $url    链接
     * @param $data   参数
     * @return mixed  返回数据
     */
    private function curlS($url, $data)
    {
        $UserAgent = 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; SLCC1; .NET CLR 2.0.50727; .NET CLR 3.0.04506; .NET CLR 3.5.21022; .NET CLR 1.0.3705; .NET CLR 1.1.4322)';
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); // 从证书中检查SSL加密算法是否存在
        curl_setopt($curl, CURLOPT_USERAGENT, $UserAgent/*$_SERVER['HTTP_USER_AGENT']*/); // 模拟用户使用的浏览器

        if (ini_get('open_basedir') == '' && ini_get('safe_mode' == 'Off')) {

            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);

        }
        //curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
        curl_setopt($curl, CURLOPT_TIMEOUT, 200); // 设置超时限制防止死循环
        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        $tmpInfo = curl_exec($curl); // 执行操作
        if (curl_errno($curl)) {
            echo 'Errno'.curl_error($curl);//捕抓异常
        }
        curl_close($curl); // 关闭CURL会话
        return $tmpInfo; // 返回数据
    }
}