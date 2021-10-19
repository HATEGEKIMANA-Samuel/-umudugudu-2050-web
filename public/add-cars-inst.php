<?php 
require_once("includes/validate_credentials.php");
if (isset($_GET['inst'])){
	$institution = encrypt_decrypt('decrypt', $_GET['inst']);
}
if (isset($_POST['institution'])) {
	$institution = $_POST['institution'];
}
if (!isset($institution) || !is_numeric($institution)) {
	header("location:404");
}
if (isset($_GET['cr'])){
	$car = encrypt_decrypt('decrypt', $_GET['cr']);
}
if (isset($car) && !is_numeric($car)) {
    $dpl=rawurlencode(encrypt_decrypt('encrypt', $institution));
	header("location:add-cars?dpl=$dpl");
}

$messages = '';
if (isset($_GET['messages']) && $_GET['messages'] ==1){
	$messages = "<span style='color:red;'>Found another  car with same plate/chassis number</span>";
}
if (isset($_GET['messages']) && $_GET['messages'] ==2) {
	$messages = "<span style='color:red;'>Please fill out all required fields</span>";
}
if (isset($_POST['save_car'])) {
	 
$plate = htmlentities($database->escape_value($_POST['plate']));
$model = htmlentities($database->escape_value($_POST['model']));
$chassis = htmlentities($database->escape_value($_POST['chassis']));
$year = htmlentities($database->escape_value($_POST['year']));
$other_info = htmlentities($database->escape_value($_POST['other_info']));

if (!isset($_POST['id_to_edit'])){
	$platetocheck = strtolower($plate);
	$chassistocheck = strtolower($chassis);
	$query =$database->query("SELECT id FROM cars WHERE (LOWER(plate)='$platetocheck' OR LOWER(chassis) = '$chassistocheck') AND status='1' LIMIT 2 ");
	$found_car  = $database->num_rows($query);
	if ($plate =='' || $model =='' || $chassis =='' || $year =='' ) {
		$messages = "<span style='color:red;'>Please fill out all required fields</span>";
	}else if ($found_car >0) {
		$messages = "<span style='color:red;'>car with same plate/chassis number is already registered</span>";
	} else {
		
	    $sql= "INSERT INTO cars(plate,model,chassis,year,other_info,owner,owner_type,time,user) 
	                       VALUES('$plate','$model','$chassis','$year','$other_info',$institution,'inst',NOW(),{$_SESSION["id"]})";
	    if ($database->query($sql)) {
	        $id=rawurlencode(encrypt_decrypt('encrypt', $institution)).rawurlencode("%cars");
	        header("location:display?inst=$id");
	    }
	}
}else{
	$id =$_POST['id_to_edit'];
	$platetocheck = strtolower($plate);
	$chassistocheck = strtolower($chassis);
	$query =$database->query("SELECT id FROM cars WHERE (LOWER(plate)='$platetocheck' OR LOWER(chassis) = '$chassistocheck') AND id !=$id AND status='1' LIMIT 2 ");
	$found_car  = $database->num_rows($query);
	if ($plate =='' || $model =='' || $chassis =='' || $year ==''){
		$id=rawurlencode(encrypt_decrypt('encrypt', $id));
		$dpl = rawurlencode(encrypt_decrypt('encrypt', $institution));
		header("location:add-cars-inst?inst=$dpl&cr=$id&messages=2");
	}else if ($found_car >0) {
		$id=rawurlencode(encrypt_decrypt('encrypt', $id));
		$dpl = rawurlencode(encrypt_decrypt('encrypt', $institution));
		header("location:add-cars-inst?inst=$dpl&cr=$id&messages=1");
	}else{
    $sql = "UPDATE cars SET plate='$plate',model='$model',chassis='$chassis',year='$year',other_info='$other_info' WHERE id=$id";
    if ($database->query($sql)) {
        $dpl=rawurlencode(encrypt_decrypt('encrypt', $institution)).rawurlencode("%cars");
        header("location:display?inst=$dpl");
    }
   }
}
}

?>
<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!--> 
<html class="no-js" lang=""> <!--<![endif]-->
<head>
<?php require_once("includes/head.php"); ?>
</head>
<body>
<!--     <style type="text/css">
    .error{
      color: maroon;
    }
    select {
    background-color: #F5F5F5;
    border: 1px double #15a6c7;
    color: #1d93d1;
    font-family: Georgia;
    font-weight: bold;
    font-size: 14px;
    height: 39px;
    padding: 7px 8px;
    width: 250px;
    outline: none;
    margin: 10px 0 10px 0;
}
select option, .form-control{
    font-family: Georgia;
    font-size: 14px;
}

