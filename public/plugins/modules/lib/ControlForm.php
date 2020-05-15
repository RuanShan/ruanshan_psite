<?php
// +----------------------------------------------------------------------
// | 操作控制-控件构造-中间件
// +----------------------------------------------------------------------
namespace plugins\modules\lib;


class ControlForm{

    protected static $instance = NULL;

    public $fields_default = null;
    public $tables_example = null;

    /**
     * 单例获取
     */
    public static function init(){
        if (self::$instance == NULL) {
            self::$instance = new self();
            self::$instance->initConst();
        }

        return self::$instance;
    }

    /**
     * 操作控制里面的选择类型--单独提出来
     * @param $type
     * @param $name
     * @param $value
     */
    public static function select($type, $name, $value='auto'){
        switch($type){
            case 'search':
                $str = "
                <select class='form-control' name='{$name}' data-value='{$value}'>
                    <option value='auto' ". ($value=='auto'?'selected=\"selected\"':'') .">自动识别</option>
                    <option value='eq' ". ($value=='eq'?'selected=\"selected\"':'') .">等于</option>
                    <option value='neq' ". ($value=='neq'?'selected=\"selected\"':'') .">不等于</option>
                    <option value='ctime' ". ($value=='ctime'?'selected=\"selected\"':'') .">时间段</option>
                    <option value='cdate' ". ($value=='cdate'?'selected=\"selected\"':'') .">日期</option>
                    <option value='in' ". ($value=='in'?'selected=\"selected\"':'') .">in</option>
                    <option value='like' ". ($value=='like'?'selected=\"selected\"':'') .">like</option>
                    <option value='match' ". ($value=='match'?'selected=\"selected\"':'') .">全文索引</option>
                    <option value='category' ". ($value=='category'?'selected=\"selected\"':'') .">分类选择</option>
                </select>
            ";
                break;
            case 'list':
                $str = "
                <select class='form-control' name='{$name}' onchange='control_edit_select(this)' data-value='{$value}'>
                    <option value='' data-tip='(默认编辑配置设置，[field]会被替换成字段对应值)' ". ($value==''?'selected=\"selected\"':'') .">自动识别</option>
                    <option value='text'  data-tip='(默认编辑配置设置)' ". ($value=='text'?'selected=\"selected\"':'') .">常规文字</option>
                    <option value='ctime' data-tip='(格式如:) Y-m-d H:i' ". ($value=='ctime'?'selected=\"selected\"':'') .">时间转换</option>
                    <option value='img' data-tip='(默认编辑配置,其它如:) //img.baidu.com/[picture]' ". ($value=='img'?'selected=\"selected\"':'') .">展示图片</option>
                    <option value='href' data-tip='(默认编辑配置,其它如:) //news.baidu.com/[id].html' ". ($value=='href'?'selected=\"selected\"':'') .">带链接文字</option>
                    <option value='category' data-tip='column表PID，默认顶级是全部' ". ($value=='category'?'selected=\"selected\"':'') .">分类</option>
                    <option value='admin_id' data-tip='(此选项会把管理员ID展示成真实名称) ' ". ($value=='admin_id'?'selected=\"selected\"':'') .">系统用户名</option>
                </select>
            ";
                break;
            case 'edit':
                $str = "
                <select class='form-control' name='{$name}' onchange='control_edit_select(this)' data-value='{$value}'>
                    <option value='text' data-tip='“placeholder” 或者 “(单位)”'>输入框</option>
                    <option value='textarea' data-tip='placeholder'>文本框</option>
                    <option value='radio' data-tip='(格式如:) 0:隐藏,1:正常,2:推荐'>单选框</option>
                    <option value='checkbox' data-tip='(格式如:) 0:隐藏,1:正常,2:推荐'>复选框</option>
                    <option value='select' data-tip='(格式如:) 0:隐藏,1:正常,2:推荐'>下拉选择框</option>
                    <option value='ctime' data-tip='(可选默认值:) ctime | system_time'>单时间</option>
                    <option value='cdate' data-tip='(可选默认值:) ctime | system_time'>单日期</option>
                    <option value='uploadImg' data-tip='(无设置值)'>图片上传</option>
                    <option value='uploadVideo' data-tip='(无设置值)'>视频上传</option>
                    <option value='editor' data-tip='(无设置值)'>编辑器</option>
                    <option value='category' data-tip='column表PID，默认顶级是全部'>分类选择</option>
                    <option value='citys' data-tip='(可选值:) 不填 | cityid | cityids (多个城市名|单个城市ID|多个城市ID)'>城市选择</option>
                    <option value='system' data-tip='(必填之一:) admin_id | ctime| system_time'>更新时系统值</option>
                    <option value='system_create' data-tip='(必填之一:) admin_id | ctime| system_time'>添加时系统值</option>
                    <option value='password'>密码框</option>
                </select>
                ";
                break;
        }
        echo $str;
    }


