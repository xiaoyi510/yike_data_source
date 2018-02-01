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

class Phone extends Admin
{
    // 收信人列表
    public function lists()
    {
        $list =  Db::name('phone')->paginate();
        return ZBuilder::make('table')
            ->setTableName('phone') // 指定数据表名
            ->addColumns([ // 批量添加数据列
                ['name','姓名','text.edit'],
                ['phone','电话','text.edit'],
                ['create_time','创建时间','datetime'],
                ['is_open', '是否开启', 'switch'],
                ['right_button', '操作', 'btn']
            ])
            ->addTopButtons(['add']) // 批量添加顶部按钮
            ->addRightButtons(['delete' => ['data-tips' => '删除后无法恢复。']]) // 批量添加右侧按钮
            ->setRowList($list) // 设置表格数据
            ->addOrder('create_time')
            ->fetch(); // 渲染页面
    }

    //新增
    public function add()
    {
        if ($this->request->isPost()) {
            $post = $this->request->post() ;
            $data = array(
                'name' => $post['name'],
                'phone' => $post['phone'],
                'create_time' => time()
            );
            if (Db::name('phone')->insert($data)) {
                $this->success('添加成功', 'lists');
            } else {
                $this->error('添加失败', 'lists');
            }

        }
        // 显示添加页面
        return ZBuilder::make('form')
            ->setPageTips('如果出现无法添加的情况，可能由于浏览器将本页面当成了广告，请尝试关闭浏览器的广告过滤功能再试。', 'warning')
            ->addFormItems([
                ['text', 'name','姓名'],
                ['text', 'phone', '手机号'],
            ])
            ->fetch();
    }
}