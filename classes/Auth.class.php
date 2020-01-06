<?php

class Auth
{
	public static function check(PgSql $db){
		//verifica header do request
		if(!isset($_SERVER['PHP_AUTH_USER'])){
		    throw new Exception("Header Authorization não foi passado",1);
		}

		// Authorization: Basic base64($user:$password)
		$username = $_SERVER['PHP_AUTH_USER'];
		$password = $_SERVER['PHP_AUTH_PW'];
		
		if(empty($username) || empty($password)){
			throw new Exception("Username ou password vazios", 1);
		}

		$sql = "SELECT id FROM users WHERE username = '$username' and password = '$password'";
		$rsUser = $db->getRow($sql);

		if(empty($rsUser)){
			return false;
		}

		return $rsUser['id'];
	}	
}