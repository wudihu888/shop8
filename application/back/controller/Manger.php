<?php
namespace app\back\controller;
use think\Controller;
class Manger extends Controller
{
    public function index(){
        return $this->fetch();
    }
}