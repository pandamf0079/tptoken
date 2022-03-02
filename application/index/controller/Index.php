<?php
namespace app\index\controller;
use think\Request;
use think\Config;
use think\Cookie;
use app\index\model\User as User;
class Index extends \think\Controller
{
    
	public function index()
    {
        return $this->fetch('/login');
    }
	
	public function ucenter(){
	
		
		return $this->fetch('/ucenter');
	}
	
	public function login(Request $request){
		/*['user_id'=>10,'username'=>'dapeng','sex'=>1]*/
		$username = $request->param('user');
		$pass = $request->param('pass');
		if($username!='' && $pass!=''){
			//数据库查询
			$userModel = new User();
			$users = $userModel->findUser($username,$pass);
			$token = $userModel->createToken($users,3600);
			Cookie::set('jwt',$token,3600);
			$this->success('登录成功','/ucenter');
			//return $token;
			
		}else{
			$this->error('用户名密码不能为空');
		}
	}
	
	
	public function getinfo(Request $request){
		
		$header = Request::instance()->header();
		if ($header['authorization'] == 'null'){
			echo json_encode([
                'status' => 1002,
                'msg' => 'token不存在,拒绝访问'
            ]);
            exit;
		}else{
			
			$userModel = new User();
			echo json_encode($userModel->verifyToken($header['authorization']));
			exit();
		}
		
	}
	
}
