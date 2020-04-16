<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: wuwu <15093565100@163.com>
// +----------------------------------------------------------------------
namespace api\product\controller;

use api\product\model\ProductCategoryModel;
use api\product\service\ProductPostService;
use cmf\controller\RestBaseController;

class ListsController extends RestBaseController
{

    /**
     * 推荐产品列表
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function recommended()
    {
        $param                = $this->request->param();
        $param['recommended'] = true;

        $productPostService = new ProductPostService();
        $items          = $productPostService->postItems($param);
        //是否需要关联模型
        if (!$items->isEmpty()) {
            if (!empty($param['relation'])) {
                $allowedRelations = allowed_relations(['user', 'categories'], $param['relation']);
                if (!empty($allowedRelations)) {
                    $items->load($allowedRelations);
                    $items->append($allowedRelations);
                }
            }
        }
        $this->success('ok', ['list' => $items]);
    }

    /**
     * 分类产品列表
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getCategoryPostLists()
    {
        $categoryId = $this->request->param('category_id', 0, 'intval');

        $productCategoryModel = new  ProductCategoryModel();
        $findCategory        = $productCategoryModel->where('id', $categoryId)->find();

        //分类是否存在
        if (empty($findCategory)) {
            $this->error('分类不存在！');
        }

        $param = $this->request->param();

        $productPostService = new ProductPostService();
        $items          = $productPostService->postItems($param);
        //是否需要关联模型
        if (!$items->isEmpty()) {
            if (!empty($param['relation'])) {
                $allowedRelations = allowed_relations(['user', 'categories'], $param['relation']);
                if (!empty($allowedRelations)) {
                    $items->load($allowedRelations);
                    $items->append($allowedRelations);
                }
            }
        }
        $this->success('ok', ['list' => $items]);
    }

}
