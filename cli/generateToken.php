<?php

require_once __DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."includes.dependencies.php";

try{

	$username = $argv[1];
	if(empty($username)){
		throw new Exception("Nao passou o parametro username", 1);
		
	}

	$envVars = Utils::getEnvVars(Utils::$logPath);
	$db = new PgSql($envVars['host'],
					$envVars['port'],
					$envVars['db'],
					$envVars['username'],
					$envVars['password']);

	$sql = "select * from users where username='$username'";
	$rsUser = $db->getRow($sql);

	print "Authorizaiton: Basic ".base64_encode($rsUser['username'].":".$rsUser['password'])."\n";


}catch(Exception $e){
	
	Utils::log($e->getMessage());
	print $e->getMessage()."\n";

}finally{
	$db->close();
}