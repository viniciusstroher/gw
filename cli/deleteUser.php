<?php

require_once __DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."includes.dependencies.php";


try{
	
	$username = $argv[1];
	
	if(empty($username)){
		throw new Exception("Parametro username nao pode ser vazio", 1);
	}

	$envVars = Utils::getEnvVars(Utils::$rootPath);
	$db = new PgSql($envVars['host'],
					$envVars['port'],
					$envVars['db'],
					$envVars['username'],
					$envVars['password']);

	$sql = "delete from users where username = '$username'";
	$db->exec($sql);

	print "User $username deleted\n";

}catch(Exception $e){
	
	Utils::log($e->getMessage());
	print $e->getMessage()."\n";

}finally{
	if(!empty($db)){
		$db->close();
	}
}