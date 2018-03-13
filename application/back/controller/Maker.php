<?php
namespace app\back\controller;
use think\Controller;
use think\Config;
use think\Db;

class Maker extends Controller{
    public function table(){

      
        return $this->fetch();
    }


    //获取表的信息 注释
    public function info(){
    
        $table = input('table'); 
        
        $schema = Config::get('database.database');
        $prefix = Config::get('database.prefix').$table;
       
        $sql = "SELECT TABLE_COMMENT from INFORMATION_SCHEMA.TABLES where table_schema=? and table_name=? ";
        
        $rows = Db::query($sql,[$schema,$prefix]);
        
        return[ 
        'comment'=>$rows[0]['TABLE_COMMENT'],
        ];
    }

    //生成模板文件
    public function generate(){
        $table = input('table');
        $comment = input('comment');
        
        $this->makerController($table,$comment);
        $this->makerModel($table,$comment);
        $this->makerValidate($table,$comment);
    }

    //生成控制器
    private function makerController($table,$comment){
        //读取模板文件
        $template = file_get_contents(APP_PATH.'back/codeTemplate/controller.tpl');
        $controller= $model=implode(array_map('ucfirst',explode('_',$table)));
        $title = $comment;
        $search = ['%controller%','%model%','%title%'];
        $replace = [$controller,$model,$title];
        $comment = str_replace($search,$replace,$template);
        $files = APP_PATH.'/back/controller/'.$controller.'.php';
        file_put_contents($files,$comment);
        echo '生成控制器'.$controller.'成功'.'<br>';

        
    }

    //生成模型
    private function makerModel($table,$comment){
        $template = file_get_contents(APP_PATH.'back/codeTemplate/model.tpl');
        $controller = $model = implode(array_map('ucfirst',explode('_',$table)));
        $title= $comment;
        $search = ['%controller%','%model%','%title%'];
        $replace = [$controller,$model,$title];
        $comment = str_replace($search,$replace,$template);
        $files = APP_PATH.'back/model/'.$model.'.php';
        file_put_contents($files,$comment);
        echo '生成模型'.$model.'成功';
    }
    //生成验证器
    private function makerValidate($table,$comment){
        $template = file_get_contents(APP_PATH.'back/codeTemplate/validate.tpl');
        $controller = $model = implode(array_map('ucfirst',explode('_',$table)));
        $title= $comment;
        $search = ['%controller%','%model%','%title%'];
        $replace = [$controller,$model,$title];
        $comment = str_replace($search,$replace,$template);
        $files = APP_PATH.'back/validate/'.$model.'.php';
        file_put_contents($files,$comment);
        echo '生成验证器'.$model.'成功';
    }

}