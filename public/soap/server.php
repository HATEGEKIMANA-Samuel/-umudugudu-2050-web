<?php
 
 /**
  * the server class for this sopa protocol
  */
 class server {
     
    public function __construct() {
         
     }
	public function getDetails($idnum){
	 	
    $nid = '1 1990 8 0001975 0 28';
    
	require_once('nusoap/lib/nusoap.php');
	try
	{
	 //	$client = new nusoap_client("http://10.10.20.3/rppa/index.php?wsdl", true);
	//	$client = new nusoap_client("http://tprweb/onlineauthentication/Citizen.svc?wsdl", true);
                $client = new nusoap_client("HTTP://nidagateway/onlineauthentication/Citizen.svc?wsdl", true);
		$client->soap_defencoding= 'UTF-8';
		//echo 'It is ok';
		$error  = $client->getError();
	 
		if($error) 
			{
				echo "<h2>Constructor error</h2><pre>" . $error . "</pre>";
			}
		try{
			 $key1="BQ_zKJWfqAR38m4MImRGQ8s1zpqU]@JKWf(Q5ARhd1dOLbzZPUoMo6WeQmxs6p5adP#NR69o7FXM)CJo4X0c[kmsWmP@HOFkN5i8qDgifkVaQtMAqpBn1_aRV&gx]xK]";
			 $key2="";
			 $key=$key1.$key2;
			 //echo $key;
			//$nid="1197080005523026";
			//$nid=$_GET['id'];
			//obj = getCitizenDemographicData($nid, "14", key);
			$params = array("parameters"=>array("DocumentNumber"=>$nid,"OperatorID"=>25,"KeyPhrase"=>$key1));
			$result =$client->call("GetCitizen",$params);
			if($result)
			{
				//echo 'OK';
			}
			else
			{
			 return 'Not OK';
			}
			foreach($result as $key=>$row)
			  {
				$ex['Surnames']=htmlentities($row['Surnames']);
				$ex['ForeName']=htmlentities($row['ForeName']);
				$ex['FatherNames']=htmlentities($row['FatherNames']);
				$ex['MotherNames']=htmlentities($row['MotherNames']);
				$ex['DateOfBirth']=htmlentities($row['DateOfBirth']);
				$ex['DocumentNumber']=htmlentities($row['DocumentNumber']);
				$ex['Cell']=htmlentities($row['Cell']);
				$ex['CivilStatus']=htmlentities($row['CivilStatus']);
				$ex['District']=htmlentities($row['District']);
				$ex['PlaceOfBirth']=htmlentities($row['PlaceOfBirth']);
				$ex['PlaceOfIssue']=htmlentities($row['PlaceOfIssue']);
				$ex['Province']=htmlentities($row['Province']);
				$ex['Sex']=htmlentities($row['Sex']);
				$ex['Sector']=htmlentities($row['Sector']);
				$ex['Village']=htmlentities($row['Village']);
				$ex['VillageID']=htmlentities($row['VillageID']);
				$ex['Signature']=htmlentities($row['Signature']);	
				$ex['Photo']=htmlentities($row['Photo']);
			 }
 			
		   return $ex;

		}
		catch(Exception $e)
		{
			return $e->getMessage();
		}
	//echo "<pre>" . htmlspecialchars($client->request, ENT_QUOTES) . "</pre></br>";
    //echo "<pre>" . htmlspecialchars($client->response, ENT_QUOTES) . "</pre></br>";
  


	}
	catch(Exception $e)
	{
		return $e->getMessage();
	}
	 
	 
	 ////
 
	 }
 }
 $params = array('uri' => 'http://10.10.83.2/rwandaevents/nida/server.php');
 $server = new SoapServer(NULL, $params);
 $server->setClass('server');
 $server->handle();
