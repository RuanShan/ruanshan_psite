<?php
/**
 * Copyright (c) 2013-2017 http://www.syousoft.com All rights reserved.
 * Author: WelkinVan(welkinvan@qq.com)
 * Date: 6/6/2017
 * Time: 11:18 PM
 */
namespace plugins\guestbook;//留言板插件
use cmf\lib\Plugin;


class GuestbookPlugin extends Plugin
{

    public $info = [
        'name'        => 'Guestbook',
        'title'       => '留言板',
        'description' => '网站留言板',
        'status'      => 1,
        'author'      => 'UpStream',
        'version'     => '1.0',
        'demo_url'    => 'http://www.syousoft.com',
        'author_url'  => 'http://www.syousoft.com'
    ];

    public $hasAdmin = 1;//插件是否有后台管理界面

    // 插件安装
    public function install()
    {
        return true;//安装成功返回true，失败false
    }

    // 插件卸载
    public function uninstall()
    {
        return true;//卸载成功返回true，失败false
    }

    //实现的guestbook钩子方法
    public function guestbook($param)
    {
        $config = $this->getConfig();

        $this->assign($config);
        return $this->fetch('widget');
    }
    //实现的before_body_end钩子方法挂载js代码
    public function beforeHeadEnd($param)
    {
        echo $this->fetch('css');
    }
    //实现的before_body_end钩子方法挂载js代码
    public function beforeBodyEnd($param)
    {
        echo $this->fetch('js');
    }

}