    /**
     * MSQYL字段选择类型
     * 包含可选值：tinyint,smallint,mediumint,int,integer,bigint,varchar,char,bit,double,float,decimal,tinytext,text,mediumtext,longtext,timestamp,datetime
     */
    public static function fieldTypeSelect($name='', $value=''){
        return "
                <select class='form-control' name='{$name}'>
                    <option value='int'". ($value=='int'?'selected=\"selected\"':'') .">int</option>
                    <option value='varchar'". ($value=='varchar'?'selected=\"selected\"':'') .">varchar</option>
                    <option value='text' ". ($value=='text'?'selected=\"selected\"':'') .">text</option>
                    <option value='tinyint' ". ($value=='tinyint'?'selected="selected"':'') .">tinyint</option>
                    <option value='smallint' ". ($value=='smallint'?'selected=\"selected\"':'') .">smallint</option>
                    <option value='bigint' ". ($value=='bigint'?'selected=\"selected\"':'') .">bigint</option>
                    <option value='char' ". ($value=='char'?'selected=\"selected\"':'') .">char</option>
                    <option value='mediumtext' ". ($value=='mediumtext'?'selected=\"selected\"':'') .">mediumtext</option>
                    <option value='longtext' ". ($value=='longtext'?'selected=\"selected\"':'') .">longtext</option>
                    <option value='timestamp' ". ($value=='timestamp'?'selected=\"selected\"':'') .">timestamp</option>
                    <option value='datetime' ". ($value=='datetime'?'selected=\"selected\"':'') .">datetime</option>
                    <option value='double' ". ($value=='double'?'selected=\"selected\"':'') .">double</option>
                    <option value='float' ". ($value=='float'?'selected=\"selected\"':'') .">float</option>
                </select>
            ";
    }

