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

function data()
{
    $url = 'http://date.yike1908.com/public/index.php/open/data';
    file_get_contents($url);
    echo date('Y-m-d H:i:s')."\n";
}

function data1()
{
    $url = 'http://date.yike1908.com/public/index.php/open/data1';
    $result = file_get_contents($url);
    echo $result.date('Y-m-d H:i:s')."\n";
}

$task = new Worker();
// 开启多少个进程运行定时任务，注意多进程并发问题
$task->count = 1;
$task->onWorkerStart = function($task)
{
//    Timer::add(10, 'data', array());
//    Timer::add(10, 'data1', array());
    Timer::add(5, function () {
        $url = 'http://yike_data_source.net/index.php/open/data/msPk10';
        file_get_contents($url);
        echo date('Y-m-d H:i:s')."\n";
    }, array());
};

// 运行worker
Worker::runAll();