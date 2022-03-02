<?php
namespace app\index\model;

use think\Model;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT as JWTUtil;
use Firebase\JWT\Key;
class User extends Model
{

	protected $jwt_secret_key = '123Abc#$';
	protected $jwt_expire_time = 10;
	
	public   function findUser($username,$pass){
		if($username=='dapeng' && $pass=='123456'){
			return ['user_id'=>10,'username'=>'dapeng','sex'=>1];
		}
	}
	
	public  function createToken($user=[],$exptime=0,$alg='HS256'){
		//$key = md5($this->jwt_secret_key);
		$key = $this->jwt_secret_key;
		$time = time();
		$expire = $exptime==0?$time+$this->jwt_expire_time:$time+$exptime;
		$payload = [
			$user,
			"iss"=>"PengCompany",
			"aud"=>"dapeng",
			"iat"=>$time,
			"nbf"=>$time,
			"exp"=>$expire
		];
		//设置过期时间
		//JWTUtil::$leeway = 10;
		$jwt = JWTUtil::encode($payload,$key,$alg);
		return $jwt; 
	
	}
	
	
	public function verifyToken($token){
		$key = $this->jwt_secret_key;
		try{
			$jwtAuth = json_encode(JWTUtil::decode($token,new Key($key, 'HS256')));
			$authInfo = json_decode($jwtAuth,true);
			return ['msg'=>'token正常','status'=>1,'data'=>$authInfo[0]];
		}catch(ExpiredException $e){
			return ['msg'=>'token expire','status'=>2,'data'=>[]];
		}catch(\Exception $e){
			return ['msg'=>'token error','status'=>3,'data'=>[$e->getMessage()]];
		}
		
	}
}