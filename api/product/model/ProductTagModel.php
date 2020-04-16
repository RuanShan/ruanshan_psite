<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: pl125 <xskjs888@163.com>
// +----------------------------------------------------------------------

namespace api\product\model;

use think\Model;

class ProductTagModel extends Model
{

    /**
     * 关联 产品表
     * @return \think\model\relation\BelongsToMany
     */
    public function items()
    {
        return $this->belongsToMany('ProductPostModel','product_tag_post','post_id','tag_id')->alias('post');
    }
}
