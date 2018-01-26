<?php
/**
 * Created by PhpStorm.
 * User: monster
 * Date: 2016/10/9
 * Time: 10:36
 */
use \Workerman\Worker;
use \Workerman\Lib\Timer;
require_once './Workerman/Autoloader.php';

function get($url){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($curl);
    curl_close($curl);
    return $result;
}

function data(){
    $url = 'http://date.yike1908.com/public/index.php/open/data';
    file_get_contents($url);
    echo date('Y-m-d H:i:s')."\n";
}

$task = new Worker();
// 开启多少个进程运行定时任务，注意多进程并发问题
$task->count = 1;
$task->onWorkerStart = function($task)
{
    // 每1秒执行一次
    $time_interval = 10;
    Timer::add($time_interval, 'data', array());
};

// 运行worker
Worker::runAll();