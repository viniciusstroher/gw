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

	$numberUncheckeds = $numbersObj->getUncheckedNumber(5);
	
	if(count($numberUncheckeds) == 0){
		Utils::log("[CRON][cronAddContact] Nenhum contato para ser adicionado",true);
		exit;
	}

	foreach ($numberUncheckeds as $key => $numberUnchecked) {
		
		
		Utils::log("[CRON][cronAddContact] ".count($numberUnchecked)." contatos para serem adicionados",true);

		//adiciona numero se nao existir
		$existsNumber = Utils::isNumberExistsInGenyMotionAndroid($numberUnchecked['numbers']);
		Utils::log("[CRON][cronAddContact] ".$numberUnchecked['numbers']."  existsNumber: ".var_export($existsNumber,true),true);
		if(!$existsNumber){
			Utils::addContactGenyMotionSmartPhone($numberUnchecked['numbers']);
			Utils::log("[CRON][cronAddContact] addContactGenyMotionSmartPhone adicionando: ".$numberUnchecked['numbers'],true);
		}

		$existsNumber = Utils::isNumberExistsInGenyMotionAndroid($numberUnchecked['numbers']);
		Utils::log("[CRON][cronAddContact] ".$numberUnchecked['numbers']." now existsNumber: ".var_export($existsNumber,true),true);

		if($existsNumber){
			$numbersObj->updateNumber($numberUnchecked['numbers'],'ADDED');
		}
		
	}


}catch(Exception $e){
	
	Utils::log("[CRON][cronAddContact][ERROR] ".$e->getMessage(),true);

}finally{
	if(!empty($db)){
		$db->close();
	}
}