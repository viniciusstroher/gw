<?php
	date_default_timezone_set('America/Los_Angeles');
	require_once __DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."includes.dependencies.php";

try{

	$date = date("[Y-m-d H:i:s] ");
	

	$isDeviceConnected 	   = Utils::isSmartphoneConnected();
	Utils::log("[CRON][cronCheckAdb] isDeviceConnected: ".var_export($isDeviceConnected,true),true);
	if(!$isDeviceConnected){
		Utils::killServer();
		Utils::startServer();

		$envVars = Utils::getEnvVars(Utils::$rootPath);
		$isConnected = Utils::connect($envVars['ipandroid']);
		Utils::log("[CRON][cronCheckAdb] isConnected: ".$isConnected,true);
	}

}catch(Exception $e){
	
	Utils::log("[CRON][cronCheckAdb][ERROR] ".$e->getMessage(),true);

}finally{
	if(!empty($db)){
		$db->close();
	}
}