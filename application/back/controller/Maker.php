<?php
namespace app\back\controller;
use think\Controller;
use think\Config;


class Maker extends Controller{
    public function table(){

      
        return $this->fetch();
    }


    //获取表的信息 注释
    public function info(){
       
        $table = input('table');
        $schema = Config::get('database.database');
        $prefix = Config::get('database.prefix').$table;

        $sql = "SELECT table_comment from INFORMATION_SCHEMA.TABLES where table_schema=? and table_name=?";

        $rows = Db::query($sql,[$schema,$prefix]);

        return[ 
        'comment'=>$rows[0]['TABLE_COMMENT'],
        ];
    }
}