<?php
class Numbers{
	private $db;
	
	public function __construct(PgSql $db){
		$this->db = $db;
	}

	function getNumber($number){
		
	    $sqlCheck = "SELECT * 
	    			 FROM numbers 
	    			 WHERE numbers 
	    			 like '%$number%'";
	    $numbers = $this->db->getRows($sqlCheck);
        return $numbers;

	}

	function getUncheckedNumber($limit = null){
		
	    //pega mais de 1min
	    $sqlCheck = "SELECT * 
	    			 FROM numbers 
	    			 WHERE status = 'UNCHECKED' 
	    			 and EXTRACT(EPOCH FROM (now() - created_at)) > 60 ";
	    ;
	    if(!empty($limit)){
	    	$sqlCheck .= "LIMIT $limit";
	    }

		$numbers = $this->db->getRows($sqlCheck);
        return $numbers;

	}

	function getAddedNumber($limit = null){
 
	    //pega mais de 1min
	    $sqlCheck = "SELECT * 
	    			 FROM numbers 
	    			 WHERE status = 'ADDED' 
	    			 and EXTRACT(EPOCH FROM (now() - created_at)) > 60 ";
	    if(!empty($limit)){
	    	$sqlCheck .= "LIMIT $limit";
	    }

	    $numbers = $this->db->getRows($sqlCheck);
        return $numbers;

	}

	function addNumber($number,$status = 'UNCHECKED',$whats = false){
  
	    $sqlCheck  = "SELECT COUNT(*) numbers 
	    			  FROM numbers 
	    			  WHERE numbers = '$number'";
	    $row = $this->db->getRow($sqlCheck);
       
		if($row['numbers'] > 0){
			//ja tem este numero
			Utils::log("Numero {$number} já existe na base");
			throw new Exception("Numero {$number} já existe na base", 1);
		}

		$whats = $whats ? 'true':'false';

	    $sqlInsert = "INSERT INTO numbers (numbers,whats,status,created_at,updated_at) 
	    			  VALUES ('$number',$whats,'$status',now(),null)";

	    $id = $this->db->insert($sqlInsert);
		if(!is_numeric($id)){
			throw new Exception("Error inserir numero", 1);
		}
		
		Utils::log($sqlInsert);

		return $id;

	}

	function updateNumber($number,$status = 'UNCHECKED',$whats = false){
		
	   	$whats = $whats ? 'true':'false';
	   	$sqlCheck = "UPDATE numbers 
	    			 SET numbers = '$number', 
	    			 	 status  = '$status', 
	    			 	 whats   = $whats,
	    			 	 updated_at = now()
	    			 WHERE numbers  = '$number'";
	    $this->db->exec($sqlCheck);
		
	    Utils::log($sqlCheck);

		return true;

	}


	function deleteNumber($number){
    
	    $sqlCheck = "DELETE FROM numbers 
	    			 WHERE numbers = '$number'";
	    $this->db->exec($sqlCheck);

	    Utils::log($sqlCheck);
	    
		return true;

	}
}
