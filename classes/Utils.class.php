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
		
		public static function parseNumber($number){
			$number = str_replace(' ', '\ ', $number);
			$number = str_replace('+', '\+', $number);
			$number = str_replace('-', '\-', $number);
			// $number = escapeshellcmd($number);
			
			return $number;
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
			
			Utils::log("[CRON][cronAddContacts] isAdbOnline: $shellExecReturn \n");
			
			if(is_numeric($shellExecReturn)){
				return true;
			}

			return false;
		}

		public static function isSmartphoneConnected(){
			$shellExecReturn = trim(shell_exec("adb get-state 1>/dev/null 2>&1 && echo 'online' || echo 'offline'"));
			
			Utils::log("[CRON][cronAddContacts] isSmartphoneConnected: $shellExecReturn \n");
			
			if(strpos($shellExecReturn, "online") !== false){
				return true;
			}

			return false;
		}

		public static function isAdbSmartphoneOnline($ip){
			//telnet limitado por 1 segundo na porta do adb 5555
			$shellExecReturn = trim(shell_exec("timeout 1 telnet $ip 5555"));
			
			Utils::log("[CRON][cronAddContacts] isAdbSmartphoneOnline: $shellExecReturn \n");
		
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
			
			Utils::log("[CRON][cronAddContacts] startServer: $return \n");
			
			if(strpos($return, "daemon started successfully") !== false){
				return true;
			}

			return false;
		}

		public static function connect($ip){
			//mata adb
			$return = shell_exec("adb connect ".$ip);
			
			Utils::log("[CRON][cronAddContacts] connect: $return \n",true);
			
			if(strpos($return, "connected") !== false){
				return true;
			}
			
			return false;
		}


		public static function addContactGenyMotionSmartPhone($number){
			//fecha todos contatos
			$cmd = "adb shell am force-stop com.samsung.android.contacts && adb shell am force-stop com.android.contacts";
			shell_exec($cmd);
			//$number = "+55 51 9541-2459";
			$numnumberberReplaced = Utils::parseNumber($number);
			
			$timeout = 3;

			// $cmd = "adb shell input keyevent KEYCODE_HOME && adb shell am force-stop com.android.contacts && adb shell am start -a android.intent.action.INSERT -t vnd.android.cursor.dir/contact -e name \"$numnumberberReplaced\" -e phone \"$numnumberberReplaced\" && sleep $timeout && adb shell input tap 215 28";

			$cmd = "adb shell input keyevent KEYCODE_HOME && adb shell am force-stop com.google.android.contacts && adb shell am start -a android.intent.action.INSERT -t vnd.android.cursor.dir/contact -e name \"$numnumberberReplaced\" -e phone \"$numnumberberReplaced\" && sleep $timeout && adb shell input tap 215 28";
			
			Utils::log("[CRON][cronAddContacts] addContactGenyMotionSmartPhone: $number \n".$cmd);

			$return = shell_exec($cmd);

			Utils::log("[CRON][cronAddContacts] addContactGenyMotionSmartPhone: $number \n".$return);

			//se achou erro retornar falso - nao adicionou o contato
			// if(strpos($return, "Error") !== false){
			//     return false;
			// }
			
			return true;
			  
		}

		

		public static function isNumberWhatsApp($number){
			// $numberReplaced = str_replace(array("+"," ","-"), "", $number);
			$numberReplaced = $number;
			$cmd = "adb shell content query --uri content://com.android.contacts/raw_contacts --where \\\"display_name=\'$numberReplaced\' and deleted=0 and account_type=\'com.whatsapp.w4b\'\\\"";
			
			Utils::log("[CRON][cronAddContacts] isNumberExistsInGenyMotionAndroid: $number \n".$cmd);

			$return = shell_exec($cmd);

			Utils::log("[CRON][cronAddContacts] isNumberWhatsApp: $number \n".$return);

			if(strpos($return, "whatsapp") !== false){
			    return true;
			} else{
				return false;
			}  
		}


		public static function isNumberExistsInGenyMotionAndroid($number){
			// $numberReplaced = str_replace(array("+"," ","-"), "", $number);
			$numberReplaced = $number;
			
			$cmd = "adb shell content query --uri content://com.android.contacts/raw_contacts --where \\\"display_name=\'$numberReplaced\' and deleted=0\\\"";
			
			Utils::log("[CRON][cronAddContacts] isNumberExistsInGenyMotionAndroid: $number \n".$cmd);
			
			$return = shell_exec($cmd);

			Utils::log("[CRON][cronAddContacts] isNumberExistsInGenyMotionAndroid: $number \n".$return."\n Added? ".var_export((strpos($return, "No result found.") !== false),true));
			
			if(strpos($return, $numberReplaced) !== false){
			    return true;
			}
			return false;
		}
		
	}