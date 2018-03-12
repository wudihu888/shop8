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

    //合并添加和更新操作
    // public function set($id=null){
    //     $request = Request::instance();
    //     //判断是否有ID  有ID就是更新  没有就是添加
    //     if(is_null($id)){
    //         $model = new BrandModel();

    //     }else{
    //         $model = BrandModel::get($id);

    //     }
    //     if($request->isGet()){
    //         $data = Session::has('data') ? Session::get('data') :$model->getData();
            
    //         return $this->fetch('set',['message'=>Session::get('message'),'data'=>$data]);
    //     }elseif($request->isPost()){
            
    //         $data = $request->post();
            
    //         $validate = Loader::validate('BrandAddValidate');
    //         $result = $validate->batch(true)->check($data);
    //         if(!$result){
    //             return $this->redirect('set',[],302,['message'=>$validate->getError(),'data'=>$data]);
    //         }
    //         $model->data($data);
    //         $model->save();
    //         return $this->redirect('index');
            
    //     }

    // }

    /*
    *   合并添加更新数据
    */
    public function set($id=null){
        $request = Request::instance();
        if(is_null($id)){
            //ID 为空 表示添加数据
            $model = new BrandModel();
        }else{
            $model = BrandModel::get($id);
        }
        
        if($request->isGet()){

            $data = Session::has('data') ? Session::get('data') : $model->getData();
              return  $this->fetch('set',['message'=>Session::get('message'),'data'=>$data]);
        }
        elseif($request->isPost()){
            $data = $request->post();
            $validate = Loader::validate('BrandAddValidate');
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

      
     

        BrandModel::destroy(input('selected/a',[]));

     
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


        $query = BrandModel::where($where);  
        $rows = $query->paginate($limit);
      
        $this->assign('rows',$rows);
        return $this->fetch();
    }
}