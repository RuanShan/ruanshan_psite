<?php
/**
 * Copyright (c) 2013-2017 http://www.syousoft.com All rights reserved.
 * Author: WelkinVan(welkinvan@qq.com)
 * Date: 6/6/2017
 * Time: 11:18 PM
 */
namespace plugins\guestbook\controller;

use cmf\controller\PluginBaseController;
use plugins\guestbook\Model\PluginGuestbookModel;

class IndexController extends PluginBaseController
{


    /**
     * 提交留言
     */
    public function addMsg()
    {
        // 获取post数据
        $data = $this->request->param();
        // 数据验证
        $result = $this->validate($data, 'Guestbook');
        if ($result !== true) {
            $this->error($result);
        }
        // 验证码校验
        if (!cmf_captcha_check($data['captcha'])) {
            $this->error('验证码错误');
        }
        // 实例化模型
        $GuestbookModel = new PluginGuestbookModel();
        $GuestbookModel->allowField(true)->data($data, true)->save();

        $this->success('提交成功！');
    }

}
