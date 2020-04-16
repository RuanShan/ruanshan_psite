<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: pl125 <xskjs888@163.com>
// +----------------------------------------------------------------------
namespace api\product\controller;

use api\product\model\ProductPostModel;
use cmf\controller\RestBaseController;
use api\product\model\ProductTagModel;

class IndexController extends RestBaseController
{
    protected $tagModel;

    /**
     * 获取标签列表
     */
    public function index()
    {
        $this->success('请求成功!', "DD");
    }

    /**
     * 获取热门标签列表
     */
    public function hotTags()
    {
        $params                         = $this->request->get();
        $params['where']['recommended'] = 1;
        $data                           = $this->tagModel->getDatas($params);

        if (empty($this->apiVersion) || $this->apiVersion == '1.0.0') {
            $response = $data;
        } else {
            $response = ['list' => $data,];
        }
        $this->success('请求成功!', $response);
    }

    /**
     * 获取标签产品列表
     * @param int $id
     */
    public function items($id)
    {
        if (intval($id) === 0) {
            $this->error('无效的标签id！');
        } else {
            $params    = $this->request->param();
            $postModel = new ProductPostModel();

            unset($params['id']);

            $items = $postModel->paramsFilter($params)->alias('post')
                ->join('__PRODUCT_TAG_POST__ tag_post', 'post.id = tag_post.post_id')
                ->where(['tag_post.tag_id' => $id])->select();

            if (!empty($params['relation'])) {
                $allowedRelations = $postModel->allowedRelations($params['relation']);
                if (!empty($allowedRelations)) {
                    if (count($items) > 0) {
                        $items->load($allowedRelations);
                        $items->append($allowedRelations);
                    }
                }
            }


            $this->success('请求成功!', ['items' => $items]);
        }
    }
}
