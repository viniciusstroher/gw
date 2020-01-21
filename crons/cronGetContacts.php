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

	$numberAdded = $numbersObj->getAddedNumber($envVars['fetch_get_number']);
	if(count($numberAdded) == 0){
		Utils::log("[CRON][cronGetContacts] Nenhum contato para ser analisado",true);
		exit;
	}

	Utils::log("[CRON][cronGetContacts] ".count($numberAdded)." contatos para serem analisados",true);
	foreach ($numberAdded as $key => $nA) {
		//edita numero 
		$whats = Utils::isNumberWhatsApp($nA['numbers']);
		Utils::log("[CRON][cronGetContacts] isNumberWhatsApp number: ".$nA['numbers']." - whats: ".var_export($whats,true),true);
		$numbersObj->updateNumber($nA['numbers'],'CHECKED',$whats);
	}
	
}catch(Exception $e){
	
	Utils::log("[CRON][cronGetContacts][ERROR] ".$e->getMessage(),true);

}finally{
	if(!empty($db)){
		$db->close();
	}
}