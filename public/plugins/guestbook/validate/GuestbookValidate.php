<?php

/**
 * Copyright (c) 2013-2017 http://www.syousoft.com All rights reserved.
 * Author: WelkinVan(welkinvan@qq.com)
 * Date: 6/6/2017
 * Time: 11:18 PM
 */
namespace plugins\guestbook\validate;
use think\Validate;

class GuestbookValidate extends Validate
{
    protected $rule = [
        'name' => 'require',
        'tel' => 'require',
        'email' => 'email',
        'subject' => 'require',
        'message' => 'require',
    ];
    protected $message = [
        'name.require' => '您为填写姓名',
        'tel.require' => '联系方式未填写',
        'email.email' => '邮箱格式错误',
        'subject.require' => '标题未填写',
        'message.require' => '留言内容不能为空',
    ];
}