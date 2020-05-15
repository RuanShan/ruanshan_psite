<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2018 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 現在 <244100330@qq.com>
// +----------------------------------------------------------------------
namespace plugins\modules;
use cmf\lib\Plugin;
use think\db;

class ModulesPlugin extends Plugin
{

    public $info = [
        'name'        => 'Modules',
        'title'       => '内容模型',
        'description' => '内容模型',
        'status'      => 1,
        'author'      => 'LI',
        'version'     => '1.4',
        'demo_url'    => 'http://demo.thinkcmf.com',
        'author_url'  => 'http://www.thinkcmf.com'
    ];

    public $hasAdmin = 1;//插件是否有后台管理界面

    // 插件安装
    public function install()
    {
        $prefix  = config('database.prefix');
        $charset = config('database.charset');
        $sqlArr  = cmf_split_sql($this->getPluginPath(). '/data/modules.sql', $prefix, $charset);
        foreach($sqlArr as $sql){
            db::execute($sql);
        }
        return true;//安装成功返回true，失败false
    }

    // 插件卸载
    public function uninstall()
    {
        $prefix = config('database.prefix');

        $sql = "DROP TABLE IF EXISTS `{$prefix}plugin_modules`;";
        db::execute($sql);
        $sql = "DROP TABLE IF EXISTS `{$prefix}plugin_modules_tables`; ";
        db::execute($sql);
        $sql = "DROP TABLE IF EXISTS `{$prefix}plugin_modules_citys`; ";
        db::execute($sql);
        $sql = "DROP TABLE IF EXISTS `{$prefix}plugin_modules_column`; ";
        db::execute($sql);
        return true;//卸载成功返回true，失败false
    }


}