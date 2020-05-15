<?php
// +----------------------------------------------------------------------
// | 内容管理-列表-编辑
// | 后台操作内容列表，在内容模型里配置后使用
// +----------------------------------------------------------------------
namespace plugins\modules\controller;

use cmf\controller\PluginAdminBaseController;
use plugins\modules\model\ContentModel;
use plugins\modules\model\PluginModulesModel as ModulesModel;
use plugins\modules\model\PluginModulesTablesModel as ModuleTablesModel;

/**
 * Class ContentController
 * @package plugins\modules\controller
 * @property \plugins\modules\model\PluginModulesModel $modulesModel 内容模型主表
 * @property \plugins\modules\model\PluginModulesTablesModel $tablesModel 内容模型里表的存储表
 */
class ContentController extends PluginAdminBaseController
{

    public $modules_id;//模块ID
    public $table_name;//表名

    public function __construct()
    {
        parent::__construct();
        $this->modules_id = $this->request->param('modules_id',0);
        $this->table_name = $this->request->param('table_name','');
    }

    public function __get($name){
        if($name=='modulesModel'){
            if(!$this->modules_id){
                return $this->modulesModel = new ModulesModel();//模块模型
            }else{
                return $this->modulesModel = ModulesModel::get( $this->modules_id );//模块模型-直接获取数据
            }
        }elseif($name=='tablesModel'){
            return $this->tablesModel = new ModuleTablesModel();//表模型
        }elseif($name=='model'){
            return $this->model = new ContentModel();//内容模式
        }
    }

    /**
     * 内容管理
     * @adminMenu(
     *     'name'   => '内容管理',
     *     'parent' => '',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '内容管理列表',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        $this->assign($param = $this->request->except(['_plugin','_controller','_action']));//批量赋值URL参数
        //查询表配置
        $where = empty($this->table_name) ? ['pinyin'=>'']: ['table_name'=>$this->table_name];
        $currTable = $this->tablesModel->where('modules_id', $this->modules_id)->where($where)->cache(true, 60)->find();
        if(empty($currTable)){
            return '<font style="color: red"><br/>模块不存在,请检查模块配置！</font>
                    <br/><br/><font style="color: green">如是已删除模块，请在菜单管理里删除此无效菜单！</font>';
        }

        $this->assign('table_name',     $table_name = $currTable['table_name']);
        $this->assign('table_pinyin',   $table_pinyin = $currTable['pinyin']);
        $this->assign('relate_level',   $currTable['relate_level']);
        $this->assign('control_search', $currTable['control_search']? $currTable['control_search']: []);
        $this->assign('control_list',   $currTable['control_list']? $currTable['control_list']: []);
        $this->assign('is_main_table',  $currTable['pinyin']? 0: 1);//是否是主表

        //内容查询
        $data = $this->model->getContent($param, $table_name, $currTable);
		$data->appends($param);
        $this->assign('items', $data->items());//内容
        $this->assign('page', $data->render());//分页

        //模块下所有表的所有字段名,格式：[tableName=>[field=>'name']]
        $this->assign('allFieldName',$allFieldName = $this->modulesModel->getAllFieldName(true));
        //真实获取模块下的所有表
        $this->assign('allTables', $allTables = $this->modulesModel->getTables(true));
        //搜索配置初始化
        \plugins\modules\lib\SearchForm::init($allFieldName, $param, $currTable['control_edit']);//control_edit -> 搜索自动匹配使用
        //列表配置初始化
        \plugins\modules\lib\ListForm::init($allFieldName, $currTable['control_edit']);


        //使用哪种列表VIEW：有带左边导航，有什么都不带的
        if(isset($param['modules_simple']) && $param['modules_simple']){
            return $this->fetch('content/simple_index');//指定简单view
        }elseif($currTable['relate_level']==2){
            return $this->fetch('content/simple_index');//一对多的表
        }elseif($currTable['relate_level']==0 && !empty($currTable['pinyin'])){
            return $this->fetch('content/simple_index');//不关联的表
        }else{
            return $this->fetch('content/index');//主表，一对一，或者其它未知的
        }
    }


    /**
     * 内容/文章添加
     */
    public function edit()
    {
        $this->assign($param = $this->request->except(['_plugin','_controller','_action']));//批量赋值URL参数

        //真实获取模块下的所有表及表配置
        $allTables = $this->modulesModel->getTables(true);
        if(empty($this->table_name)) $this->table_name = key($allTables);//默认主表
        if(!isset($allTables[$this->table_name])) return '错误，该模型下未发现('.$this->table_name.')表！';
        $currTable = $allTables[$this->table_name];
        $this->assign('allTables', $allTables);
        $this->assign('table_name',     $this->table_name);
        $this->assign('relate_level',   $currTable['relate_level']);
        $this->assign('is_main_table',  $is_main_table = $currTable['pinyin'] ? 0 : 1);//是否是主表
        //模块下所有表的所有字段名,格式：[tableName=>[field=>'name']]
        $this->assign('allFieldName', $allFieldName = $this->modulesModel->getAllFieldName(true));
        //主表的关联id
        if(!isset($param['itemid'])){
            $this->assign('itemid', ($is_main_table && isset($param['id'])) ? $param['id'] : 0);
        }

        //内容-该id相关所有内容
        $content = [];
        if(isset($param['id'])){
            $content = $this->model->getEditContent($this->modules_id, $param['id'], $this->table_name, $is_main_table);
        }
        $this->assign('content', $content);


        //使用哪种编辑VIEW：有带左边导航，有不带的
        if(isset($param['modules_simple']) && $param['modules_simple']){
            return $this->fetch('content/simple_edit');//指定简单view - 连顶部导航也不带 - 用于对话框或者iframe
        }elseif($currTable['relate_level']==2){
            return $this->fetch('content/simple_edit');//一对多的表
        }elseif($currTable['relate_level']==0 && !empty($currTable['pinyin'])){
            return $this->fetch('content/simple_edit');//不关联的表
        }else{
            return $this->fetch('content/edit');//主表，一对一，或者其它未知的
        }
    }

