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
			return ['user_id'=>10,'username'=>'meiling2','sex'=>2];
		}
	}
	
	public  function createToken($user=[],$exptime=0,$alg='RS256'){
		
		
		
		//$key = md5($this->jwt_secret_key);
		$private_key = file_get_contents("./keys/rsa_private_key.pem");
		if(openssl_pkey_get_private($private_key)){
			$time = time();
			$expire = $exptime==0?$time+$this->jwt_expire_time:$time+$exptime;
			$payload = [
				$user,
				"iss"=>"PengCompany",//签发组织
				"aud"=>"dapeng",//签发作者
				"iat"=>$time,
				"nbf"=>$time,
				"exp"=>$expire
			];
			//设置过期时间
			//JWTUtil::$leeway = 10;
			$jwt = JWTUtil::encode($payload,$private_key,$alg);
			return $jwt;
			 
		}else{
			return false;
		}
	
	}
	
	
	public function verifyToken($token){
		$public_key = file_get_contents("./keys/rsa_public_key.pem");
		try{
			$jwtAuth = json_encode(JWTUtil::decode($token,new Key($public_key, 'RS256')));
			$authInfo = json_decode($jwtAuth,true);
			//print_r(  JWTUtil::decode($token,new Key($key, 'RS256'))  );
			return ['msg'=>'token正常','status'=>1,'data'=>$authInfo[0]];
		}catch(ExpiredException $e){
			return ['msg'=>'token expire','status'=>2,'data'=>[]];
		}catch(\Exception $e){
			return ['msg'=>'token error','status'=>3,'data'=>[$e->getMessage()]];
		}
		
	}
}