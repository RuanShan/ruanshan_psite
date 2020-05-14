<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: pl125 <xskjs888@163.com>
// +----------------------------------------------------------------------

namespace api\demo\controller;
use api\demo\model\WebtemplateModel;
use cmf\controller\RestBaseController;
use Overtrue\Pinyin\Pinyin;


class IndexController extends RestBaseController
{
    public function index()
    {
        $this->success('请求成功!', ['test'=>'test']);
    }


    public function webtemplates()
    {

      $templates = WebtemplateModel::all();
      foreach ($templates as $key => $template) {
        // code...
        $this->generateTemplateYaml($template);
      }
      $response = ['list' => $templates];
        $this->success('请求成功!', $response);
    }


    function generateTemplateYaml($template){
      $pinyin = new Pinyin();

      $dir = CMF_DATA."templates";
      if( !file_exists($dir)){
        mkdir( $dir );
      }
      $filename = $template->publish_time;
      $path = $dir.DIRECTORY_SEPARATOR.$filename."-".$pinyin->permalink($template->title).'.md';
      $file = fopen($path, "w");

      $data= <<<EOF
title: $template->title
subtitle: $template->title2
cover: $template->img_url
taxons: $template->type1 $template->type3
number: $template->number
author:
  nick: 青桔单车
  github_name: qingjue
date: 2020-04-20 12:00:00
publishDate: $template->publish_time
link: $template->access_url
---
EOF;

      fwrite( $file, $data );
      fclose($file);
    }
}
