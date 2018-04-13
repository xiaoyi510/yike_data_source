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
        echo 2;
        $this->xyft();
        echo 3;
        $this->I15_gd();
        $this->I15_jx();
        $this->I15_sh();
        $this->I15_sd();
        $this->I15_hl();
        $this->I15_hb();
        $this->I15_gz();
        $this->I15_gs();
        $this->I15_fj();
        $this->I15_sx();
        $this->I15_ah();
        $this->I15_js();
        echo 4;
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
        try {
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
                    if ($r) {
                        echo 'I15_gd:ok<br/>';
                    } else {
                        echo 'I15_gd:NO<br/>';
                    }
                } else {
                    echo 'I15_gd:Yes<br/>';
                }
            } else {
                throw new Exception('I15_gd:NO, Code');
            }
        } catch (\Exception $e) {
            echo $e->getFile().':'.$e->getLine().':'.$e->getMessage().'<br/>所有数据都挂了:I15_gd↑<br/>';
        }
    }

    //江西11选五
    private function I15_jx(){
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
    private function I15_sh(){
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

    /**
     * 山东11选五
     * @author HomeGrace
     * 时间 2018年4月13日
     */
    private function I15_sd()
    {
        try {
            $re = $this->curl("http://www.cpbao.com/sdel11to5/scheme!getServicerTime.action", []);
            $re = json_decode($re);
            $re = $re->resultList[0];
            if ($re) {
                $list =  Db::name('data')->field('expect')->where(['uid' => 6])->order('id desc')->find();
                if(empty($list) || $list['expect'] != $re->periodNumber){
                    $data = array(
                        'uid'      => 6,
                        'expect'   => $re->periodNumber,
                        'opencode' => $re->result,
                        'opentime' => strtotime($re->prizeTime)
                    );
                    $r = Db::name('data')->insert($data);
                    if ($r) {
                        echo 'I15_sd:ok<br/>';
                    } else {
                        echo 'I15_sd:NO<br/>';
                    }
                } else {
                    echo 'I15_sd:Yes<br/>';
                }
            } else {
                throw new Exception('I15_sd:NO, Code');
            }
        } catch (\Exception $e) {
            echo $e->getFile().':'.$e->getLine().':'.$e->getMessage().'<br/>所有数据都挂了:I15_sd↑<br/>';
        }
    }

    /**
     * 黑龙江 11 选五
     * @author HomeGrace
     */
    private function I15_hl()
    {
        try {
            $re = file_get_contents('http://pub.icaile.com/hlj11x5kjjg.php');
            preg_match('/<td class="nth-child-1">(.*?)<\/td>/ims', $re, $a);
            preg_match('/<td class="nth-child-2">(.*?)<\/td>/ims', $re, $a1);
            preg_match('/<td class="nth-child-3">(.*?)<\/td>/ims', $re, $a2);
            preg_match_all('/<em.*?>(.*?)<\/em>/', $a2[1], $a3);
            if (!empty($a3[1])) {
                $list =  Db::name('data')->field('expect')->where(['uid' => 13])->order('id desc')->find();
                if (empty($list) || $list['expect'] != $a[1]) {
                    $data = [
                        'uid'      => 13,
                        'expect'   => $a[1],
                        'opencode' => join(',', $a3[1]),
                        'opentime' => strtotime($a1[1]),
                    ];
                    $r = Db::name('data')->insert($data);
                    if ($r) {
                        echo 'I15_hl:ok<br/>';
                    } else {
                        echo 'I15_hl:NO<br/>';
                    }
                } else {
                    echo 'I15_hl:Yes<br/>';
                }
            }
        } catch (\Exception $e) {
            echo $e->getFile().':'.$e->getLine().':'.$e->getMessage().'<br/>所有数据都挂了:I15_hl↑<br/>';
        }
    }

    /**
     * 河北 11 选五
     * @author HomeGrace
     */
    private function I15_hb()
    {
        try {
            $re = file_get_contents('http://pub.icaile.com/heb11x5kjjg.php');
            preg_match('/<td class="nth-child-1">(.*?)<\/td>/ims', $re, $a);
            preg_match('/<td class="nth-child-2">(.*?)<\/td>/ims', $re, $a1);
            preg_match('/<td class="nth-child-3">(.*?)<\/td>/ims', $re, $a2);
            preg_match_all('/<em.*?>(.*?)<\/em>/', $a2[1], $a3);
            if (!empty($a3[1])) {
                $list =  Db::name('data')->field('expect')->where(['uid' => 14])->order('id desc')->find();
                if (empty($list) || $list['expect'] != $a[1]) {
                    $data = [
                        'uid'      => 14,
                        'expect'   => $a[1],
                        'opencode' => join(',', $a3[1]),
                        'opentime' => strtotime($a1[1]),
                    ];
                    $r = Db::name('data')->insert($data);
                    if ($r) {
                        echo 'I15_hb:ok<br/>';
                    } else {
                        echo 'I15_hb:NO<br/>';
                    }
                } else {
                    echo 'I15_hb:Yes<br/>';
                }
            }
        } catch (\Exception $e) {
            echo $e->getFile().':'.$e->getLine().':'.$e->getMessage().'<br/>所有数据都挂了:I15_hb↑<br/>';
        }
    }

    /**
     * 贵州 11 选五
     * @author HomeGrace
     */
    private function I15_gz()
    {
        try {
            $re = file_get_contents('http://pub.icaile.com/gz11x5kjjg.php');
            preg_match('/<td class="nth-child-1">(.*?)<\/td>/ims', $re, $a);
            preg_match('/<td class="nth-child-2">(.*?)<\/td>/ims', $re, $a1);
            preg_match('/<td class="nth-child-3">(.*?)<\/td>/ims', $re, $a2);
            preg_match_all('/<em.*?>(.*?)<\/em>/', $a2[1], $a3);
            if (!empty($a3[1])) {
                $list =  Db::name('data')->field('expect')->where(['uid' => 15])->order('id desc')->find();
                if (empty($list) || $list['expect'] != $a[1]) {
                    $data = [
                        'uid'      => 15,
                        'expect'   => $a[1],
                        'opencode' => join(',', $a3[1]),
                        'opentime' => strtotime($a1[1]),
                    ];
                    $r = Db::name('data')->insert($data);
                    if ($r) {
                        echo 'I15_gz:ok<br/>';
                    } else {
                        echo 'I15_gz:NO<br/>';
                    }
                } else {
                    echo 'I15_gz:Yes<br/>';
                }
            }
        } catch (\Exception $e) {
            echo $e->getFile().':'.$e->getLine().':'.$e->getMessage().'<br/>所有数据都挂了:I15_gz↑<br/>';
        }
    }

    /**
     * 甘肃 11 选五
     * @author HomeGrace
     */
    private function I15_gs()
    {
        try {
            $re = file_get_contents('http://pub.icaile.com/gs11x5kjjg.php');
            preg_match('/<td class="nth-child-1">(.*?)<\/td>/ims', $re, $a);
            preg_match('/<td class="nth-child-2">(.*?)<\/td>/ims', $re, $a1);
            preg_match('/<td class="nth-child-3">(.*?)<\/td>/ims', $re, $a2);
            preg_match_all('/<em.*?>(.*?)<\/em>/', $a2[1], $a3);
            if (!empty($a3[1])) {
                $list =  Db::name('data')->field('expect')->where(['uid' => 16])->order('id desc')->find();
                if (empty($list) || $list['expect'] != $a[1]) {
                    $data = [
                        'uid'      => 16,
                        'expect'   => $a[1],
                        'opencode' => join(',', $a3[1]),
                        'opentime' => strtotime($a1[1]),
                    ];
                    $r = Db::name('data')->insert($data);
                    if ($r) {
                        echo 'I15_gs:ok<br/>';
                    } else {
                        echo 'I15_gs:NO<br/>';
                    }
                } else {
                    echo 'I15_gs:Yes<br/>';
                }
            }
        } catch (\Exception $e) {
            echo $e->getFile().':'.$e->getLine().':'.$e->getMessage().'<br/>所有数据都挂了:I15_gs↑<br/>';
        }
    }

    /**
     * 福建 11 选五
     * @author HomeGrace
     */
    private function I15_fj()
    {
        try {
            $re = file_get_contents('http://pub.icaile.com/fj11x5kjjg.php');
            preg_match('/<td class="nth-child-1">(.*?)<\/td>/ims', $re, $a);
            preg_match('/<td class="nth-child-2">(.*?)<\/td>/ims', $re, $a1);
            preg_match('/<td class="nth-child-3">(.*?)<\/td>/ims', $re, $a2);
            preg_match_all('/<em.*?>(.*?)<\/em>/', $a2[1], $a3);
            if (!empty($a3[1])) {
                $list =  Db::name('data')->field('expect')->where(['uid' => 17])->order('id desc')->find();
                if (empty($list) || $list['expect'] != $a[1]) {
                    $data = [
                        'uid'      => 17,
                        'expect'   => $a[1],
                        'opencode' => join(',', $a3[1]),
                        'opentime' => strtotime($a1[1]),
                    ];
                    $r = Db::name('data')->insert($data);
                    if ($r) {
                        echo 'I15_fj:ok<br/>';
                    } else {
                        echo 'I15_fj:NO<br/>';
                    }
                } else {
                    echo 'I15_fj:Yes<br/>';
                }
            }
        } catch (\Exception $e) {
            echo $e->getFile().':'.$e->getLine().':'.$e->getMessage().'<br/>所有数据都挂了:I15_fj↑<br/>';
        }
    }

    /**
     * 山西 11 选五
     * @author HomeGrace
     */
    private function I15_sx()
    {
        try {
            $re = file_get_contents('http://pub.icaile.com/sx11x5kjjg.php');
            preg_match('/<td class="nth-child-1">(.*?)<\/td>/ims', $re, $a);
            preg_match('/<td class="nth-child-2">(.*?)<\/td>/ims', $re, $a1);
            preg_match('/<td class="nth-child-3">(.*?)<\/td>/ims', $re, $a2);
            preg_match_all('/<em.*?>(.*?)<\/em>/', $a2[1], $a3);
            if (!empty($a3[1])) {
                $list =  Db::name('data')->field('expect')->where(['uid' => 18])->order('id desc')->find();
                if (empty($list) || $list['expect'] != $a[1]) {
                    $data = [
                        'uid'      => 18,
                        'expect'   => $a[1],
                        'opencode' => join(',', $a3[1]),
                        'opentime' => strtotime($a1[1]),
                    ];
                    $r = Db::name('data')->insert($data);
                    if ($r) {
                        echo 'I15_sx:ok<br/>';
                    } else {
                        echo 'I15_sx:NO<br/>';
                    }
                } else {
                    echo 'I15_sx:Yes<br/>';
                }
            }
        } catch (\Exception $e) {
            echo $e->getFile().':'.$e->getLine().':'.$e->getMessage().'<br/>所有数据都挂了:I15_sx↑<br/>';
        }
    }

    /**
     * 安徽 11 选五
     * @author HomeGrace
     * 时间 2018 年 4 月 13 日
     */
    private function I15_ah()
    {
        try {
            $re = $this->curl('https://api.api68.com/ElevenFive/getElevenFiveInfo.do?lotCode=10017', []);
            $re = json_decode($re);
            $re = $re->result->data;
            if ($re) {
                $list =  Db::name('data')->field('expect')->where(['uid' => 19])->order('id desc')->find();
                if(empty($list) || $list['expect'] != $re->preDrawIssue){
                    $data = array(
                        'uid'      => 19,
                        'expect'   => $re->preDrawIssue,
                        'opencode' => $re->preDrawCode,
                        'opentime' => strtotime($re->preDrawTime)
                    );
                    $r = Db::name('data')->insert($data);
                    if ($r) {
                        echo 'I15_ah:ok<br/>';
                    } else {
                        echo 'I15_ah:NO<br/>';
                    }
                } else {
                    echo 'I15_ah:Yes<br/>';
                }
            } else {
                throw new \Exception('I15_ah:NO, Code');
            }
        } catch (\Exception $e) {
            echo $e->getFile().':'.$e->getLine().':'.$e->getMessage().'<br/>所有数据都挂了:I15_ah↑<br/>';
        }
    }

    /**
     * 江苏 11 选五
     * @author HomeGrace
     * 时间 2018 年 4 月 13 日
     */
    private function I15_js()
    {
        try {
            $re = $this->curl('https://api.api68.com/ElevenFive/getElevenFiveInfo.do?lotCode=10015', []);
            $re = json_decode($re);
            $re = $re->result->data;
            if ($re) {
                $list =  Db::name('data')->field('expect')->where(['uid' => 20])->order('id desc')->find();
                if(empty($list) || $list['expect'] != $re->preDrawIssue){
                    $data = array(
                        'uid'      => 20,
                        'expect'   => $re->preDrawIssue,
                        'opencode' => $re->preDrawCode,
                        'opentime' => strtotime($re->preDrawTime)
                    );
                    $r = Db::name('data')->insert($data);
                    if ($r) {
                        echo 'I15_js:ok<br/>';
                    } else {
                        echo 'I15_js:NO<br/>';
                    }
                } else {
                    echo 'I15_js:Yes<br/>';
                }
            } else {
                throw new \Exception('I15_js:NO, Code');
            }
        } catch (\Exception $e) {
            echo $e->getFile().':'.$e->getLine().':'.$e->getMessage().'<br/>所有数据都挂了:I15_js↑<br/>';
        }
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





    
    
    
    

    

    /**
     * 抓取数据  https 或 http 形式
     * @param $url    链接
     * @param $data   参数
     * @return mixed  返回数据
     */
    private function curl($url, $data)
    {
        set_time_limit(0);
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