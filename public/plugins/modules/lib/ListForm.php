<?php
// +----------------------------------------------------------------------
// | 内容展示列表-控件构造-中间件
// +----------------------------------------------------------------------
namespace plugins\modules\lib;

class ListForm{

    protected static $instance = NULL;

    /**
     * 初始化数据
     * @param $fieldName 主要用于获取字段的配置名称
     * @return ListForm|null
     */
    public static function init($fieldName=[], $control_edit=[]){
        if (self::$instance == NULL) {
            self::$instance = new self();//实例化到静态属性
            if($fieldName)      self::$instance->fieldName = $fieldName;
            if($control_edit && is_array($control_edit))   self::$instance->control_edit = array_column($control_edit, null, 'name');
        }

        return self::$instance;
    }

    /**
     * -将用于赋值
     */
    public static function addData($content=[]) {
        if (self::$instance != NULL) {
            self::$instance->content = $content;
        }
    }


    /**
     * 标题输出
     * @param $tag
     * @param $table
     */
    public static function startTitle($tag, $table)
    {
        $tag['alias'] = $tag['alias'] ?: (self::$instance->fieldName[$table][$tag['name']]?:$tag['name']);//别名
        $tag['width'] = empty($tag['width']) ? 'auto;' : (ctype_digit($tag['width']) ? $tag['width'] . 'px;' : $tag['width']);//显示width

        echo <<<parse
            <th width="{$tag['width']}"> {$tag['alias']} </th>
parse;
    }


    /**
     * 统一调用函数
     * @param $tag  配置
     * @param $val 当前行内容数组
     */
    public static function start($tag){
        //自动识别
        if(empty($tag['type'])){
            $tag['type']   = isset(self::$instance->control_edit[$tag['name']]['type']) ? self::$instance->control_edit[$tag['name']]['type'] : '';
            if(empty($tag['config'])){
                $tag['config'] = isset(self::$instance->control_edit[$tag['name']]['config'])? self::$instance->control_edit[$tag['name']]['config']: '';
            }
        }
        $tag['value']  = isset(self::$instance->content[$tag['name']]) ? self::$instance->content[$tag['name']] : '';//值

        switch($tag['type']){
            case 'text':
                self::$instance->text($tag);
                break;
            case 'ctime':
                self::$instance->ctime($tag);
                break;
            case 'img':
                self::$instance->img($tag);
                break;
            case 'href':
                self::$instance->href($tag);
                break;
            case 'category':
                self::$instance->category($tag);
                break;
            case 'admin_id':
                self::$instance->admin_name($tag);
                break;
            default:
                self::$instance->text($tag);
                break;
        }

    }

    /**
     * 调用分类
     * @param 同输入框
     * @update 2018-04-02
     */
    public  function category($tag){
        if(!isset(self::$instance->category)){
            self::$instance->category = \plugins\modules\model\PluginModulesColumnModel::where(["modules_id" => input('modules_id')])->column('name','id');
        }
        $value = isset(self::$instance->category[$tag['value']]) ? self::$instance->category[$tag['value']] : $tag['value'];
        echo <<<parse
        <td>{$value}</td>
parse;
    }


    /**
     * 常规文本
     * @param $tag [name=>'',type=>'',width=>'']
     * @param value 默认值/字段值
     * @param desc 说明名称
     * @param name 字段名称
     * @param width 显示宽度
     * @param config 配置
     */
    public function text($tag){
        if(isset($tag['config']) && !empty($tag['config']) ||
            $tag['config'] = isset(self::$instance->control_edit[$tag['name']]['config'])? self::$instance->control_edit[$tag['name']]['config']: ''){
            if(strpos($tag['config'],',')){
                $tag['config'] = explode(',', $tag['config']);
            }elseif(strpos($tag['config'],'|')){
                $tag['config'] = explode('|', $tag['config']);
            }
            if(is_array($tag['config'])){
                if($tag['type']=='checkbox'){
                    $value_key = explode(',',$tag['value']);
                    $tag['value'] = [];
                    foreach($tag['config'] as $v){
                        if (!strpos($v, ':')) {
                            $key = $val = str_replace(['\'','\"'],'',$v);;
                        } else {
                            list($key, $val) = explode(':', $v);
                        }
                        if(is_array($value_key) && in_array($key, $value_key)){
                            $tag['value'][] = $val;
                        }
                    }
                    $tag['value'] = implode(', ', $tag['value']);
                }else{
                    foreach($tag['config'] as $v){
                        if (!strpos($v, ':')) {
                            $key = $val = str_replace(['\'','\"'],'',$v);;
                        } else {
                            list($key, $val) = explode(':', $v);
                        }
                        if($tag['value']==$key){
                            $tag['value'] = $val;break;
                        }
                    }
                }
            }
        }
        echo <<<parse
        <td>{$tag['value']}</td>
parse;
    }


    /**
     * 时间转换
     */
    public function ctime($tag){
        if(isset($tag['value'])){
            if(is_numeric($tag['value'])){
                if(!empty($tag['config'])){
                    $tag['value'] = date($tag['config'], $tag['value'] );
                }else{
                    $tag['value'] = date('Y-m-d H:i', $tag['value']);
                }
            }
        }
        echo <<<parse
        <td>{$tag['value']}</td>
parse;
    }


    /**
     * 图片展示
     */
    public function img($tag){
        if(!empty($tag['config'])){
            $src = str_replace('['.$tag['name'].']', $tag['value'], $tag['config']);
        }else{
            if(!isset(self::$instance->imgpath)){
                self::$instance->imgpath = config('IMGPATH');
            }
            $src = self::$instance->imgpath . $tag['value'];
        }
        echo <<<parse
        <td><a href="{$src}"><img src='{$src}' width='{$tag['width']}'></a></td>
parse;
    }



    /**
     * url链接
     */
    public function href($tag){
        if (!empty($tag['config'])) {
            $href = $tag['config'];
            preg_match_all('/\[([a-zA-Z0-9_-]+)\]/', $tag['config'], $matches);
            if(!empty($matches[1])){
                foreach($matches[1] as $field){
                    if($fieldValue  = isset(self::$instance->content[$field]) ? self::$instance->content[$field] : ''){//取设置字段值
                        $href = str_replace('['.$field.']', $fieldValue, $href);
                    }
                }
            }
        } else {
            $href = '#';
        }

        echo <<<parse
        <td><a target="_blank"  href="{$href}">{$tag['value']}</td>
parse;
    }

    /**
     * 后台用户管理员 获取 真实名称
     * @param $tag
     */
    public  function admin_name($tag){
        if(!isset(self::$instance->admin_name)){
            $alluser = \think\Db::name('user')->column('user_nickname','id');
            self::$instance->admin_name = $alluser;
        }
        $value = isset(self::$instance->admin_name[$tag['value']]) ? self::$instance->admin_name[$tag['value']] : $tag['value'];
        echo <<<parse
        <td>{$value}</td>
parse;
    }

}