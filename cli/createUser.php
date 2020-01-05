<?php

require_once __DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."includes.dependencies.php";


try{
	
	$username = $argv[1];
	$password = $argv[2];

	if(empty($username) || empty($password)){
		throw new Exception("Parametro username e password nao podem ser vazios", 1);
	}

	$envVars = Utils::getEnvVars(Utils::$logPath);
	$db = new PgSql($envVars['host'],
					$envVars['port'],
					$envVars['db'],
					$envVars['username'],
					$envVars['password']);

	$sql = "insert into users (username,password,created_at) values ('$username','$password',now())";
	$id  = $db->insert($sql);

	if(!is_numeric($id)){
		throw new Exception("Erro ao criar usuario", 1);
	}

	print "Authorizaiton: Basic ".base64_encode($username.":".$password)."\n";

}catch(Exception $e){
	
	Utils::log($e->getMessage());
	print $e->getMessage()."\n";

}finally{
	$db->close();
}