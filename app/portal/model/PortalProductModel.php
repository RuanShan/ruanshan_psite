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
namespace app\portal\model;

use app\admin\model\RouteModel;
use think\Model;
use think\Db;
use app\portal\model\PortalPostModel;

/**
 * @property mixed id
 */
class PortalProductModel extends PortalPostModel
{
    protected $name = 'portal_post';

    // 定义全局的查询范围
    protected function base($query)
    {
        $query->where('post_type',3);
    }

}
