<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: pl125 <xskjs888@163.com>
// +----------------------------------------------------------------------

namespace api\product\controller;

use api\product\service\ProductPostService;
use api\user\model\UserFavoriteModel;
use api\user\model\UserLikeModel;
use cmf\controller\RestBaseController;
use api\product\model\ProductPostModel;
use think\Db;

class ItemsController extends RestBaseController
{
    /**
     * 产品列表
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $params      = $this->request->get();
        $postService = new ProductPostService();
        $data        = $postService->postItems($params);
        //是否需要关联模型
        if (!$data->isEmpty()) {
            if (!empty($params['relation'])) {

                $allowedRelations = allowed_relations(['user', 'categories'], $params['relation']);

                if (!empty($allowedRelations)) {
                    $data->load('user');
                    $data->append($allowedRelations);
                }
            }
        }
        if (empty($this->apiVersion) || $this->apiVersion == '1.0.0') {
            $response = $data;
        } else {
            $response = ['list' => $data];
        }
        $this->success('请求成功!', $response);
    }

    /**
     * 获取指定的产品
     * @param $id
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function read($id)
    {
        if (intval($id) === 0) {
            $this->error('无效的产品id！');
        } else {
            $postModel = new ProductPostModel();
            $data      = $postModel->where('id', $id)->find();

            if (empty($data)) {
                $this->error('产品不存在！');
            } else {
                $postModel->where('id', $id)->setInc('post_hits');
                $url         = cmf_url('product/Item/index', ['id' => $id, 'cid' => $data['categories'][0]['id']], true, true);
                $data['url'] = $url;
                $this->success('请求成功!', $data);
            }

        }
    }

    /**
     * 我的产品列表
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function my()
    {
        $params            = $this->request->get();
        $params['user_id'] = $this->getUserId();

        $postService = new ProductPostService();
        $data        = $postService->postItems($params);

        if (empty($this->apiVersion) || $this->apiVersion == '1.0.0') {
            $response = [$data];
        } else {
            $response = ['list' => $data];
        }

        $this->success('请求成功!', $response);
    }

    /**
     * 添加产品
     * @throws \think\Exception
     */
    public function save()
    {
        $data            = $this->request->post();
        $data['user_id'] = $this->getUserId();
        $result          = $this->validate($data, 'Items.item');
        if ($result !== true) {
            $this->error($result);
        }

        if (empty($data['published_time'])) {
            $data['published_time'] = time();
        }
        $postModel = new ProductPostModel();
        $postModel->addItem($data);
        $this->success('添加成功！');
    }

    /**
     * 更新产品
     * @param $id
     * @throws \think\Exception
     */
    public function update($id)
    {
        $data   = $this->request->put();
        $result = $this->validate($data, 'Items.item');
        if ($result !== true) {
            $this->error($result);
        }
        $postModel = new ProductPostModel();
        $res       = $postModel->itemFind(['id' => $id, 'user_id' => $this->getUserId()]);
        if (empty($res)) {
            $this->error('产品不存在或者已经删除！');
        }

        $result = $postModel->editItem($data, $id, $this->getUserId());

        if ($result === false) {
            $this->error('编辑失败！');
        } else {
            $this->success('编辑成功！');
        }
    }

    /**
     * 删除产品
     * @param $id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function delete($id)
    {
        if (empty($id)) {
            $this->error('无效的产品id');
        }
        $postModel = new ProductPostModel();
        $result    = $postModel->deleteItem($id, $this->getUserId());
        if ($result == -1) {
            $this->error('产品已删除');
        }
        if ($result) {
            $this->success('删除成功！');
        } else {
            $this->error('删除失败！');
        }
    }

    /**
     * 批量删除产品
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function deletes()
    {
        $ids = $this->request->post('ids/a');
        if (empty($ids)) {
            $this->error('产品id不能为空');
        }
        $postModel = new ProductPostModel();
        $result    = $postModel->deleteItem($ids, $this->getUserId());
        if ($result == -1) {
            $this->error('产品已删除');
        }
        if ($result) {
            $this->success('删除成功！');
        } else {
            $this->error('删除失败！');
        }
    }

    /**
     * 搜索查询
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function search()
    {
        $params = $this->request->get();

        if (!empty($params['keyword'])) {
            $postService = new ProductPostService();
            $data        = $postService->postItems($params);

            if (empty($this->apiVersion) || $this->apiVersion == '1.0.0') {
                $response = $data;
            } else {
                $response = ['list' => $data,];
            }
            $this->success('请求成功!', $response);
        } else {
            $this->error('搜索关键词不能为空！');
        }

    }

    /**
     * 产品点赞
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function doLike()
    {
        $userId    = $this->getUserId();
        $itemId = $this->request->param('id', 0, 'intval');

        $userLikeModel = new UserLikeModel();

        $findLikeCount = $userLikeModel->where([
            'user_id'   => $userId,
            'object_id' => $itemId
        ])->where('table_name', 'product_post')->count();

        if (empty($findLikeCount)) {
            $postModel = new ProductPostModel();
            $item   = $postModel->where('id', $itemId)->field('id,post_title,post_excerpt,more')->find();

            if (empty($item)) {
                $this->error('产品不存在！');
            }

            Db::startTrans();
            try {
                $postModel->where(['id' => $itemId])->setInc('post_like');
                $thumbnail = empty($item['more']['thumbnail']) ? '' : $item['more']['thumbnail'];
                $userLikeModel->insert([
                    'user_id'     => $userId,
                    'object_id'   => $itemId,
                    'table_name'  => 'product_post',
                    'title'       => $item['post_title'],
                    'thumbnail'   => $thumbnail,
                    'description' => $item['post_excerpt'],
                    'url'         => json_encode(['action' => 'product/Item/index', 'param' => ['id' => $itemId, 'cid' => $item['categories'][0]['id']]]),
                    'create_time' => time()
                ]);
                Db::commit();
            } catch (\Exception $e) {
                Db::rollback();
                $this->error('点赞失败！');
            }

            $likeCount = $postModel->where('id', $itemId)->value('post_like');
            $this->success("赞好啦！", ['post_like' => $likeCount]);
        } else {
            $this->error("您已赞过啦！");
        }
    }

    /**
     * 取消产品点赞
     */
    public function cancelLike()
    {
        $userId = $this->getUserId();

        $itemId = $this->request->param('id', 0, 'intval');

        $userLikeModel = new UserLikeModel();

        $findLikeCount = $userLikeModel->where([
            'user_id'   => $userId,
            'object_id' => $itemId
        ])->where('table_name', 'product_post')->count();

        if (!empty($findLikeCount)) {
            $postModel = new ProductPostModel();
            Db::startTrans();
            try {
                $postModel->where(['id' => $itemId])->setDec('post_like');
                $userLikeModel->where([
                    'user_id'   => $userId,
                    'object_id' => $itemId
                ])->where('table_name', 'product_post')->delete();
                Db::commit();
            } catch (\Exception $e) {
                Db::rollback();
                $this->error('取消点赞失败！');
            }

            $likeCount = $postModel->where('id', $itemId)->value('post_like');
            $this->success("取消点赞成功！", ['post_like' => $likeCount]);
        } else {
            $this->error("您还没赞过！");
        }
    }