label{
    font-weight: bold;
    font-family: serif;
}

       .tab-container{
        background: #fff;
        box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
        transition: 0.3s;
        padding-bottom: 20px;
       }
       .tab-container nav{
        background: transparent;
        border: none;
       }
       .tab-content{
        padding: 0px 40px;
       }
       .tab-content legend{
            margin-bottom: 20px;
            margin-top:40px; 
            text-align: center;
            color: #272c33;
            font-family: georgia;
       }
       .tab-content-body{
        margin: 0 auto;
        width: 80%;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
        transition: 0.3s;
       }
       .btn ,.btn-primary{background: #272c33;color: #fff}
       #nav-tab{width: 80%;margin:0 auto;}
       #nav-tab ul
        {
        margin: 0;
        background: #000;
        padding: 0;
        list-style-type: none;
        text-align: center;
        }

        #nav-tab ul li { display: inline;float: left;padding: 15px 18%;background:#272c33;border-right: 1px solid grey }
        #nav-tab ul li a{
            color: #fff;
        }
        #nav-tab ul li#active{background: #E74C3C !important}
        
        .fiedset-type{
        	border: 1px solid gray;
        	margin-bottom: 10px;
        	
        }
        .fiedset-type .fieldset-legend{
        	width:inherit;
        	padding:0 10px;
        	border-bottom:none;
        	text-align: left;
        	margin-left: 10px;
        	font-family: serif;
        	font-size: inherit;
        	font-weight: bold;
        }
       .fiedset-type .form-group{
       	width: 98%;
       	margin-left: 5px;
       }
    </style> -->


    <style type="text/css">
    .error{
      color: maroon;
    }
  
select option, .form-control{
    font-size: 14px;
}

