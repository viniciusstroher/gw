<?php
	date_default_timezone_set('America/Los_Angeles');
	require_once __DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."includes.dependencies.php";

try{
	$date = date("[Y-m-d H:i:s] ");

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

	//verifica se adb esta online
	$isAdbOnline = Utils::isAdbOnline();
	Utils::log("[CRON][cronGetContacts] isAdbOnline: ".var_export($isAdbOnline,true),true);
	
	if(!$isAdbOnline){
		//inicia adb se estiver parado
		Utils::startServer();
		//throw new Exception("Adb esta offline.", 1);
	}

	$isAdbOnline = Utils::isAdbOnline();
	if(!$isAdbOnline){
		Utils::log("[CRON][cronGetContacts] isAdbOnline: ".var_export($isAdbOnline,true)."..... Saindo...",true);
		exit;
	}

	//verifica se dispositivo esta vivo
	$isAdbSmartphoneOnline = Utils::isAdbSmartphoneOnline($envVars['ipandroid']);
	Utils::log("[CRON][cronGetContacts] isAdbSmartphoneOnline: ".var_export($isAdbSmartphoneOnline,true),true);
	
	//verifica se o genytmotion esta connectado ao adb
	$isSmartphoneConnected = Utils::connect($envVars['ipandroid']);
	Utils::log("[CRON][cronGetContacts] isSmartphoneConnected: ".var_export($isSmartphoneConnected,true),true);

	//edita numero 
	$whats = Utils::getNumberGenyMotionSmartPhone($numberAddedked[0]['numbers']);
	Utils::log("[CRON][cronGetContacts] number: ".$numberAddedked[0]['numbers']." - whats: ".var_export($whats,true),true);
	$numbersObj->updateNumber($numberAddedked[0]['numbers'],'CHECKED',$whats);
}catch(Exception $e){
	
	Utils::log("[CRON][cronGetContacts][ERROR] ".$e->getMessage(),true);

}finally{
	$db->close();
}