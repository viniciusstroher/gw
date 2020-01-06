<?php
	date_default_timezone_set('America/Sao_Paulo');
	
	//GET NUMBER
	//INSERT NUMBER  - STATUS UNCHECKED PASSA POR CRON
	require_once __DIR__.DIRECTORY_SEPARATOR."includes.dependencies.php";
try{

	//ACCESS LOG
	$access_data = array();
	$access_data['SERVER']  = $_SERVER;
	$access_data['REQUEST'] = $_REQUEST;
	$access_data = json_encode($access_data); 
	Utils::log($access_data);



	if(empty($_POST)){
		Utils::log("O acesso a esta api precisa ser feito pelo method post");
		throw new Exception("O acesso a esta api precisa ser feito pelo method post", 1);
	}


	$envVars = Utils::getEnvVars(Utils::$rootPath);
	$db = new PgSql($envVars['host'],
					$envVars['port'],
					$envVars['db'],
					$envVars['username'],
					$envVars['password']);

	$user_id = Auth::check($db);
	if(!$user_id){
		Utils::log("Usuario invalido");
		throw new Exception("Usuario invalido", 1);
	}

	Utils::Log("Usuario $user_id authenticado.");

	$action = @$_POST['action'];
	$response = array();
	if(empty($action)){
		Utils::log("Faltou parametro action");
		throw new Exception("Faltou parametro action", 1);	
	}

	switch ($action) {
		case 'checkNumber':
			$ddi 	= @$_POST['ddi'];
			$ddd    = @$_POST['ddd'];
			$number = @$_POST['number'];

			if(empty($number) || empty($ddd)){
				Utils::log("number ou ddd não pode ser null");
				throw new Exception("number ou ddd não pode ser null", 1);
			}

			if(!empty($ddi)){
				$ddi = "+".$ddi;
			}

			$number = $ddi." ".$ddd."-".$number;

			$numbersObj = new Numbers($db);
			$rsNumber = $numbersObj->getNumber($number);
			if(empty($rsNumber)){
				$numbersObj->addNumber($user_id,$number);
				$rsNumber = $numbersObj->getNumber($number);
			}

			$response['data'] = $rsNumber;
			
			break;
		
		default:
			throw new Exception("Action invalida", 1);
			break;
	}

	print json_encode($response);

}catch(Exception $e){
	Utils::log($e->getMessage());
	print json_encode(array("error"=>$e->getMessage()));	
}finally {

	if(!empty($db)){
		$db->close();
	}
}