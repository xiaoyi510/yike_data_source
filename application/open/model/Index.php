<?php
/**
 * Created by PhpStorm.
 * User: 15390
 * Date: 2017/8/31
 * Time: 11:22
 */

namespace app\zc\model;
use think\Model as ThinkModel;

class Index extends ThinkModel
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__LOTTERY_USER__';
    // 自动写入时间戳
    protected $autoWriteTimestamp = true;
}