label{
    /*font-weight: bold;*/
    /*font-family: serif;*/
    font-size: 13px !important;
}


       .tab-container{
        background: #fff;
        /*box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);*/
        transition: 0.3s;
        padding-bottom: 20px;
       }
       .tab-container nav{
        background: transparent;
        border: none;
       }
       .tab-content{
        padding: 0px 40px;
       }
       .tab-content legend{
            margin-bottom: 20px;
            margin-top:40px; 
            text-align: center;
            color: #272c33;
       }
       .tab-content-body{
        margin: 0 auto;
        width: 95%;
        padding: 20px;
        margin-bottom: 20px;
        /*box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);*/
        transition: 0.3s;
       }
       .btn ,.btn-primary{background: #272c33;color: #fff}
       #nav-tab{width: 80%;margin:0 auto;}
       #nav-tab ul
        {
        margin: 0;
        background: #000;
        padding: 0;
        list-style-type: none;
        text-align: center;
        }

        #nav-tab ul li { display: inline;float: left;padding: 15px 18%;background:#272c33;border-right: 1px solid grey }
        #nav-tab ul li a{
            color: #fff;
        }
        #nav-tab ul li#active{background: #E74C3C !important}
        
        .fiedset-type{
          border: 1px solid #eceff0;
          margin-bottom: 10px;
          background-color: #dbecfd57 !important;
          
        }
        .fiedset-type .fieldset-legend{
          width:inherit;
          padding:0 10px;
          border-bottom:none;
          text-align: left;
          margin-left: 10px;
          font-weight: 500
        }
       .fiedset-type .form-group{
        width: 98%;
        margin-left: 5px;
       }
    </style>



    <?php require_once 'includes/left_nav.php'; ?>

    <div id="right-panel" class="right-panel">
        <!-- Header-->
     <?php require_once 'includes/top_nav.php'; ?>
    <div class="container">
        <div class="tab-container">
            <div class="tab-content mt-10" id="nav-tabContent">
                
                    <?php 
	                    if (isset($institution) && is_numeric($institution)) {
	                    	$institution_data = $database->fetch_array($database->query("SELECT name,category_id,country,country_loc FROM institutions WHERE id ='$institution' AND status='1' LIMIT 1 "));
                    		if ($institution_data['category_id']== 4) {
								 $name = $institution_data['name'];
							 }else if ($institution_data['category_id']== 2) {
							 	$foreign  = $database->fetch_array($database->query("SELECT name FROM countries WHERE id ='{$institution_data['country']}' LIMIT 1 "));
								 $name = $foreign['name']." Embassy";
							 }else if ($institution_data['category_id']== 3) {
							 	$location  = $database->fetch_array($database->query("SELECT name FROM countries WHERE id ='{$institution_data['country_loc']}' LIMIT 1 "));
								 $name = "Rwandan Embassy {$location['name']}";
							 }else{
							 	$name = $institution_data['name']." Kigali-Rwanda";
							 } 
						}
						$text = "Adding";
						if (isset($car) && is_numeric($car)) {
	                    		$query =$database->query("SELECT * FROM cars WHERE id = '$car' AND status='1' LIMIT 1");
					            $row  = $database->fetch_array($query);
							$text ="Editing";	
						}
                     ?>
                          
                <div class="tab-content-body">
                   <h4 class="fs-18 text-center mt-20"><?=$text?> <span style="color:green;"><?php echo $name; ?></span>'s Car <hr class="mt-20 mb-20 "></h4>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" id="form">
                	<?php echo $messages;?>
                	<?php if (isset($car) && is_numeric($car)) {?>
                		<input type="hidden" name="id_to_edit" value="<?php echo $car; ?>" />
                	<?php }?>
                	
                	<?php if (isset($institution) && is_numeric($institution)) {?>
                		<input type="hidden" name="institution" value="<?php echo $institution; ?>" />
                	<?php }?>

              <div class="row">    

                  <div class="col-lg-6">
                    <div class="form-group">
                      <label for="name">Plate number<span class="required-mark">*</span></label>
                      <input type="text" class="form-control" maxlength="20" placeholder="Plate Number" name="plate"  value="<?php if (isset($car) && is_numeric($car)){echo $row['plate'];}elseif(isset($_POST['plate'])){echo $_POST['plate'];}?>">
                    </div>
                  </div>

                  <div class="col-lg-6">
                    <div class="form-group">
                      <label for="name">Model<span class="required-mark">*</span></label>
                      <input type="text" class="form-control" maxlength="100" placeholder="Model name" name="model"  value="<?php if (isset($car) && is_numeric($car)){echo $row['model'];}elseif(isset($_POST['model'])){echo $_POST['model'];}?>">
                    </div>
                  </div>
                  
                  <div class="col-lg-6">
                    <div class="form-group">
                      <label for="name">Chassis number<span class="required-mark">*</span></label>
                      <input type="text" class="form-control" maxlength="100" placeholder="Chassis Number" name="chassis"  value="<?php if (isset($car) && is_numeric($car)){echo $row['chassis'];}elseif(isset($_POST['chassis'])){echo $_POST['chassis'];}?>">
                    </div>
                  </div>

                  <div class="col-lg-6">
                    <div class="form-group">
                    <label for="country">Year<span class="required-mark">*</span></label>
                        <select class="form-control" name="year">
                        <option value="">--Choose vehicle's out year--</option>
                         <?php 
                                    $this_year = date("Y");
                        $i=0;
                                  for($j  = 1980; $j<= $this_year; $j++){
                                    if ((isset($row['year'])) && ($j ==$row['year'])) {
                            echo "<option selected value=\"{$j}\">{$j}</option>";
                            $i++;
                          }else if(isset($_POST['year']) && ($j ==$_POST['year']) & $i==0){
                            echo "<option selected value=\"{$j}\">{$j}</option>";
                            $i++;
                          }else{
                            echo "<option value=\"{$j}\">{$j}</option>";
                          }
                                   }
                               ?>
                              </select>
                        </div>
                  </div>
                  
                  <div class="col-lg-12">
                    
                    <div class="form-group">
                      <label for="name">Other Infos</label>
                      <input type="text" class="form-control" maxlength="250" placeholder="Other information"  name="other_info"  value="<?php if (isset($car) && is_numeric($car)){echo $row['other_info'];}elseif(isset($_POST['other_info'])){echo $_POST['other_info'];}?>">
                    </div>

                  </div>

                  <div class="col-lg-4 col-lg-offset-4">
                    <button  type="submit" name="save_car" class="btn btn-primary w-100p pt-10 pb-10 mt-10 fs-15 pull-right">Save</button>
                  </div>
                  
                </div>
                    </form>
                </div>
            </div> 
        </div>
    </div>
 </div>
<script src="assets/js/vendor/jquery-2.1.4.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-datepicker.js"></script>
<script src="assets/js/plugins.js"></script>
<script src="js/ajax.js"></script>
<script src="assets/js/main.js"></script>
<script src="assets/js/custom.js"></script>
<script>
	$('.datepicker').datepicker({
       endDate: '0d',
       format: 'yyyy-mm-dd'
     });
     
     $('.datepicker1').datepicker({
       format: 'yyyy-mm-dd'
     });
  
</script>

</body>
</html>