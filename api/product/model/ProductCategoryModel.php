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

class ProductCategoryModel extends Model
{
    //类型转换
    protected $type = [
        'more' => 'array',
    ];


    //模型关联方法
    protected $relationFilter = ['items'];


    /**
     * more 自动转化
     * @param $value
     * @return array
     */
    public function getMoreAttr($value)
    {
        $more = json_decode($value, true);
        if (!empty($more['thumbnail'])) {
            $more['thumbnail'] = cmf_get_image_url($more['thumbnail']);
        }

        if (!empty($more['photos'])) {
            foreach ($more['photos'] as $key => $value) {
                $more['photos'][$key]['url'] = cmf_get_image_url($value['url']);
            }
        }
        return $more;
    }

    /**
     * 关联产品表
     * @return \think\model\relation\BelongsToMany
     */
    public function items()
    {
        return $this->belongsToMany('ProductPostModel', 'product_category_post', 'post_id', 'category_id');
    }

    /**
     * 关联产品分类和产品表
     * @return \think\model\relation\HasMany
     */
    public function PostIds()
    {
        return $this->hasMany('ProductCategoryPostModel', 'category_id', 'id');
    }

    /**
     * 此类产品id数组
     * @param string $category_id 分类di
     * @return array|string|Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function categoryPostIds($category_id)
    {
        $ids      = [];
        $post_ids = self::relation('PostIds')->field(true)->where('id', $category_id)->find();
        foreach ($post_ids['PostIds'] as $key => $id) {
            $ids[] = $id['post_id'];
        }
        $post_ids['PostIds'] = $ids;
        return $post_ids;
    }
}
