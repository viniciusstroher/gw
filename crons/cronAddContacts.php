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


	$numberUnchecked = $numbersObj->getUncheckedNumber(1);
	if(count($numberUnchecked) == 0){
		Utils::log("[CRON][cronAddContact] Nenhum contato para ser adicionado",true);
		exit;
	}
	
	Utils::log("[CRON][cronAddContact] ".count($numberUnchecked)." contatos para serem adicionados",true);
	
	//verifica se adb esta online
	// $isAdbOnline = Utils::isAdbOnline();
	// Utils::log("[CRON][cronAddContact] isAdbOnline: ".var_export($isAdbOnline,true),true);
	
	// if(!$isAdbOnline){
	// 	Utils::startServer();
	// }

	// $isAdbOnline = Utils::isAdbOnline();
	// if(!$isAdbOnline){
	// 	Utils::log("[CRON][cronAddContact] isAdbOnline: ".var_export($isAdbOnline,true)."..... Saindo...",true);
	// 	exit;
	// }

	// //verifica se dispositivo esta vivo
	// $isAdbSmartphoneOnline = Utils::isAdbSmartphoneOnline($envVars['ipandroid']);
	// Utils::log("[CRON][cronAddContact] isAdbSmartphoneOnline: ".var_export($isAdbSmartphoneOnline,true),true);

	$isDeviceConnected 	   = Utils::isSmartphoneConnected();
	$isSmartphoneConnected = false;
	
	Utils::log("[CRON][cronAddContact] isDeviceConnected: ".var_export($isDeviceConnected,true),true);
	if(!$isDeviceConnected){
		//verifica se o genytmotion esta connectado ao adb
		$isSmartphoneConnected = Utils::connect($envVars['ipandroid']);
		Utils::log("[CRON][cronAddContact] isSmartphoneConnected: ".var_export($isSmartphoneConnected,true),true);
	}
	
	if(!$isSmartphoneConnected){
		Utils::killServer();
		Utils::log("[CRON][cronAddContact] killServer",true);	

		Utils::startServer();
		Utils::log("[CRON][cronAddContact] startServer",true);	
		exit;
	}

	//adiciona numero se nao existir
	$existsNumber = Utils::isNumberExistsInGenyMotionAndroid($numberUnchecked[0]['numbers']);
	Utils::log("[CRON][cronAddContact] ".$numberUnchecked[0]['numbers']."  existsNumber: ".var_export($existsNumber,true),true);
	if(!$existsNumber){
		Utils::addContactGenyMotionSmartPhone($numberUnchecked[0]['numbers']);
		Utils::log("[CRON][cronAddContact] addContactGenyMotionSmartPhone adicionando: ".$numberUnchecked[0]['numbers'],true);
		
	}


	$existsNumber = Utils::isNumberExistsInGenyMotionAndroid($numberUnchecked[0]['numbers']);
	Utils::log("[CRON][cronAddContact] ".$numberUnchecked[0]['numbers']." now existsNumber: ".var_export($existsNumber,true),true);
	if($existsNumber){
		$numbersObj->updateNumber($numberUnchecked[0]['numbers'],'ADDED');
	}
}catch(Exception $e){
	
	Utils::log("[CRON][cronAddContact][ERROR] ".$e->getMessage(),true);

}finally{
	if(!empty($db)){
		$db->close();
	}
}