    /**
     * 产品收藏
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function doFavorite()
    {
        $userId = $this->getUserId();

        $itemId = $this->request->param('id', 0, 'intval');

        $userFavoriteModel = new UserFavoriteModel();

        $findFavoriteCount = $userFavoriteModel->where([
            'user_id'   => $userId,
            'object_id' => $itemId
        ])->where('table_name', 'product_post')->count();

        if (empty($findFavoriteCount)) {
            $postModel = new ProductPostModel();
            $item   = $postModel->where(['id' => $itemId])->field('id,post_title,post_excerpt,more')->find();
            if (empty($item)) {
                $this->error('产品不存在！');
            }

            Db::startTrans();
            try {
                $postModel->where(['id' => $itemId])->setInc('post_favorites');
                $thumbnail = empty($item['more']['thumbnail']) ? '' : $item['more']['thumbnail'];
                $userFavoriteModel->insert([
                    'user_id'     => $userId,
                    'object_id'   => $itemId,
                    'table_name'  => 'product_post',
                    'thumbnail'   => $thumbnail,
                    'title'       => $item['post_title'],
                    'description' => $item['post_excerpt'],
                    'url'         => json_encode(['action' => 'product/Item/index', 'param' => ['id' => $itemId, 'cid' => $item['categories'][0]['id']]]),
                    'create_time' => time()
                ]);
                Db::commit();
            } catch (\Exception $e) {
                Db::rollback();

                $this->error('收藏失败！');
            }

            $favoriteCount = $postModel->where('id', $itemId)->value('post_favorites');
            $this->success("收藏好啦！", ['post_favorites' => $favoriteCount]);
        } else {
            $this->error("您已收藏过啦！");
        }
    }

    /**
     * 取消产品收藏
     */
    public function cancelFavorite()
    {
        $userId = $this->getUserId();

        $itemId = $this->request->param('id', 0, 'intval');

        $userFavoriteModel = new UserFavoriteModel();

        $findFavoriteCount = $userFavoriteModel->where([
            'user_id'   => $userId,
            'object_id' => $itemId
        ])->where('table_name', 'product_post')->count();

        if (!empty($findFavoriteCount)) {
            $postModel = new ProductPostModel();
            Db::startTrans();
            try {
                $postModel->where(['id' => $itemId])->setDec('post_favorites');
                $userFavoriteModel->where([
                    'user_id'   => $userId,
                    'object_id' => $itemId
                ])->where('table_name', 'product_post')->delete();
                Db::commit();
            } catch (\Exception $e) {
                Db::rollback();
                $this->error('取消失败！');
            }

            $favoriteCount = $postModel->where('id', $itemId)->value('post_favorites');
            $this->success("取消成功！", ['post_favorites' => $favoriteCount]);
        } else {
            $this->error("您还没收藏过！");
        }
    }


    /**
     * 相关产品列表
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function relatedItems()
    {
        $itemId  = $this->request->param('id', 0, 'intval');
        $categoryId = Db::name('product_category_post')->where('post_id', $itemId)->value('category_id');

        $postModel = new ProductPostModel();
        $items  = $postModel
            ->alias('post')
            ->join('__PRODUCT_CATEGORY_POST__ category_post', 'post.id=category_post.post_id')
            ->where(['post.delete_time' => 0, 'post.post_status' => 1, 'category_post.category_id' => $categoryId])
            ->orderRaw('rand()')
            ->limit(5)
            ->select();
        if ($items->isEmpty()){
            $this->error('没有相关产品！');
        }
        $this->success('success', ['list' => $items]);
    }
}