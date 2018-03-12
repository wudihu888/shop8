<?php
namespace app\back\controller;
use think\Controller;
use think\Request;
use app\back\model\Brand as BrandModel;
use think\Loader;
use think\Validate;
use think\Session;
use think\Paginator;
class Brand extends Controller
{
    public function add(){

        //判断是什么请求 get 展示数据  POST 添加数据
        $request = Request::instance();
        if($request->isGet()){
            return $this->fetch('add',['message'=>Session::get('message')]);
        }elseif($request->isPost()){
            $data = $request->post();

            //验证
            $validate = Loader::validate('BrandAddValidate');
            $result = $validate->batch(true)->check($data);
           
            if(!$result){
              $this->redirect('add',[],'302',['message'=>$validate->getError()]);
            }

            $model= new BrandModel();
            $model->data($data);//data 方法添加数据
            $model->save();
            
            $this->redirect('index');

        }

        
    }
    // 删除操作
    public function delete(){

        $a = input('selected/a',[]);
     

        BrandModel::destroy(input('selected/a',[]));

     
        $this->redirect('index');
    }

    public function set(){
        
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


        $query = BrandModel::where($where);  
        $rows = $query->paginate($limit);
      
        $this->assign('rows',$rows);
        return $this->fetch();
    }
}