    /**
     * 创建模块 - 初始化选择4张表和快速编辑表字段
     * 调用示例：plugins\modules\lib\ControlForm::init()->fields_default;
     */
    private function initConst(){
        //字段默认使用类型，特殊类型请单独配置。
        $this->fields_default = [
            'type'  =>'int',//类型INT，包含可选值：tinyint,smallint,mediumint,int,integer,bigint,varchar,char,bit,double,float,decimal,tinytext,text,mediumtext,longtext,timestamp,datetime
            'len'   =>11,   //长度10
            'float' =>0,    //无小数
            'null'  =>0,    //不为NULL
            'default'=>'',  //默认空字符
            'inc'   =>0,    //不自动增长,其值表示增长值
            'key'   =>'',   //索引，其它可选值：PRIMARY、INDEX、UNIQUE、FULLTEXT
            'level'  =>9,    //发开操作：0-9,0表示必须保留此字段
        ];
        $this->tables_example = [
            [
                'name' => '',//表名
                'remark'=>'',
                'level' => 0,//重要级别：0是主表，不可删除
                'relate' => 0,//关联：0是不关联，1是一对一，2是一对多
                'fields' => [
                    'id'        =>['alias' => 'ID', 'type' => 'int', 'len' => 10, 'inc' => 1, 'key' => 'PRIMARY', 'level' => 0],
                    'category'  =>['alias' => '栏目', 'type' => 'int', 'len' => 10, 'level' => 3],
                    'title'     =>['alias' => '标题', 'type' => 'varchar', 'len' => 255, 'level' => 1],
                    'alias'     =>['alias' => '别名', 'type' => 'varchar', 'len' => 255, 'level' => 5],
                    'shorttitle'=>['alias' => '短标题', 'type' => 'varchar', 'len' => 32, 'level' => 5],
                    'citys'     =>['alias' => '城市', 'type' => 'varchar', 'len' => 255, 'level' => 9],
                    'keywords'  =>[ 'alias' => '关键词', 'type' => 'varchar', 'len' => 128, 'level' => 3],
                    'tags'      =>['alias' => '标签', 'type' => 'varchar', 'len' => 128, 'level' => 5],
                    'pinyin'    =>['alias' => '拼音', 'type' => 'varchar', 'len' => 32, 'level' => 7],
                    'showtype'  =>['alias' => '被推送标识', 'type' => 'tinyint', 'len' => 1, 'default' => 1, 'level' => 3],
                    'author'    =>['alias' => '文章来源作者', 'type' => 'varchar', 'len' => 32, 'level' => 3],
                    'source'    =>['alias' => '文章来源地址', 'type' => 'varchar', 'len' => 255, 'level' => 7],
                    'digest'    =>['alias' => '摘要', 'type' => 'varchar', 'len' => 255, 'level' => 1],
                    'editor'    =>['alias' => '编辑人员', 'type' => 'varchar', 'len' => 32, 'level' => 1],
                    'editorid'  =>['alias' => '编辑人员ID', 'type' => 'int', 'len' => 10, 'level' => 1],
                    'picture'   =>['alias' => '缩略图片', 'type' => 'varchar', 'len' => 255, 'level' => 1],
                    'picture1'  =>['alias' => '附图2', 'type' => 'varchar', 'len' => 255, 'level' => 7],
                    'picture2'  =>['alias' => '附图2', 'type' => 'varchar', 'len' => 255, 'level' => 7],
                    'picture3'  =>['alias' => '附图3', 'type' => 'varchar', 'len' => 255, 'level' => 7],
                    'bigpicture'=>['alias' => '内容大图', 'type' => 'varchar', 'len' => 255, 'level' => 5],
                    'createtime'=>['alias' => '录入时间', 'type' => 'int', 'len' => 10, 'level' => 1],
                    'updatetime'=>['alias' => '更新时间', 'type' => 'int', 'len' => 10, 'level' => 1],
                    'showtime'  =>['alias' => '发布时间', 'type' => 'int', 'len' => 10, 'level' => 3],
                    'attachnum' =>['alias' => '附件总数', 'type' => 'int', 'len' => 10, 'level' => 9],
                    'pagenum'   =>['alias' => '分页总数', 'type' => 'int', 'len' => 10, 'level' => 9],
                    'goodnum'   =>['alias' => '点赞数', 'type' => 'int', 'len' => 10, 'level' => 7],
                    'favnum'    =>['alias' => '收藏数', 'type' => 'int', 'len' => 10, 'level' => 7],
                    'commnum'   =>['alias' => '评论数', 'type' => 'int', 'len' => 10, 'level' => 7],
                    'pv'        =>['alias' => '总访问次数', 'type' => 'int', 'len' => 10, 'level' => 5],
                    'pv2'       =>['alias' => '日访问次数', 'type' => 'int', 'len' => 10, 'level' => 9],
                    'pv3'       =>['alias' => '周访问次数', 'type' => 'int', 'len' => 10, 'level' => 9],
                    'pv3'       =>['alias' => '月访问次数', 'type' => 'int', 'len' => 10, 'level' => 9],
                    'orderby'   =>['alias' => '排序值', 'type' => 'int', 'len' => 10, 'level' => 9],
                   // 'filename'  =>['alias' => '访问路径', 'type' => 'varchar', 'len' => 255, 'level' => 9],
                    'state'     =>['alias' => '状态', 'type' => 'tinyint', 'len' => 1, 'level' => 1],
                ],
                'config' => [
                    'category' => ['type' => 'link', 'table' => 'column', 'on' => 'id', 'field' => 'name'],
                    'showtype' => ['type' => 'default', 'value' => '1:正常,2:推荐,3:置顶'],
                    'editor' => ['type' => 'system', 'value' => 'admin'],
                    'state' => ['type' => 'default', 'value' => '0:隐藏,1:正常,2:推荐'],
                ],
                'list'  =>[
                    'id'        =>['name'=>'id','alias'=>'ID','type'=>'text'],
                    'title'     =>['name'=>'title','alias'=>'标题','type'=>'href','config'=>'http://www.baidu.com/[id].html'],
                    'editor'    =>['name'=>'editor','alias'=>'编辑','type'=>'text'],
                    'updatetime'=>['name'=>'updatetime','alias'=>'更新时间','type'=>'ctime','position'=>'right'],//date|Y-m-d H:i
                    'showtime'  =>['name'=>'showtime','alias'=>'发布时间','type'=>'ctime','position'=>'right'],//date|Y-m-d H:i
                    'status'    =>['name'=>'status','alias'=>'状态','type'=>'radio','position'=>'right','config'=>'0:隐藏,1:正常,2:推荐'],//|{0:隐藏,1:正常,2:推荐}
                ],
                'edit'  =>[
                    ['name'=>'category','alias'=>'栏目','type'=>'category'],
                    ['name'=>'title','alias'=>'标题','type'=>'text'],
                    ['name'=>'alias','alias'=>'别名','type'=>'text'],
                    ['name'=>'shorttitle','alias'=>'短标题','type'=>'text'],
                    ['name'=>'citys','alias'=>'城市','type'=>'citys'],
                    ['name'=>'keywords','alias'=>'关键词','type'=>'textarea'],
                    ['name'=>'tags','alias'=>'标签','type'=>'checkbox','position'=>'right','config'=>'1:要闻,2:娱乐,3:体育,4:美食,5:健康'],
                    ['name'=>'pinyin','alias'=>'拼音','type'=>'text'],
                    ['name'=>'showtype','alias'=>'被推送标识','type'=>'radio','position'=>'right','config'=>'1:正常,2:推荐,3:置顶'],
                    ['name'=>'author','alias'=>'文章来源作者','type'=>'text','width'=>'100'],
                    ['name'=>'source','alias'=>'文章来源地址','type'=>'text'],
                    ['name'=>'digest','alias'=>'摘要','type'=>'textarea'],
                    ['name'=>'editor','alias'=>'编辑人员','type'=>'text','width'=>'100','position'=>'right'],
                    ['name'=>'editorid','alias'=>'编辑人员ID','type'=>'system_create','width'=>'80','position'=>'right','config'=>'uid'],
                    ['name'=>'picture','alias'=>'缩略图片','type'=>'uploadImg','position'=>'right'],
                    ['name'=>'picture2','alias'=>'附图2','type'=>'uploadImg','position'=>'right'],
                    ['name'=>'picture3','alias'=>'附图3','type'=>'uploadImg','position'=>'right'],
                    ['name'=>'bigpicture','alias'=>'内容大图','type'=>'uploadImg','position'=>'right'],
                    ['name'=>'createtime','alias'=>'录入时间','type'=>'system_create','position'=>'right','config'=>'time'],
                    ['name'=>'updatetime','alias'=>'更新时间','type'=>'system','position'=>'right','config'=>'time'],
                    ['name'=>'showtime','alias'=>'发布时间','type'=>'ctime','position'=>'right'],
                    ['name'=>'attachnum','alias'=>'附件总数','type'=>'text','width'=>'80'],
                    ['name'=>'pagenum','alias'=>'分页总数','type'=>'text','width'=>'80'],
                    ['name'=>'goodnum','alias'=>'点赞数','type'=>'text','width'=>'80'],
                    ['name'=>'favnum','alias'=>'收藏数','type'=>'text','width'=>'80'],
                    ['name'=>'commnum','alias'=>'评论数','type'=>'text','width'=>'80'],
                    ['name'=>'pv','alias'=>'总访问次数','type'=>'text','width'=>'80'],
                    ['name'=>'pv2','alias'=>'日访问次数','type'=>'text','width'=>'80'],
                    ['name'=>'pv3','alias'=>'周访问次数','type'=>'text','width'=>'80'],
                    ['name'=>'pv3','alias'=>'月访问次数','type'=>'text','width'=>'80'],
                    ['name'=>'orderby','alias'=>'排序值','type'=>'text','width'=>'80'],
                    ['name'=>'filename','alias'=>'访问路径','type'=>'text'],
                    ['name'=>'status','alias'=>'状态','type'=>'radio','position'=>'right','config'=>'0:隐藏,1:正常,2:推荐'],//|{0:隐藏,1:正常,2:推荐}
                ],
                'search'=>[
                    ['name'=>'id',      'alias'=>'ID',    'type'=>'eq','width'=>'50'],
                    ['name'=>'title',   'alias'=>'标题',  'type'=>'like','width'=>'250'],
                    ['name'=>'editor',  'alias'=>'编辑',  'type'=>'eq'],
                    ['name'=>'updatetime','alias'=>'更新时间','type'=>'date'],
                ],
            ],
            //-----【扩展表】-----//
            [
                'name' => 'infos',
                'remark'=>'扩展信息',
                'level' => 3,
                'relate' => 1,
                'fields' => [
                    'itemid'=>['alias' => '列表ID', 'type' => 'int', 'len' => 10, 'key' => 'PRIMARY', 'level' => 0],
                    'num1'=>['alias' => '自定义数字字段1', 'type' => 'int', 'len' => 10, 'level' => 5],
                    'num2'=>['alias' => '自定义数字字段2', 'type' => 'int', 'len' => 10, 'level' => 7],
                    'num3'=>['alias' => '自定义数字字段3', 'type' => 'int', 'len' => 10, 'level' => 7],
                    'num4'=>['alias' => '自定义数字字段4', 'type' => 'int', 'len' => 10, 'level' => 7],
                    'num5'=>['alias' => '自定义数字字段5', 'type' => 'int', 'len' => 10, 'level' => 9],
                    'num6'=>['alias' => '自定义数字字段6', 'type' => 'int', 'len' => 10, 'level' => 9],
                    'num7'=>['alias' => '自定义数字字段7', 'type' => 'int', 'len' => 10, 'level' => 9],
                    'num8'=>['alias' => '自定义数字字段8', 'type' => 'int', 'len' => 10, 'level' => 9],
                    'num9'=>['alias' => '自定义数字字段9', 'type' => 'int', 'len' => 10, 'level' => 9],
                    'str1'=>['alias' => '自定义字符字段1', 'type' => 'varchar', 'len' => 255, 'level' => 5],
                    'str2'=>['alias' => '自定义字符字段2', 'type' => 'varchar', 'len' => 255, 'level' => 7],
                    'str3'=>['alias' => '自定义字符字段3', 'type' => 'varchar', 'len' => 255, 'level' => 7],
                    'str4'=>['alias' => '自定义字符字段4', 'type' => 'varchar', 'len' => 255, 'level' => 7],
                    'str5'=>['alias' => '自定义字符字段5', 'type' => 'varchar', 'len' => 255, 'level' => 9],
                    'str6'=>['alias' => '自定义字符字段6', 'type' => 'varchar', 'len' => 255, 'level' => 9],
                    'str7'=>['alias' => '自定义字符字段7', 'type' => 'varchar', 'len' => 255, 'level' => 9],
                    'str8'=>['alias' => '自定义字符字段8', 'type' => 'varchar', 'len' => 255, 'level' => 9],
                    'str9'=>['alias' => '自定义字符字段9', 'type' => 'varchar', 'len' => 255, 'level' => 9],
                ],
                'config' => [],
                'list'  =>[],
                'edit'  =>[],
                'search'=>[],
            ],
            //-----【附件表】-----//
            [
                'name' => 'attachs',
                'remark'=>'附件',
                'level' => 7,
                'relate' => 2,
                'fields' => [
                    'id'        =>['alias' => 'ID', 'type' => 'int', 'len' => 10, 'inc' => 1, 'key' => 'PRIMARY', 'level' => 0],
                    'itemid'    =>['alias' => '列表ID', 'type' => 'int', 'len' => 10, 'key' => 'Normal', 'level' => 0],
                    'width'     =>['alias' => '宽', 'type' => 'int', 'len' => 10, 'level' => 3],
                    'height'    =>['alias' => '高', 'type' => 'int', 'len' => 10, 'level' => 3],
                    'orderby'   =>['alias' => '排序', 'type' => 'int', 'len' => 10, 'level' => 5],
                    'favnum'    =>['alias' => '收藏数', 'type' => 'int', 'len' => 10, 'level' => 9],
                    'fileurl'   =>['alias' => '地址', 'type' => 'varchar', 'len' => 255, 'level' => 9],
                    'subtitle'  =>['alias' => '小标题', 'type' => 'varchar', 'len' => 255, 'level' => 5],
                    'digest'    =>['alias' => '摘要', 'type' => 'varchar', 'len' => 255, 'level' => 5],
                ],
                'config' => [],
                'list'  =>[
                    ['name'=>'id','alias'=>'ID','type'=>'text','width'=>'60'],
                    ['name'=>'itemid','alias'=>'列表ID','type'=>'text','width'=>'90'],
                    ['name'=>'width','alias'=>'宽','type'=>'text','width'=>'60'],
                    ['name'=>'height','alias'=>'高','type'=>'text','width'=>'60'],
                    ['name'=>'subtitle','alias'=>'小标题','type'=>'text','width'=>'300'],
                    ['name'=>'digest','alias'=>'正文','type'=>'textarea','width'=>'300'],
                    ['name'=>'orderby','alias'=>'排序','type'=>'text','width'=>'60'],
                    ['name'=>'favnum','alias'=>'收藏数','type'=>'text','width'=>'75'],
                ],
                'edit'  =>[
                    ['name'=>'itemid','alias'=>'列表ID','type'=>'text','width'=>'70'],
                    ['name'=>'width','alias'=>'宽','type'=>'text','width'=>'70'],
                    ['name'=>'height','alias'=>'高','type'=>'text','width'=>'70'],
                    ['name'=>'orderby','alias'=>'排序','type'=>'text','width'=>'70'],
                    ['name'=>'favnum','alias'=>'收藏数','type'=>'text','width'=>'70'],
                    ['name'=>'fileurl','alias'=>'地址','type'=>'uploadImg'],
                    ['name'=>'subtitle','alias'=>'小标题','type'=>'textarea','width'=>'300'],
                    ['name'=>'digest','alias'=>'正文','type'=>'textarea','width'=>'300'],
                ],
                'search'=>[
                    ['name'=>'id','alias'=>'ID','type'=>'eq','width'=>'50'],
                    ['name'=>'itemid','alias'=>'列表ID','type'=>'eq','width'=>'50'],
                    ['name'=>'width','alias'=>'宽','type'=>'eq','width'=>'50'],
                    ['name'=>'height','alias'=>'高','type'=>'eq','width'=>'50'],
                ],
            ],

            //-----【内容表】，说明：此测试表设置为一对一关联,分页仅用_ueditor_page_break_tag_标识符断开。也可设置一对多关联，即一篇文章对应多条分页内容。二选一 -----//
            //一对一配置如下：
            [
                'name' => 'texts',
                'remark'=>'内容',
                'level' => 5,
                'relate' => 1,
                'fields' => [
                    'itemid'=>['alias' => '文章ID', 'type' => 'int', 'len' => 10, 'key' => 'PRIMARY', 'level' => 0],
                    'text'  =>['alias' => '内容', 'type' => 'text', 'len' => 0, 'level' => 1],
                ],
                'config' => [],//一对一没有此值
                'list'  =>[],//一对一没有此值
                'edit'  =>[
                    ['name'=>'text','alias'=>'内容','type'=>'editor','desc'=>'说明：此测试表设置为一对一关联,分页仅用_ueditor_page_break_tag_标识符断开。
                    如果存在分页时,此表本该是一对多关系，请自行设置一对多关联的配置。'],
                ],
                'search'=>[],
            ],
            //一对多配置如下：
            /*[
                'name'  =>'texts',
                'remark'=>'内容',
                'level' =>5,
                'relate'=>2,
                'fields'=>[
                    'id'        =>['alias'=>'ID',  'type'=>'int','len'=>10,'inc'=>1,'key'=>'PRIMARY','level'=>0],
                    'itemid'    =>['alias'=>'列表ID','type'=>'int','len'=>10,'key'=>'Normal','level'=>0],
                    'subtitle'  =>['alias'=>'分页小标题','type'=>'varchar','len'=>255,'level'=>7],
                    'keywords'  =>['alias'=>'分页关键字','type'=>'varchar','len'=>255,'level'=>7],
                    'page'      =>['alias'=>'页码','type'=>'int','len'=>6,'level'=>7],
                    'text'      =>['alias'=>'内容','type'=>'text','len'=>0,'level'=>1],
                ],
                'config'=>[],
                'list'  =>[
                    ['name'=>'id','alias'=>'ID','type'=>'text'],
                    ['name'=>'itemid','alias'=>'列表ID','type'=>'text'],
                    ['name'=>'title','alias'=>'标题','type'=>'href','config'=>'http://www.baidu.com/[itemid].html'],
                    ['name'=>'page','alias'=>'页码','type'=>'text'],
                ],
                'edit'  =>[
                    ['name'=>'subtitle','alias'=>'分页小标题','type'=>'textarea'],
                    ['name'=>'keywords','alias'=>'分页关键字','type'=>'text'],
                    ['name'=>'page','alias'=>'页码','type'=>'text','width'=>'100'],
                    ['name'=>'text','alias'=>'内容','type'=>'editor','desc'=>'说明：此测试表设置为一对一关联,分页仅用_ueditor_page_break_tag_标识符断开。
                    如果存在分页时,此表本该是一对多关系，请自行设置一对多关联的配置。'],
                ],
                'search'=>[],
            ],*/
        ];
    }

}