    /**
     * 修改内容/文章提交
     */
    public function editPost()
    {
        $param = $this->request->except(['_plugin','_controller','_action']);
        if(isset($param['id'])){
            $param['post'] = \plugins\modules\lib\Filter::formatPost($param['post']);
        }else{
            $param['post'] = \plugins\modules\lib\Filter::formatPost($param['post'], true);
            $this->addPost($param);
            $this->success('添加成功!');
        }

        //先更新主表
        $this->model->table($param['table_name'])->where('id', $param['id'])->update($param['post'][$param['table_name']]);
        unset($param['post'][$param['table_name']]);

        //再更新附表(一对一的表)
        if($param['post'])
        foreach($param['post'] as $table => $list){
            if($this->model->table($table)->where('itemid',$param['id'])->count()){
                if(isset($list['id'])){
                    $where = ['id'=>$list['id']];
                    unset($list['id']);
                }else{
                    $where = ['itemid'=>$param['id']];
                }
                $this->model->table($table)->where($where)->update( array_merge(['itemid'=>$param['id']], $list) );
            }else{
                $this->model->table($table)->insert( array_merge(['itemid'=>$param['id']], $list) );
            }
        }

        unset($param['post']);
        $this->success('修改成功!', cmf_plugin_url('Modules://Content/edit', $param));
    }

    /**
     * 添加内容/文章提交
     */
    public function addPost($param)
    {
        //先取主表插入，然后取出自增ID再插附表
        $itemid     = $this->model->table($param['table_name'])->insert($param['post'][$param['table_name']], false, true);
        if($itemid){
            unset($param['post'][$param['table_name']]);
            foreach($param['post'] as $table => $list){
                $list['itemid'] = $itemid;//唯一关联
                $this->model->table($table)->insert($list);
            }
        }

        $param['id'] = $itemid;//这个itemid表示主表的ID或者多表的ID
        unset($param['post']);
        $this->success('添加成功!', cmf_plugin_url('Modules://Content/edit', $param));
    }

    /**
     * 内容删除
     * @adminMenu(
     *     'name'   => '内容删除',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '内容模型内容数据删除',
     *     'param'  => ''
     * )
     */
    public function delete()
    {
        $param = $this->request->except(['_plugin','_controller','_action']);

        if (isset($param['ids']) && isset($param['modules_id'])) {
            $ids = $this->request->param('ids/a');
            unset($param['ids']);

            //先查询一对一和一对多关联表
            $relateTables = [];
            $relateField = '';
            $modulesDatas = $this->tablesModel->where('modules_id',$param['modules_id'])->field('id,pinyin,table_name,relate_field,relate_level')->select();

            foreach($modulesDatas as $data){
                if($data['table_name'] == $param['table_name']){//当前表
                    if(!empty($data['pinyin'])){//如果当前表不是主表，就不会有关联表
                        $relateTables = [];break;
                    }
                }else if($data['relate_level']>0){
                    $relateTables[] = $data['table_name'];
                    $relateField = $data['relate_field'];
                }
            }

            //删除当前表数据
            $result = $this->modulesModel->table($param['table_name'])->where('id', 'in', $ids)->delete();
            if($result){
                if(!empty($relateTables)){//删除关联表数据
                    $relateField = empty($relateField) ? 'itemid' : $relateField;//关联字段
                    foreach($relateTables as $table){
                        $this->modulesModel->table($table)->where($relateField, 'in', $ids)->delete();
                    }
                }
                $this->success("删除成功！", cmf_plugin_url('Modules://Content/index', $param));
            }

            $this->error("删除失败！", cmf_plugin_url('Modules://Content/index', $param));
        }
    }

}
