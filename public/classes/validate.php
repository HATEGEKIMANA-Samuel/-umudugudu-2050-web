<?php 
class validate{
	private $_passed = false,
	        $_errors = array(),
	        $_db = null;
	public function __construct(){
		global $database;
		$this->_db = $database;
	}
	public function check($source, $items = array()){
		foreach($items as $item => $rules){
			$value ="";
		  	if (isset($source[$item]) && !empty($source[$item])) {
				$value = trim($source[$item]);
				foreach($rules as $rule => $rule_value){

					$item =  str_replace('_', ' ', $item);
					switch($rule){
						case 'required':
						if(empty($value) &&  $rule_value== true){
							$this->adderror("{$item} is required");
						}
						break;
						case 'min':
						    if(strlen($value)< $rule_value){
							$this->adderror("{$item} must be a minimum of {$rule_value} lettre(s)");
						}
						break;
						case 'max':
						    if(strlen($value) > $rule_value){
							$this->adderror("{$item} must be of maximum of {$rule_value} lettre(s)");
						}
						break;
						case 'number':
						    if(!is_numeric($value) && $rule_value== true){
								$this->adderror("{$item} can only contain digits");
							}elseif(is_numeric($value) && $rule_value== false){
								$this->adderror("{$item} can not be a number");
							}
						break;
						case 'matches':
						    if($value != $source[$rule_value]){
								$this->adderror(str_replace('_', ' ', $rule_value)." must match {$item}");
							}
						break;
						case 'unique':
							$where = $rule_value['column']."= '$value' ";
							if ($rule_value['except']!=null) {
								$where .= "AND ".$rule_value['except']['column'] ."!='".$rule_value['except']['value']."'";
							}
							if(!empty($this->_db->getArray("id",$rule_value['table'],$where))) {
								$this->adderror("{$item} has been taken.");
							}
						
						break;
					}
				}
			}elseif (isset($_FILES[$item]['tmp_name']) && !empty($_FILES[$item]['tmp_name'])) {
				//rename the file
				$_FILES[$item]['name'] = date('Ymdhisu').'.'.pathinfo($_FILES[$item]['name'], PATHINFO_EXTENSION);

				foreach($rules as $rule => $rule_value){
					$filename = str_replace('_', ' ', $item);
					$file =  $item;

					switch($rule){
						case 'min':
						    if($_FILES[$item]['size'] < $rule_value){
							$this->adderror("{$filename} must be a minimum of ".$rule_value/1000000 ."KB".$_FILES[$item]['size']);
						}
						break;
						case 'max':
						    if($_FILES[$item]['size'] > $rule_value){
							$this->adderror("{$filename} must be of maximum of".$rule_value/1000000 ."KB");
						}
						break;
						case 'type':
						    if(!in_array(pathinfo($_FILES[$item]['name'], PATHINFO_EXTENSION), explode(",", $rule_value))){
								$this->adderror("{$file} must be one of these types: {$rule_value}");
							}
						break;
					}
				}
			}else{
				$this->adderror("{$item} is required");
			}
			
		}
		if(!$this->errors()){
			$this->_passed = true;
		}
		return $this;
	}
	
	private function adderror($error){
		$this->_passed = false;
		$this->_errors[]=$error;
	}
	public function errors(){
		return $this->_errors;
	}
	public function passed(){
		return $this->_passed;
	}
	public function setPassed($value=false){
		$this->_passed = $value;
	}
}


?>