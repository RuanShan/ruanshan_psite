<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: wuwu <15093565100@163.com>
// +----------------------------------------------------------------------
namespace api\product\controller;

use api\product\service\ProductPostService;
use cmf\controller\RestBaseController;

class UserController extends RestBaseController
{
    /**
     * 会员产品列表
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function items()
    {
        $userId = $this->request->param('user_id', 0, 'intval');

        if (empty($userId)) {
            $this->error('用户id不能空！');
        }

        $param             = $this->request->param();
        $param['user_id']  = $userId;
        $productPostService = new ProductPostService();
        $items          = $productPostService->postItems($param);
        if ($items->isEmpty()) {
            $this->error('没有数据');
        } else {
            $this->success('ok', ['list' => $items]);
        }
    }

}
