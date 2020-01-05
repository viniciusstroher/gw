<?php
	
	class Utils{
		public static $rootPath;
		public static function getEnvVars($pathEnv){
			if(!file_exists($pathEnv)){
				throw new Exception("Não foi possivel achar o arquivo env", 1);
			}

			$dotenv  = Dotenv\Dotenv::createImmutable($pathEnv);
			$envVars = $dotenv->load();
			if(empty($envVars)){
				throw new Exception("arquivo .env vazio", 1);
			}

			return $envVars;
		}
		
		public static function log($msg, $print = false){
			$msg = date("[Y-m-d H:i:s] "). $msg."\n";
			
			if($print){
				print $msg;
			}

			file_put_contents(Utils::$rootPath.DIRECTORY_SEPARATOR."log".DIRECTORY_SEPARATOR."app.log",$msg,FILE_APPEND);
		}

		#METODOS DO ADB		
		public static function isAdbOnline(){
			$shellExecReturn = trim(shell_exec("pgrep adb"));
			
			if(is_numeric($shellExecReturn)){
				return true;
			}

			return false;
		}

		public static function isAdbSmartphoneOnline($ip){
			//telnet limitado por 1 segundo na porta do adb 5555
			$shellExecReturn = trim(shell_exec("timeout 1 telnet $ip 5555"));
			
			if(strpos($shellExecReturn, "Connected") == true){
				return true;
			}

			return false;
			
		}

		public static function killServer(){
			//mata adb
			shell_exec("adb kill-server");
		}
		
		public static function startServer(){
			//mata adb
			$return = shell_exec("adb start-server");
			if(strpos($return, "daemon started successfully") == true){
				return true;
			}

			return false;
		}

		public static function connect($ip){
			//mata adb
			$return = shell_exec("adb connect ".$ip);
			if(strpos($return, "connected") == true){
				return true;
			}
			
			return false;
		}


		public static function addContactGenyMotionSmartPhone($number){
			//fecha todos contatos
			$cmd = "adb shell am force-stop com.samsung.android.contacts && adb shell am force-stop com.android.contacts";
			shell_exec($cmd);
			//$number = "+55 51 9541-2459";
			$numberReplaced = str_replace(array("+"," ","-"), "", $number);
			$timeout = 3;
			$return = shell_exec("adb shell input keyevent KEYCODE_HOME && adb shell am force-stop com.android.contacts && adb shell am start -a android.intent.action.INSERT -t vnd.android.cursor.dir/contact -e name \"$numberReplaced\" -e phone \"$number\" && sleep $timeout && adb shell input tap 215 28");

		}

		

		public static function isNumberWhatsApp($number){
			$numberReplaced = str_replace(array("+"," ","-"), "", $number);
			$cmd = "adb shell content query --uri content://com.android.contacts/raw_contacts --where \"display_name=\'$numberReplaced\' and account_type=com.whatsapp.w4b\"";
			

			$return = shell_exec($cmd);
			if(strpos($return, "whatsapp") !== false){
			    return true;
			} else{
				return false;
			}  
		}


		public static function isNumberExistsInGenyMotionAndroid($number){
			$numberReplaced = str_replace(array("+"," ","-"), "", $number);
			$cmd = "adb shell content query --uri content://com.android.contacts/raw_contacts --where \"display_name=\'$numberReplaced\' and deleted=0\"";
			//account_type=com.whatsapp.w4b
			$return = shell_exec($cmd);
			if(strpos($return, "No result found") !== false){
			    return true;
			}
			return false;
		}
		
	}