<?php
	date_default_timezone_set('America/Los_Angeles');
	require_once __DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."includes.dependencies.php";

try{
	$date = date("[Y-m-d H:i:s] ");

	$isDeviceConnected 	   = Utils::isSmartphoneConnected();
	Utils::log("[CRON][cronAddContact] isDeviceConnected: ".var_export($isDeviceConnected,true),true);
	if(!$isDeviceConnected){
		exit;
	}

	$envVars = Utils::getEnvVars(Utils::$rootPath);
	$db = new PgSql($envVars['host'],
					$envVars['port'],
					$envVars['db'],
					$envVars['username'],
					$envVars['password']);
	
	$numbersObj = new Numbers($db);

	$numberAddedked = $numbersObj->getAddedNumber(1);
	if(count($numberAddedked) == 0){
		Utils::log("[CRON][cronGetContacts] Nenhum contato para ser analisado",true);
		exit;
	}

	Utils::log("[CRON][cronGetContacts] ".count($numberAddedked)." contatos para serem analisados",true);

	//edita numero 
	$whats = Utils::isNumberWhatsApp($numberAddedked[0]['numbers']);
	Utils::log("[CRON][cronGetContacts] isNumberWhatsApp number: ".$numberAddedked[0]['numbers']." - whats: ".var_export($whats,true),true);
	$numbersObj->updateNumber($numberAddedked[0]['numbers'],'CHECKED',$whats);
}catch(Exception $e){
	
	Utils::log("[CRON][cronGetContacts][ERROR] ".$e->getMessage(),true);

}finally{
	if(!empty($db)){
		$db->close();
	}
}