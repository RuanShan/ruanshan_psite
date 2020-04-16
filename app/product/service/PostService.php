<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2019 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 小夏 < 449134904@qq.com>
// +----------------------------------------------------------------------
namespace app\product\service;

use app\product\model\ProductPostModel;
use think\db\Query;

class PostService
{
    /**
     * 产品查询
     * @param $filter
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public function adminItemList($filter)
    {
        return $this->adminPostList($filter);
    }

    /**
     * 页面产品列表
     * @param $filter
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public function adminPageList($filter)
    {
        return $this->adminPostList($filter, true);
    }

    /**
     * 产品查询
     * @param      $filter
     * @param bool $isPage
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public function adminPostList($filter, $isPage = false)
    {

        $join = [
            ['__USER__ u', 'a.user_id = u.id']
        ];

        $field = 'a.*,u.user_login,u.user_nickname,u.user_email';

        $category = empty($filter['category']) ? 0 : intval($filter['category']);
        if (!empty($category)) {
            array_push($join, [
                '__PRODUCT_CATEGORY_POST__ b', 'a.id = b.post_id'
            ]);
            $field = 'a.*,b.id AS post_category_id,b.list_order,b.category_id,u.user_login,u.user_nickname,u.user_email';
        }

        $productPostModel = new ProductPostModel();
        $items        = $productPostModel->alias('a')->field($field)
            ->join($join)
            ->where('a.create_time', '>=', 0)
            ->where('a.delete_time', 0)
            ->where(function (Query $query) use ($filter, $isPage) {

                $category = empty($filter['category']) ? 0 : intval($filter['category']);
                if (!empty($category)) {
                    $query->where('b.category_id', $category);
                }

                $startTime = empty($filter['start_time']) ? 0 : strtotime($filter['start_time']);
                $endTime   = empty($filter['end_time']) ? 0 : strtotime($filter['end_time']);
                if (!empty($startTime)) {
                    $query->where('a.published_time', '>=', $startTime);
                }
                if (!empty($endTime)) {
                    $query->where('a.published_time', '<=', $endTime);
                }

                $keyword = empty($filter['keyword']) ? '' : $filter['keyword'];
                if (!empty($keyword)) {
                    $query->where('a.post_title', 'like', "%$keyword%");
                }

                if ($isPage) {
                    $query->where('a.post_type', 2);
                } else {
                    $query->where('a.post_type', 1);
                }
            })
            ->order('update_time', 'DESC')
            ->paginate(10);

        return $items;

    }

    /**
     * 已发布产品查询
     * @param  int $postId     产品id
     * @param int  $categoryId 分类id
     * @return array|string|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function publishedItem($postId, $categoryId = 0)
    {
        $productPostModel = new ProductPostModel();

        if (empty($categoryId)) {

            $where = [
                'post.post_type'   => 1,
                'post.post_status' => 1,
                'post.delete_time' => 0,
                'post.id'          => $postId
            ];

            $item = $productPostModel->alias('post')->field('post.*')
                ->where($where)
                ->where('post.published_time', ['< time', time()], ['> time', 0], 'and')
                ->find();
        } else {
            $where = [
                'post.post_type'       => 1,
                'post.post_status'     => 1,
                'post.delete_time'     => 0,
                'relation.category_id' => $categoryId,
                'relation.post_id'     => $postId
            ];

            $join    = [
                ['__PRODUCT_CATEGORY_POST__ relation', 'post.id = relation.post_id']
            ];
            $item = $productPostModel->alias('post')->field('post.*')
                ->join($join)
                ->where($where)
                ->where('post.published_time', ['< time', time()], ['> time', 0], 'and')
                ->find();
        }


        return $item;
    }

    /**
     * 上一篇产品
     * @param int $postId     产品id
     * @param int $categoryId 分类id
     * @return array|string|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function publishedPrevItem($postId, $categoryId = 0)
    {
        $productPostModel = new ProductPostModel();

        if (empty($categoryId)) {

            $where = [
                'post.post_type'   => 1,
                'post.post_status' => 1,
                'post.delete_time' => 0,
            ];

            $item = $productPostModel
                ->alias('post')
                ->field('post.*')
                ->where($where)
                ->where('post.id', '<', $postId)
                ->where('post.published_time', ['< time', time()], ['> time', 0], 'and')
                ->order('id', 'DESC')
                ->find();

        } else {
            $where = [
                'post.post_type'       => 1,
                'post.post_status'     => 1,
                'post.delete_time'     => 0,
                'relation.category_id' => $categoryId,
            ];

            $join    = [
                ['__PRODUCT_CATEGORY_POST__ relation', 'post.id = relation.post_id']
            ];
            $item = $productPostModel
                ->alias('post')
                ->field('post.*')
                ->join($join)
                ->where($where)
                ->where('relation.post_id', '<', $postId)
                ->where('post.published_time', ['< time', time()], ['> time', 0], 'and')
                ->order('id', 'DESC')
                ->find();
        }


        return $item;
    }

    /**
     * 下一篇产品
     * @param int $postId     产品id
     * @param int $categoryId 分类id
     * @return array|string|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function publishedNextItem($postId, $categoryId = 0)
    {
        $productPostModel = new ProductPostModel();

        if (empty($categoryId)) {

            $where = [
                'post.post_type'   => 1,
                'post.post_status' => 1,
                'post.delete_time' => 0,
            ];

            $item = $productPostModel->alias('post')->field('post.*')
                ->where($where)
                ->where('post.id', '>', $postId)
                ->where('post.published_time', ['< time', time()], ['> time', 0], 'and')
                ->order('id', 'ASC')
                ->find();
        } else {
            $where = [
                'post.post_type'       => 1,
                'post.post_status'     => 1,
                'post.delete_time'     => 0,
                'relation.category_id' => $categoryId,

            ];

            $join    = [
                ['__PRODUCT_CATEGORY_POST__ relation', 'post.id = relation.post_id']
            ];
            $item = $productPostModel->alias('post')->field('post.*')
                ->join($join)
                ->where($where)
                ->where('relation.post_id', '>', $postId)
                ->where('post.published_time', ['< time', time()], ['> time', 0], 'and')
                ->order('id', 'ASC')
                ->find();
        }


        return $item;
    }

    /**
     * 页面管理查询
     * @param int $pageId 产品id
     * @return array|string|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function publishedPage($pageId)
    {

        $where = [
            'post_type'   => 2,
            'post_status' => 1,
            'delete_time' => 0,
            'id'          => $pageId
        ];

        $productPostModel = new ProductPostModel();
        $page            = $productPostModel
            ->where($where)
            ->where('published_time', ['< time', time()], ['> time', 0], 'and')
            ->find();

        return $page;
    }

}