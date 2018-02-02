<?php
/**
 * Created by PhpStorm.
 * User: 15390
 * Date: 2017/8/29
 * Time: 17:00
 */

namespace app\open\admin;

use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use think\Db;

class Log extends Admin
{
    // log列表
    public function lists()
    {
        $list =  Db::name('log')->paginate();
        return ZBuilder::make('table')
            ->setTableName('log') // 指定数据表名
            ->addColumns([ // 批量添加数据列
                ['name','操作名','text'],
                ['is_ok','发送是否成功','status','',['0'=>'失败', '1'=>'成功']],
                ['reason','备注','text'],
                ['create_time','创建时间','datetime'],
                ['right_button', '操作', 'btn']
            ])
            ->addRightButtons(['delete' => ['data-tips' => '删除后无法恢复。']]) // 批量添加右侧按钮
            ->setRowList($list) // 设置表格数据
            ->addOrder('create_time')
            ->fetch(); // 渲染页面
    }
}