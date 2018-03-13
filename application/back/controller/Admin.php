<?php
namespace app\back\controller;
use think\Controller;
use think\Request;
use app\back\model\Admin as AdminModel;
use think\Loader;
use think\Validate;
use think\Session;
use think\Paginator;
class Admin extends Controller
{

    // 登陆
    public function login(){
        $request = Request::instance();
        if($request->isGet()){
              return $this->fetch('login',['message'=>Session::get('message')]);
        }elseif($request->isPost()){
            $admin = AdminModel::get(['user'=>input('user')]);
            if($admin){
                if($admin->password ==md5(input('password').$admin->salt)){
                    $data = $admin->getData();
                    unset($data['password']);
                    unset($data['salt']);
                    Session::set('admin',$data);
                   
                    $this->redirect('manger/index');
                }
            }
            $this->redirect('login',[],302,['message'=>'管理员信息错误']);
        }
    }

    
    //退出登陆
    public function logout(){
        Session::delete('admin');
        $this->redirect('login');
    }
      




    /*
    *   合并添加更新数据
    */
    public function set($id=null){
        $request = Request::instance();
        if(is_null($id)){
            //ID 为空 表示添加数据
            $model = new AdminModel();
        }else{
            $model = AdminModel::get($id);
        }
        
        if($request->isGet()){

            $data = Session::has('data') ? Session::get('data') : $model->getData();
              return  $this->fetch('set',['message'=>Session::get('message'),'data'=>$data]);
        }
        elseif($request->isPost()){
            $data = $request->post();
            $validate = Loader::validate('AdminSetValidate');
            $result = $validate->batch(true)->check($data);
            if(!$result){
                return $this->redirect('set',[],302,['message'=>$validate->getError(),'data'=>$data]);
            }
            $model->data($data);
            $model->save();
            return $this->redirect('index');
        }
    }



   
    // 删除操作
    public function delete(){

      
     

        AdminModel::destroy(input('selected/a',[]));

     
        $this->redirect('index');
    }




    public function index(){
        $limit = 10;
        $where = $filter=[];
        $filter['filter_title'] = input('filter_title','');
        if($filter['filter_title']!= ''){
             $where['title'] = ['like',$filter['filter_title'].'%'];
        }
        $filter['filter_site'] = input('filter_site','');
        if($filter['filter_site']!= ''){
            if(substr($filter['filter_site'],0,7)!= 'http://' && substr($filter['filter_site'],0,8)!= 'https://'){
                $site = 'http://'.$filter['filter_site'];
                $sites = 'https://'.$filter['filter_site'];
                $where['site'] = ['like',[$site.'%',$sites.'%'],'OR'];
            }
            else
                $where['site'] = ['like',$filter['filter_site'].'%'];
        }


        $query = AdminModel::where($where);  
        $rows = $query->paginate($limit);
      
        $this->assign('rows',$rows);
        return $this->fetch();
    }
}