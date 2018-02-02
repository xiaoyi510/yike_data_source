<?php
/**
 * Created by PhpStorm.
 * User: 15390
 * Date: 2017/9/5
 * Time: 11:01
 */

namespace app\open\home;

use think\Db;
use think\Controller;

class Monitor extends Controller
{
    public function index()
    {
        $list =  Db::name('api')->where('is_open = 1')->select();
        foreach($list as $k => $v){
            $start_time = strtotime(date('Y/m/d', time()).' '.date('H:i:s',$v['start_time']));
            $end_time = strtotime(date('Y/m/d', time()).' '.date('H:i:s',$v['end_time']));
            if($start_time != $end_time){
                if(time() >= $start_time && time() <= $end_time){
                    $pl = $list =  Db::name('platform')->where('id = '.$v['uid'])->find();
                    $re = file_get_contents($v['link']);
                    $re = json_decode($re,true);
                    if($v['ok_num'] == 0){
                        $data['ok_num'] = $re['data']['result'][0]['num'];
                        Db::name('api')->where('id = '.$v['id'])->update($data);
                    }else{
                        if($re['data']['result'][0]['num'] == $v['ok_num']){
                            $time = $re['data']['result'][0]['time'] + $v['max_interval'] * 60;
                            if(time() > $time){
                                $url = 'http://'.$_SERVER['HTTP_HOST'].'/public/index.php/open/message/send_message/msg/'.$pl['name'].$v['name'].'第'.($v['ok_num'] + 1).'期';
                                file_get_contents($url);
                            }
                        }elseif($re['data']['result'][0]['num'] > $v['ok_num'] + 1){
                            $url = 'http://'.$_SERVER['HTTP_HOST'].'/public/index.php/open/message/send_message/msg/'.$pl['name'].$v['name'].'第'.($v['ok_num'] + 1).'期';
                            file_get_contents($url);
                            $data['ok_num'] = $re['data']['result'][0]['num'];
                            Db::name('api')->where('id = '.$v['id'])->update($data);
                        }elseif($re['data']['result'][0]['num'] == $v['ok_num'] + 1){
                            $data['ok_num'] = $re['data']['result'][0]['num'];
                            Db::name('api')->where('id = '.$v['id'])->update($data);
                        }
                    }
                }
            }else{
                $pl = $list =  Db::name('platform')->where('id = '.$v['uid'])->find();
                $re = file_get_contents($v['link']);
                $re = json_decode($re,true);
                if($v['ok_num'] == 0){
                    $data['ok_num'] = $re['data']['result'][0]['num'];
                    Db::name('api')->where('id = '.$v['id'])->update($data);
                }else{
                    if($re['data']['result'][0]['num'] == $v['ok_num']){
                        $time = $re['data']['result'][0]['time'] + $v['max_interval'] * 60;
                        if(time() > $time){
                            $url = 'http://'.$_SERVER['HTTP_HOST'].'/public/index.php/open/message/send_message/msg/'.$pl['name'].$v['name'].'第'.($v['ok_num'] + 1).'期';
                            file_get_contents($url);
                        }
                    }elseif($re['data']['result'][0]['num'] > $v['ok_num'] + 1){
                        $url = 'http://'.$_SERVER['HTTP_HOST'].'/public/index.php/open/message/send_message/msg/'.$pl['name'].$v['name'].'第'.($v['ok_num'] + 1).'期';
                        file_get_contents($url);
                        $data['ok_num'] = $re['data']['result'][0]['num'];
                        Db::name('api')->where('id = '.$v['id'])->update($data);
                    }elseif($re['data']['result'][0]['num'] == $v['ok_num'] + 1){
                        $data['ok_num'] = $re['data']['result'][0]['num'];
                        Db::name('api')->where('id = '.$v['id'])->update($data);
                    }
                }
            }
        }
    }
}