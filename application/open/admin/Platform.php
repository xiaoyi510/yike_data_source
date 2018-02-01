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

class Platform extends Admin
{
    // 接入平台列表
    public function lists()
    {
        $list =  Db::name('platform')->paginate();
        return ZBuilder::make('table')
            ->setTableName('platform') // 指定数据表名
            ->addColumns([ // 批量添加数据列
                ['name','平台名','text.edit'],
                ['create_time','创建时间','datetime'],
                ['right_button', '操作', 'btn']
            ])
            ->addTopButtons(['add']) // 批量添加顶部按钮
            ->addRightButtons(['edit'=>['title' => '监控接口管理','href'=>url('api_lists', ['id'=>'__id__', 'name' => '__name__'])],'delete' => ['data-tips' => '删除后无法恢复。']]) // 批量添加右侧按钮
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
                'create_time' => time()
            );
            if (Db::name('platform')->insert($data)) {
                $this->success('添加成功', 'lists');
            } else {
                $this->error('添加失败', 'lists');
            }

        }
        // 显示添加页面
        return ZBuilder::make('form')
            ->setPageTips('如果出现无法添加的情况，可能由于浏览器将本页面当成了广告，请尝试关闭浏览器的广告过滤功能再试。', 'warning')
            ->addFormItems([
                ['text', 'name','平台名'],
            ])
            ->fetch();
    }

    public function api_lists()
    {
        $data = input();
        $list =  Db::name('api')->where('uid='.$data['id'])->paginate();
        $name = '监控接口列表（'.$data['name'].'）';
        return ZBuilder::make('table')
            ->setPageTitle($name)
            ->setTableName('api') // 指定数据表名
            ->addColumns([ // 批量添加数据列
                ['name','接口名','text.edit'],
                ['link','链接','text.edit'],
                ['start_time','开始时间','time.edit'],
                ['end_time','结束时间','time.edit'],
                ['max_interval','最大开奖间隔','text.edit'],
                ['create_time','创建时间','datetime'],
                ['is_open', '是否开启监控', 'switch'],
                ['right_button', '操作', 'btn']
            ])
            ->addTopButtons(['add'=>['href'=>url('api_add', ['id'=>$data['id'], 'name' => $data['name']])]]) // 批量添加顶部按钮
            ->addRightButtons(['delete' => ['data-tips' => '删除后无法恢复。']]) // 批量添加右侧按钮
            ->setRowList($list) // 设置表格数据
            ->addOrder('create_time')
            ->fetch(); // 渲染页面
    }

    //新增
    public function api_add()
    {
        $data = input();
        if ($this->request->isPost()) {
            $post = $this->request->post() ;
            $data = array(
                'uid' => $post['uid'],
                'name' => $post['name'],
                'link' => $post['link'],
                'start_time' => strtotime($post['start_time']),
                'end_time' => strtotime($post['end_time']),
                'max_interval' => $post['max_interval'],
                'create_time' => time()
            );
            if (Db::name('api')->insert($data)) {
                $this->success('添加成功', url('api_lists', ['id'=>$post['uid'], 'name' => $post['uname']]));
            } else {
                $this->error('添加失败', url('api_lists', ['id'=>$post['uid'], 'name' => $post['uname']]));
            }

        }
        // 显示添加页面
        return ZBuilder::make('form')
            ->setPageTips('如果出现无法添加的情况，可能由于浏览器将本页面当成了广告，请尝试关闭浏览器的广告过滤功能再试。', 'warning')
            ->addFormItems([
                ['hidden', 'uid',$data['id']],
                ['text', 'uname','平台名','',$data['name'],'','readonly'],
                ['text', 'name','接口名'],
                ['text', 'link','链接'],
                ['time', 'start_time','开始时间','每日开奖起始时间'],
                ['time', 'end_time','结束时间','每日开奖结束时间'],
                ['text:4', 'max_interval', '允许的最大开奖间隔', '', '', ['','分'], '', ''],
            ])
            ->fetch();
    }
}