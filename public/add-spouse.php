<?php 
require_once("includes/validate_credentials.php");
if (isset($_GET['dpl'])){
	$diplomat = encrypt_decrypt('decrypt', $_GET['dpl']);
}
if (isset($_POST['diplomat'])) {
	$diplomat = $_POST['diplomat'];
}
if (!isset($diplomat) || !is_numeric($diplomat)) {
	header("location:404");
}
if (isset($_GET['sp'])){
	$spouse = encrypt_decrypt('decrypt', $_GET['sp']);
}
if (isset($spouse) && !is_numeric($spouse)) {
    $dpl=rawurlencode(encrypt_decrypt('encrypt', $diplomat));
	header("location:add-spouse?dpl=$dpl");
}

$messages = '';
if (isset($_GET['messages']) && $_GET['messages'] ==1){
	$messages = "<span style='color:red;'>Found another  spouse with same passport number</span>";
}
if (isset($_GET['messages']) && $_GET['messages'] ==2) {
	$messages = "<span style='color:red;'>Please fill out all required fields</span>";
}
if (isset($_POST['save_spouse'])) {
	 
$given_name = htmlentities($database->escape_value($_POST['given_name']));
$family_name = htmlentities($database->escape_value($_POST['family_name']));
$other_name = htmlentities($database->escape_value($_POST['other_name']));
$gender = htmlentities($database->escape_value($_POST['gender']));
$dob = htmlentities($database->escape_value($_POST['dob']));
$birth_place = htmlentities($database->escape_value($_POST['birth_place']));
$birth_nationality = htmlentities($database->escape_value($_POST['birth_nationality']));
$other_nationality = htmlentities($database->escape_value($_POST['other_nationality']));
$passport = htmlentities($database->escape_value($_POST['passport']));
$issued_country = htmlentities($database->escape_value($_POST['issued_country']));
$issued_date = htmlentities($database->escape_value($_POST['issued_date']));
$expiry_date = htmlentities($database->escape_value($_POST['expiry_date']));
$rwandan_id = htmlentities($database->escape_value($_POST['rwandan_id']));
$rssb = htmlentities($database->escape_value($_POST['rssb']));
$mmi = htmlentities($database->escape_value($_POST['mmi']));
$email = htmlentities($database->escape_value($_POST['email']));
$phone = htmlentities($database->escape_value($_POST['phone']));
$company = htmlentities($database->escape_value($_POST['company']));
$job_title = htmlentities($database->escape_value($_POST['job_title']));

if (!isset($_POST['id_to_edit'])){
	$passporttocheck = strtolower($passport);
	$query =$database->query("SELECT id FROM spouse WHERE LOWER(passport)='$passporttocheck' AND status='1' LIMIT 2 ");
	$found_diplomat  = $database->num_rows($query);
	if ($given_name =='' || $family_name =='' || $gender =='' || $dob ==''   || $birth_nationality =='' || $passport =='' || $issued_date =='' || $issued_country =='' || $expiry_date =='') {
		$messages = "<span style='color:red;'>Please fill out all required fields</span>";
	}else if ($found_diplomat >0) {
		$messages = "<span style='color:red;'>Spouse with $passport as passport is already registered</span>";
	} else {
		
	    $sql= "INSERT INTO spouse(diplomat,given_name,family_name,other_name,gender,dob,birth_place,birth_nationality,other_nationality,email,phone,rwandan_id,rssb,mmi,passport,issued_country,issued_date,expiry_date,company,job_title,time,user) 
	                       VALUES($diplomat,'$given_name','$family_name','$other_name','$gender','$dob','$birth_place',$birth_nationality,$other_nationality,'$email','$phone','$rwandan_id','$rssb','$mmi','$passport',$issued_country,'$issued_date','$expiry_date','$company','$job_title',NOW(),{$_SESSION["id"]})";
	    if ($database->query($sql)) {
	        $id=rawurlencode(encrypt_decrypt('encrypt', $diplomat)).rawurlencode("%spouse");
	        header("location:diplomats?dpl=$id");
	    }
	}
}else{
	$id =$_POST['id_to_edit'];
	$passporttocheck = strtolower($passport);
	$query =$database->query("SELECT id FROM spouse WHERE LOWER(passport)='$passporttocheck' AND id !=$id AND status='1' LIMIT 2 ");
	$found_diplomat  = $database->num_rows($query);
	if ($given_name =='' || $family_name =='' || $gender =='' || $dob ==''   || $birth_nationality =='' || $passport =='' || $issued_date =='' || $issued_country =='' || $expiry_date =='') {
		$id=rawurlencode(encrypt_decrypt('encrypt', $id));
		$dpl = rawurlencode(encrypt_decrypt('encrypt', $diplomat));
		header("location:add-spouse?dpl=$dpl&sp=$id&messages=2");
	}else if ($found_diplomat >0) {
		$id=rawurlencode(encrypt_decrypt('encrypt', $id));
		$dpl = rawurlencode(encrypt_decrypt('encrypt', $diplomat));
		header("location:add-spouse?dpl=$dpl&sp=$id&messages=1");
	}else{
    $sql = "UPDATE spouse SET given_name='$given_name',family_name='$family_name',other_name='$other_name',gender='$gender',dob='$dob',birth_place='$birth_place',birth_nationality=$birth_nationality,other_nationality=$other_nationality,email='$email',phone='$phone',rwandan_id='$rwandan_id',rssb='$rssb',mmi='$mmi',passport='$passport',issued_country=$issued_country,issued_date='$issued_date',expiry_date='$expiry_date',company='$company',job_title='$job_title' WHERE id=$id";
    if ($database->query($sql)) {
        $dpl=rawurlencode(encrypt_decrypt('encrypt', $diplomat)).rawurlencode("%spouse");
        header("location:diplomats?dpl=$dpl");
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
  <!--   <style type="text/css">
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
    font-size: 13px;
}

label{
    /*font-weight: bold;*/
    /*font-family: serif;*/
    font-size: 12.5px !important;
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
	                    if (isset($diplomat) && is_numeric($diplomat)) {
	                    		$diplomat_data =$database->fetch_array($database->query("SELECT given_name,family_name,other_name FROM diplomats WHERE id = '$diplomat' LIMIT 1"));
					          
						}
						$text ="Adding";
						if (isset($spouse) && is_numeric($spouse)) {
	                    		$query =$database->query("SELECT * FROM spouse WHERE id = '$spouse' LIMIT 1");
					            $row  = $database->fetch_array($query);
								$text ="Editing";
						}
                     ?>
                          
                <div class="tab-content-body">
                   <h4 class="text-center fs-18 pt-20"><?=$text?> <span style="color:green;"><?php echo "{$diplomat_data['given_name']} {$diplomat_data['family_name']}"; ?></span>'s Partner <hr class="mt-20 mb-30"></h4>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" id="form">
                	<?php echo $messages;?>
                	<?php if (isset($spouse) && is_numeric($spouse)) {?>
                		<input type="hidden" name="id_to_edit" value="<?php echo $spouse; ?>" />
                	<?php }?>
                	
                	<?php if (isset($diplomat) && is_numeric($diplomat)) {?>
                		<input type="hidden" name="diplomat" value="<?php echo $diplomat; ?>" />
                	<?php }?>

                  <div class="row">
                    
                    <div class="col-lg-6">
                      
                      <div class="form-group">
                        <label for="name">Given Name<span class="required-mark">*</span></label>
                        <input type="text" class="form-control" maxlength="100" placeholder="Given names" name="given_name"  value="<?php if (isset($spouse) && is_numeric($spouse)){echo $row['given_name'];}elseif(isset($_POST['given_name'])){echo $_POST['given_name'];}?>">
                      </div>

                    </div>
                    <div class="col-lg-6">
                      
                      <div class="form-group">
                        <label for="name">Family Name<span class="required-mark">*</span></label>
                        <input type="text" class="form-control" maxlength="100" placeholder="Family names" name="family_name"  value="<?php if (isset($spouse) && is_numeric($spouse)){echo $row['family_name'];}elseif(isset($_POST['family_name'])){echo $_POST['family_name'];}?>">
                      </div>

                    </div>
                    <div class="col-lg-6">
                      
                      <div class="form-group">
                        <label for="name">Other Name</label>
                        <input type="text" class="form-control" maxlength="100" placeholder="Other names"  name="other_name"  value="<?php if (isset($spouse) && is_numeric($spouse)){echo $row['other_name'];}elseif(isset($_POST['other_name'])){echo $_POST['other_name'];}?>">
                      </div>

                    </div>
                    <div class="col-lg-6">
                      
                        <div class="form-group">
                            <label for="gender">Sex<span class="required-mark">*</span></label>
                            <select name="gender" class="form-control" id="gender">
                                <option value="">--SELECT--</option>
                                <option <?php if (isset($spouse) && is_numeric($spouse) && $row['gender']=='Male'){echo "selected";}elseif(isset($_POST['gender']) && $_POST['gender'] =='Male'){echo "Selected";}?> value="Male">Male</option>
                                <option <?php if (isset($spouse) && is_numeric($spouse) && $row['gender']=='Female'){echo "selected";}elseif(isset($_POST['gender']) && $_POST['gender'] =='Female'){echo "Selected";}?> value="Female">Female</option>
                            </select>
                        </div>

                    </div>

                    <div class="col-lg-6">
                      <div class="form-group date">
                        <label>Date of Birth<span class="required-mark">*</span></label>
                         <input type="text" maxlength="20" autocomplete="off" placeholder="Date of Birth" class="form-control datepicker"  name="dob" value="<?php if (isset($spouse) && is_numeric($spouse)){echo $row['dob'];}elseif(isset($_POST['dob'])){echo $_POST['dob'];}?>">
                       </div>
                    </div>

                    <div class="col-lg-6">
                      <div class="form-group">
                        <label for="contact_name">Place of Birth</label>
                        <input type="text" class="form-control" maxlength="255" placeholder="Enter Place Of Birth" name="birth_place" value="<?php if (isset($spouse) && is_numeric($spouse)){echo $row['birth_place'];}elseif(isset($_POST['birth_place'])){echo $_POST['birth_place'];}?>">
                      </div>
                    </div>

                    <div class="col-md-6">
                      
                       <div class="form-group">
                        <label for="country">Nationality Of Birth<span class="required-mark">*</span></label>
                            <select class="form-control"  name="birth_nationality">
                            <option value="">--Choose country--</option>
                             <?php $query1 = $database->query("SELECT id,name FROM countries ORDER BY id ASC");
                           $i=0;
                                 while($row1  = $database->fetch_array($query1)) {
                                  if ((isset($row['birth_nationality'])) && ($row1['id'] ==$row['birth_nationality'])) {
                          echo "<option selected value=\"{$row1['id']}\">{$row1['name']}</option>";
                          $i++;
                        }else if(isset($_POST['birth_nationality']) & ($row1['id'] ==$_POST['birth_nationality']) & $i==0){
                          echo "<option selected value=\"{$row1['id']}\">{$row1['name']}</option>";
                          $i++;
                        }else{
                          echo "<option value=\"{$row1['id']}\">{$row1['name']}</option>";
                        }
                                 }
                             ?>
                            </select>
                      </div>
                      

                    </div>
                    <div class="col-md-6 otherIfAny">
                          <div class="form-group">
                            <label for="country">Other (if any) nationality</label>
                                <select class="form-control"  name="other_nationality">
                                <option value="0">--Choose country--</option>
                                 <?php $query1 = $database->query("SELECT id,name FROM countries  ORDER BY id ASC");
                               $i=0;
                                while($row1  = $database->fetch_array($query1)) {
                                    if ((isset($row['other_nationality'])) && ($row1['id'] ==$row['other_nationality'])) {
                                    echo "<option selected value=\"{$row1['id']}\">{$row1['name']}</option>";
                                    $i++;
                                  }else if(isset($_POST['other_nationality']) & ($row1['id'] ==$_POST['other_nationality'])){
                                    echo "<option selected value=\"{$row1['id']}\">{$row1['name']}</option>";
                                    $i++;
                                  }else{
                                    echo "<option value=\"{$row1['id']}\">{$row1['name']}</option>";
                                  }
                                          
                                }
                                 ?>
                                </select>
                          </div>  
                      </div>
                      
                    <div class="col-lg-12">
                      
                         <fieldset class="fiedset-type pb-20">
                          <legend class="fieldset-legend">Passport<span class="required-mark">*</span></legend> 

                          <div class="col-lg-6">
                            
                            <div class="form-group">
                              <input type="text" class="form-control" name="passport" maxlength="30" placeholder="Passport number"  value="<?php if (isset($spouse) && is_numeric($spouse)){echo $row['passport'];}elseif(isset($_POST['passport'])){echo $_POST['passport'];}?>">
                            </div>

                          </div>
                          <div class="col-lg-6">
                            
                              <div class="form-group">
                                  <select class="form-control" name="issued_country">
                                      <option value="">--Issued Country--</option>
                                      <?php $query1 = $database->query("SELECT id,name FROM countries ORDER BY id ASC");
                                 $i=0;
                                       while($row1  = $database->fetch_array($query1)) {
                                        if ((isset($row['issued_country'])) && ($row1['id'] ==$row['issued_country'])) {
                                echo "<option selected value=\"{$row1['id']}\">{$row1['name']}</option>";
                                $i++;
                              }else if(isset($_POST['issued_country']) & ($row1['id'] ==$_POST['issued_country']) & $i==0){
                                echo "<option selected value=\"{$row1['id']}\">{$row1['name']}</option>";
                                $i++;
                              }else{
                                echo "<option value=\"{$row1['id']}\">{$row1['name']}</option>";
                              }
                                       }
                                   ?>
                                  </select>
                              </div>

                          </div>
                          <div class="col-lg-6">
                            <div class="form-group">
                              <input type="text" maxlength="20" autocomplete="off" placeholder="Issued date" class="form-control datepicker"  name="issued_date" value="<?php if (isset($spouse) && is_numeric($spouse)){echo $row['issued_date'];}elseif(isset($_POST['issued_date'])){echo $_POST['issued_date'];}?>">
                            </div>
                          </div>
                          <div class="col-lg-6">
                            <div class="form-group">
                              <input type="text" maxlength="20" autocomplete="off" placeholder="Expiry date" class="form-control datepicker1"  name="expiry_date" value="<?php if (isset($spouse) && is_numeric($spouse)){echo $row['expiry_date'];}elseif(isset($_POST['expiry_date'])){echo $_POST['expiry_date'];}?>">
                            </div>
                          </div>
      
                      </fieldset>


                    </div>


                    <div class="col-lg-6">
                        <div class="form-group">
                          <label for="location">Rwandan ID</label>
                          <input type="text" name="rwandan_id" class="form-control" placeholder="ID number" maxlength="30" value="<?php if (isset($spouse) && is_numeric($spouse)){echo $row['rwandan_id'];}elseif(isset($_POST['rwandan_id'])){echo $_POST['rwandan_id'];}?>">
                        </div>
                    </div>

                    <div class="col-lg-6">
                      
                      <div class="form-group">
                        <label for="location">RSSB</label>
                        <input type="text" name="rssb" class="form-control" placeholder="If any" maxlength="30" value="<?php if (isset($spouse) && is_numeric($spouse)){echo $row['rssb'];}elseif(isset($_POST['rssb'])){echo $_POST['rssb'];}?>">
                      </div>

                    </div>
                    <div class="col-lg-6">
                      
                      <div class="form-group">
                        <label for="location">MMI</label>
                        <input type="text" name="mmi" class="form-control" placeholder="If any" maxlength="30" value="<?php if (isset($spouse) && is_numeric($spouse)){echo $row['mmi'];}elseif(isset($_POST['mmi'])){echo $_POST['mmi'];}?>">
                      </div>


                    </div>
                    <div class="col-lg-6">
                      

                      <div class="form-group">
                        <label for="location">Email</label>
                        <input type="text" name="email" class="form-control" maxlength="50" placeholder="Email" value="<?php if (isset($spouse) && is_numeric($spouse)){echo $row['email'];}elseif(isset($_POST['email'])){echo $_POST['email'];}?>">
                      </div>


                    </div>

                    <div class="col-lg-12">
                      
                        <div class="form-group">
                          <label for="name">Telephone</label>
                          <input type="text" class="form-control" name="phone" maxlength="20" placeholder="Telephone"  value="<?php if (isset($spouse) && is_numeric($spouse)){echo $row['phone'];}elseif(isset($_POST['phone'])){echo $_POST['phone'];}?>">
                        </div>
                    </div>

                    <div class="col-lg-12">
                      
                        <fieldset class="fiedset-type pb-20">
                            <legend class="fieldset-legend">Job</legend> 

                              <div class="col-lg-6">
                                 <div class="form-group">
                                  <input type="text" class="form-control" maxlength="255" name="company" placeholder="Company/Organisation"  value="<?php if (isset($spouse) && is_numeric($spouse)){echo $row['company'];}elseif(isset($_POST['company'])){echo $_POST['company'];}?>">
                                </div>
                              </div>
                              <div class="col-lg-6">
                                <div class="form-group">
                                  <input type="text" class="form-control" maxlength="255" name="job_title" placeholder="Title"  value="<?php if (isset($spouse) && is_numeric($spouse)){echo $row['job_title'];}elseif(isset($_POST['job_title'])){echo $_POST['job_title'];}?>">
                                </div>
                              </div>
                         </fieldset>

                    </div>

                    <div class="col-lg-4 col-lg-offset-4">
                      <button  type="submit" name="save_spouse" class="btn w-100p mt-10 btn-primary fs-15 pt-10 pb-10 pull-right">Save</button>
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