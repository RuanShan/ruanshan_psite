<?php
// +----------------------------------------------------------------------
// | 内容查询-控件构造-中间件
// +----------------------------------------------------------------------
namespace plugins\modules\lib;

class SearchForm{

    protected static $instance = NULL;

    /**
     * 初始化数据
     * @return SearchForm|null
     */
    public static function init($fieldName=[], $content=[], $control_edit=[]){
        if (self::$instance == NULL) {
            self::$instance = new self();//实例化到静态属性
            self::$instance->onConstruct($fieldName, $content, $control_edit);
        }

        return self::$instance;
    }

    /**
     * -将用于初始赋值
     */
    public function onConstruct($fieldName=[], $content=[], $control_edit=[]) {
        $this->fieldName    = $fieldName;
        $this->content      = $content;
        $this->control_edit =  is_array($control_edit) ? array_column($control_edit, null, 'name') : [];
    }


    /**
     * 统一调用函数
     * @param $tag
     * @param $table
     */
    public static function start($tag, $table){
        $type   = $tag['type'];
        $tag['alias']  = $tag['alias'] ?: (self::$instance->fieldName[$table][$tag['name']]?:$tag['name']);//别名
        $tag['value']  = isset(self::$instance->content[$tag['name']]) ? self::$instance->content[$tag['name']] : (isset($tag['value'])?$tag['value']:'');//值
        $tag['desc']   = empty($tag['desc']) ? '' : $tag['desc'];//说明描述
        $tag['width']  = empty($tag['width']) ? 'auto;' : (ctype_digit($tag['width'])? $tag['width'].'px;': $tag['width']);//控件width

        //未选择时-根据编辑配置自动选择执行哪种查询
        if(empty($type) || $type=='auto') {
            if (isset(self::$instance->control_edit[$tag['name']])) {
                switch (self::$instance->control_edit[$tag['name']]['type']) {
                    case 'textarea':
                    case 'editor':
                        $type = 'like';
                        break;
                    case 'checkbox':
                        $type = 'in';
                        break;
                    case 'ctime':
                        $type = 'ctime';
                        break;
                    case 'cdate':
                        $type = 'cdate';
                        break;
                    case 'radio':
                    case 'select':
                        $type = 'select';
                        break;
                    default:
                        $type = 'eq';
                        break;
                }
            }
            //未配置编辑的，根据字段包含字符串识别
            if($type=='auto' || empty($type)){
                if(strpos($tag['name'],'time')) $type = 'ctime';
                elseif(strpos($tag['name'],'date')) $type = 'cdate';
            }
        }
        switch($type){
            case 'eq':
            case 'neq':
                self::$instance->input($tag);
                break;
            case 'in':
                self::$instance->checkbox($tag);
                break;
            case 'ctime':
                self::$instance->ctime($tag);
                break;
            case 'cdate':
                self::$instance->cdate($tag);
                break;
            case 'select':
                self::$instance->select($tag);
                break;
            case 'category':
                self::$instance->category($tag);
                break;
            default:
                self::$instance->input($tag);
                break;
        }

    }


    /**
     * input框查询
     * @param $tag [name=>'',type=>'',width=>'']
     * @param alias 别名
     * @param value 默认值/字段值
     * @param desc 说明名称
     * @param postname 控件名称
     * @param width 控件宽度
     */
    public function input($tag){
        echo <<<parse
            <div class="form-group input-group" style="width:{$tag['width']};">
                <span class="input-group-addon">{$tag['alias']}：</span>
                <input type="text" class="form-control" name="{$tag['name']}" value="{$tag['value']}">
            </div>
parse;
    }


    /**
     * 复选框
     * @param input框
     */
    public function checkbox($tag){
        $value = explode(',',$tag['value']);
        if(!empty($tag['config']) || $tag['config'] = self::$instance->control_edit[$tag['name']]['config']){
            if(strpos($tag['config'],',')){
                $tag['config'] = explode(',', $tag['config']);
            }elseif(strpos($tag['config'],'|')){
                $tag['config'] = explode('|', $tag['config']);
            }
        }
        if(empty($tag['config']) || !is_array($tag['config'])){
            $tag['config'] = ['1:请按规定格式配置'];
        }

        foreach($tag['config'] as $v){
            if (!strpos($v, ':')) {
                $key = $val = str_replace(['\'','\"'],'',$v);;
            } else {
                list($key, $val) = explode(':', $v);
            }
            $select = in_array($key,$value) ? 'checked="checked" ' : '';
            echo "
                <label class=\"checkbox-inline\">
                    <input type=\"checkbox\" id=\"{$tag['name']}[]\" value=\"{$key}\" {$select}>{$val}
                </label>";
        }
    }

