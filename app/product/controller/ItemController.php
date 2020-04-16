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
namespace app\product\controller;

use cmf\controller\HomeBaseController;
use app\product\model\ProductCategoryModel;
use app\product\service\PostService;
use app\product\model\ProductPostModel;
use think\Db;

class ItemController extends HomeBaseController
{
    /**
     * 产品详情
     * @return mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {

        $productCategoryModel = new ProductCategoryModel();
        $postService         = new PostService();

        $itemId  = $this->request->param('id', 0, 'intval');
        $categoryId = $this->request->param('cid', 0, 'intval');
        $item    = $postService->publishedItem($itemId, $categoryId);

        if (empty($item)) {
            abort(404, '产品不存在!');
        }


        $prevItem = $postService->publishedPrevItem($itemId, $categoryId);
        $nextItem = $postService->publishedNextItem($itemId, $categoryId);

        $tplName = 'item';

        if (empty($categoryId)) {
            $categories = $item['categories'];

            if (count($categories) > 0) {
                $this->assign('category', $categories[0]);
            } else {
                abort(404, '产品未指定分类!');
            }

        } else {
            $category = $productCategoryModel->where('id', $categoryId)->where('status', 1)->find();

            if (empty($category)) {
                abort(404, '产品不存在!');
            }

            $this->assign('category', $category);

            $tplName = empty($category["one_tpl"]) ? $tplName : $category["one_tpl"];
        }

        Db::name('product_post')->where('id', $itemId)->setInc('post_hits');


        hook('product_before_assign_item', $item);

        $this->assign('item', $item);
        $this->assign('prev_item', $prevItem);
        $this->assign('next_item', $nextItem);

        $tplName = empty($item['more']['template']) ? $tplName : $item['more']['template'];

        return $this->fetch("/$tplName");
    }

    // 产品点赞
    public function doLike()
    {
        $this->checkUserLogin();
        $itemId = $this->request->param('id', 0, 'intval');


        $canLike = cmf_check_user_action("posts$itemId", 1);

        if ($canLike) {
            Db::name('product_post')->where('id', $itemId)->setInc('post_like');

            $this->success("赞好啦！");
        } else {
            $this->error("您已赞过啦！");
        }
    }

}
