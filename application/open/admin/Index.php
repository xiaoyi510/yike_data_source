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

class Index extends Admin
{
    // 彩种管理
    public function type()
    {
        $list =  Db::name('type')->order('id desc')->select();
        return ZBuilder::make('table')
            ->setTableName('type') // 指定数据表名
            ->addColumns([ // 批量添加数据列
                ['uid','彩种id'],
                ['name','彩种名','text.edit'],
                ['is_open', '是否开启', 'switch'],
            ])
            ->setRowList($list) // 设置表格数据
            ->fetch(); // 渲染页面
    }

    // 开奖源管理
    public function lists($uid = 1)
    {
        $list_type  = Db::name('type')->order('id asc')->select();
        foreach($list_type as $k => $v){
            $list_tab[$v['uid']] = array(
                'title' => $v['name'],
                'url' => url('lists', ['$uid' => $v['uid'] ])
            );
        }
        $list =  Db::name('data')->where('uid = '.$uid)->order('id desc')->paginate();
        return ZBuilder::make('table')
            ->setTableName('list')
            ->setTabNav($list_tab, $uid)
            ->addColumns([ // 批量添加数据列
                ['expect','期号'],
                ['opencode','开奖号'],
                ['opentime','开奖时间','datetime'],
            ])
            ->setRowList($list) // 设置表格数据
            ->fetch(); // 渲染模板
    }
}