    /**
     * 下拉框
     * @param 同输入框
     * @update 2018-04-02
     * @desc  格式：  0:隐藏|1:正常|2:推荐
     */
    public static function select($tag){
        $value = $tag['value'];
        if(!empty($tag['config']) || $tag['config'] = self::$instance->control_edit[$tag['name']]['config']){
            if(strpos($tag['config'],',')){
                $tag['config'] = explode(',', $tag['config']);
            }elseif(strpos($tag['config'],'|')){
                $tag['config'] = explode('|', $tag['config']);
            }
        }
        if(empty($tag['config']) || !is_array($tag['config'])){
            $tag['config'] = ['0:请按规定格式配置'];
        }

        echo <<<parse
        <div class="form-group input-group"  style="margin-right: 15px;padding-left:15px;">
            <span class="input-group-addon" for="{$tag['name']}">{$tag['alias']}:</span>
            <select name="{$tag['name']}" class="form-control">
              <option value="">全部</option>
parse;
        foreach($tag['config'] as $v){
            if (!strpos($v, ':')) {
                $key = $val = str_replace(['\'','\"'],'',$v);;
            } else {
                list($key, $val) = explode(':', $v);
            }
            $selected = ($key == $value) ? 'selected = "selected"' : '';
            echo "<option value='{$key}' {$selected}> {$val} </option>";
        }

        echo <<<parse
            </select>
        </div>
parse;

    }


    /**
     * 时间
     */
    public function ctime($tag){
        $start  = isset($tag['value'][0])? $tag['value'][0]: '';
        $end    = isset($tag['value'][1])? $tag['value'][1]: '';
        echo <<<parse
            <div class="form-group input-group">
                <span class="input-group-addon">{$tag['alias']}：</span>
                <input class="form-control js-bootstrap-datetime" type="text" name="{$tag['name']}[]" value="{$start}" autocomplete="off" style="width:{$tag['width']}">
                <span class="input-group-addon">-</span>
                <input class="form-control js-bootstrap-datetime" type="text" name="{$tag['name']}[]" value="{$end}" autocomplete="off" style="width:{$tag['width']}">
            </div>
parse;
    }


    /**
     * 日期
     */
    public function cdate($tag){
        echo <<<parse
            <div class="form-group input-group">
                <span class="input-group-addon">{$tag['alias']}：</span>
                <input class="form-control js-bootstrap-date" type="text" name="{$tag['name']}" autocomplete="off" value="{$tag['value']}">
            </div>
parse;
    }


    /**
     * 调用分类
     * @param config值表示父级分类ID，默认0全部分类
     * @update 2019-09-18
     */
    public  function category($tag){
        $modules_id = input('modules_id');
        $pid = empty(self::$instance->control_edit[$tag['name']]['config']) ? 0 : intval(self::$instance->control_edit[$tag['name']]['config']);
        if($tag['value']){
            if(empty($this->columnModel)) $this->columnModel = new \plugins\modules\model\PluginModulesColumnModel();
            $column_name = $this->columnModel->getColumnNamesById($modules_id, $tag['value']);//getColumnById是获取单个分类全部信息
        }else{
            $column_name = '';
        }
        if(!isset($this->baseurl)) $this->baseurl = cmf_plugin_url('Modules://AdminColumn/select',['modules_id'=>$modules_id]);

        echo <<<parse
		<div class="input-group" style="width:{$tag['width']}">
			<span class="input-group-addon" style="max-width:{$tag['width']};overflow:hidden;">{$tag['alias']}</span>
            <input class="form-control" type="text" required  value="{$column_name}"
                   placeholder="请选择分类" onclick="doSelectCategory('{$tag['name']}', '{$pid}');" id="js-categories-name-{$tag['name']}" readonly/>
		</div>
        <input class="form-control" type="hidden"  value="{$tag['value']}" name="{$tag['name']}"  id="js-categories-id-{$tag['name']}"/>
parse;

        if(!isset($this->doSelectCategory)){
            $this->doSelectCategory = true;
            echo <<<parse
            <script>//分类
                function doSelectCategory(fieldname, pid) {
                    var selectedCategoriesId = $('#js-categories-id-' + fieldname).val();
                    openIframeLayer("{$this->baseurl}?fieldname="+fieldname+"&pid="+pid+"&ids=" + selectedCategoriesId, '请选择', {
                        area: ['700px', '400px'],
                        btn: ['确定', '取消'],
                        yes: function (index, layero) {
                            var iframeWin          = window[layero.find('iframe')[0]['name']];
                            var selectedCategories = iframeWin.confirm();
                            $('#js-categories-id-' + fieldname).val(selectedCategories.selectedCategoriesId.join(','));
                            $('#js-categories-name-' + fieldname).val(selectedCategories.selectedCategoriesName.join(' '));
                            layer.close(index); //如果设定了yes回调，需进行手工关闭
                        }
                    });
                }
            </script>
parse;
        }

    }


}