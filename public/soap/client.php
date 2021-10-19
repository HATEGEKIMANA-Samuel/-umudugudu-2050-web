<?php
class clientp {
	
  public function __construct() {
		$params = array('location' => 'http://10.10.83.2/rwandaevents/nida/server.php', 
		                'uri' => 'urn://10.10.83.2/rwandaevents/nida/server.php',
						'trace' => 1);
	$this->instance = new SoapClient(NULL, $params);
	}
	
 public function getName($idNum){
 	return $this->instance->__soapCall('getDetails', $idNum);
 }
	
}
$client = new clientp;


?>