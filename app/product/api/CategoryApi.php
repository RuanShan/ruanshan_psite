<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2019 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 老猫 <thinkcmf@126.com>
// +----------------------------------------------------------------------
namespace app\product\api;

use app\product\model\ProductCategoryModel;
use think\db\Query;

class CategoryApi
{
    /**
     * 分类列表 用于模板设计
     * @param array $param
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index($param = [])
    {
        $productCategoryModel = new ProductCategoryModel();

        $where = ['delete_time' => 0];

        //返回的数据必须是数据集或数组,item里必须包括id,name,如果想表示层级关系请加上 parent_id
        return $productCategoryModel->where($where)
            ->where(function (Query $query) use ($param) {
                if (!empty($param['keyword'])) {
                    $query->where('name', 'like', "%{$param['keyword']}%");
                }
            })->select();
    }

    /**
     * 分类列表 用于导航选择
     * @return array
     */
    public function nav()
    {
        $productCategoryModel = new ProductCategoryModel();

        $where = ['delete_time' => 0];

        $categories = $productCategoryModel->where($where)->select();

        $return = [
            //'name'  => '产品分类',
            'rule'  => [
                'action' => 'product/List/index',
                'param'  => [
                    'id' => 'id'
                ]
            ],//url规则
            'items' => $categories //每个子项item里必须包括id,name,如果想表示层级关系请加上 parent_id
        ];

        return $return;
    }

}