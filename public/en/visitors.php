<?php 
require_once("includes/validate_credentials.php");
$messages = '';
if (isset($_GET['messages']) && $_GET['messages'] ==1) {
	$messages = "<span style='color:red;'>Found another  diplomat with same passport number</span>";
}
if (isset($_GET['messages']) && $_GET['messages'] ==2) {
	$messages = "<span class='text-danger pb-10 fs-14 text-center' >Please fill out all required fields</span>";
}
if (isset($_POST['save_diplomat'])){
	 
  $given_name = htmlentities($database->escape_value($_POST['given_name']));
  $family_name = htmlentities($database->escape_value($_POST['family_name']));
  $other_name = htmlentities($database->escape_value($_POST['other_name']));
  $gender = htmlentities($database->escape_value($_POST['gender']));
  $dob = htmlentities($database->escape_value($_POST['dob']));
  $marital_status = htmlentities($database->escape_value($_POST['marital_status']));
  $birth_place = htmlentities($database->escape_value($_POST['birth_place']));
  $birth_nationality = htmlentities($database->escape_value($_POST['birth_nationality']));
  $passport = htmlentities($database->escape_value($_POST['passport']));
  $issued_country = htmlentities($database->escape_value($_POST['issued_country']));
  $issued_date = htmlentities($database->escape_value($_POST['issued_date']));
  $expiry_date = htmlentities($database->escape_value($_POST['expiry_date']));
  $email = htmlentities($database->escape_value($_POST['email']));
  $phone = htmlentities($database->escape_value($_POST['phone']));
  $father_name = htmlentities($database->escape_value($_POST['father_name']));
  $father_country = htmlentities($database->escape_value($_POST['father_country']));
  $mother_name = htmlentities($database->escape_value($_POST['mother_name']));
  $mother_country = htmlentities($database->escape_value($_POST['mother_country']));
  $institution = htmlentities($database->escape_value($_POST['institution']));


  if (!isset($_POST['id_to_edit'])){
  	$passporttocheck = strtolower($passport);
  	$query =$database->query("SELECT id FROM diplomats WHERE LOWER(passport)='$passporttocheck' AND status='1' LIMIT 2 ");
  	$found_diplomat  = $database->num_rows($query);
  	if ($given_name =='' || $family_name =='' || $gender =='' || $dob =='' || $marital_status =='' || $birth_place =='' || $birth_nationality =='' || $passport =='' || $issued_date =='' || $issued_country =='' || $expiry_date =='' || $email =='' || $father_name =='' || $father_country =='' || $mother_name =='' || $mother_country =='') {
  		$messages = "<span class='text-danger pb-10 fs-14 text-center'>Please fill out all required fields</span>";
  	}else if ($found_diplomat >0) {
  		$messages = "<span style='color:red;'>Diplomat with $passport as passport is already registered</span>";
  	} else {
  		  $v = v_institution($institution);
  	    $sql= "INSERT INTO diplomats(title,given_name,family_name,other_name,gender,dob,marital_status,birth_place,birth_nationality,email,phone,passport,issued_country,issued_date,expiry_date,father_name,father_country,mother_name,mother_country,time,user,institution) 
  	                       VALUES('Visitor','$given_name','$family_name','$other_name','$gender','$dob','$marital_status','$birth_place',$birth_nationality,'$email','$phone','$passport',$issued_country,'$issued_date','$expiry_date','$father_name',$father_country,'$mother_name',$mother_country,NOW(),{$_SESSION["id"]},'{$v}')";
  	    if ($database->query($sql)) {
  	        $id=encrypt_decrypt('encrypt', $database->inset_id());
  	        header("location:diplomats?dpl=$id");
  	    }
  	}
  }else{
  	$id =$_POST['id_to_edit'];
  	$passporttocheck = strtolower($passport);
  	$query =$database->query("SELECT id FROM diplomats WHERE LOWER(passport)='$passporttocheck' AND id !=$id AND status='1' LIMIT 2 ");
  	$found_diplomat  = $database->num_rows($query);
  	if ($given_name =='' || $family_name =='' || $gender =='' || $dob =='' || $marital_status =='' || $birth_place =='' || $birth_nationality =='' || $passport =='' || $issued_date =='' || $issued_country =='' || $expiry_date =='' || $email =='' || $father_name =='' || $father_country =='' || $mother_name =='' || $mother_country =='') {
  		$id=encrypt_decrypt('encrypt', $id);
  		header("location:add-diplomat?dpl=$id&messages=2");
  	}else if ($found_diplomat >0) {
  		$id=encrypt_decrypt('encrypt', $id);
  		header("location:add-diplomat?dpl=$id&messages=1");
  	}else{
      $v = v_institution($institution);
      $sql = "UPDATE diplomats SET given_name='$given_name',family_name='$family_name',other_name='$other_name',gender='$gender',dob='$dob',marital_status='$marital_status',birth_place='$birth_place',birth_nationality=$birth_nationality,email='$email',phone='$phone',passport='$passport',issued_country=$issued_country,issued_date='$issued_date',expiry_date='$expiry_date',father_name='$father_name',father_country=$father_country,mother_name='$mother_name',mother_country=$mother_country,institution='{$v}' WHERE id=$id";
      if ($database->query($sql)) {
          $id=encrypt_decrypt('encrypt', $id);
          header("location:diplomats?dpl=$id");
      }
     }
  }
}
if (isset($_GET['dpl'])) {
	$diplomat = encrypt_decrypt('decrypt', $_GET['dpl']);
}
function v_institution($value='')
{
  global $database;
  $value = trim($value);
  $inst = strtolower($value);
  $qr = $database->fetch_array($database->query("SELECT id FROM v_institutions WHERE LOWER(institution)='{$inst}'"));
  if (isset($qr['id']) && !empty($qr['id'])) {
    return $qr['id'];
  }else{
    $database->query("INSERT INTO v_institutions(institution,date) VALUES('{$value}',NOW())");
    return $database->inset_id();
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
<!-- <style type="text/css">
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
                
                    <?php if (isset($diplomat) && is_numeric($diplomat)) {?>
                    		<?php   
                    		$query =$database->query("SELECT d.*,v.institution AS ins_name FROM diplomats AS d, v_institutions AS v WHERE v.id=d.institution AND d.id = '$diplomat' LIMIT 1");
				            $row  = $database->fetch_array($query);
						}
                     ?>
                          
                <div class="tab-content-body">
                   <h4 class="text-center fw-500 fs-18 pt-10">Visitors Registration Form <hr class="mb-20 mt-20"> </h4>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" id="form">
                	<div class="row"><div class="col-md-12"> <?php echo $messages;?></div></div>
                	<?php if (isset($diplomat) && is_numeric($diplomat)) {?>
                		<input type="hidden" name="id_to_edit" value="<?php echo $diplomat; ?>" />
                	<?php }?>

                  <div class="row"> 


                      <div class="col-md-4">
                      <div class="form-group">
                      <label for="name">Given Name<span class="required-mark">*</span></label>
                      <input type="text" class="form-control" maxlength="100" placeholder="Given names" name="given_name"  value="<?php if (isset($diplomat) && is_numeric($diplomat)){echo $row['given_name'];}elseif(isset($_POST['given_name'])){echo $_POST['given_name'];}?>">
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="name">Family Name<span class="required-mark">*</span></label>
                      <input type="text" class="form-control" maxlength="100" placeholder="Family names" name="family_name"  value="<?php if (isset($diplomat) && is_numeric($diplomat)){echo $row['family_name'];}elseif(isset($_POST['family_name'])){echo $_POST['family_name'];}?>">
                    </div>
                  </div>

                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="name">Other Name</label>
                      <input type="text" class="form-control" maxlength="100" placeholder="Other names"  name="other_name"  value="<?php if (isset($diplomat) && is_numeric($diplomat)){echo $row['other_name'];}elseif(isset($_POST['other_name'])){echo $_POST['other_name'];}?>">
                    </div>
                  </div>

                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="gender">Gender<span class="required-mark">*</span></label>
                      <select name="gender" class="form-control" id="gender">
                          <option value=""> -- Select --</option>
                          <option <?php if (isset($diplomat) && is_numeric($diplomat) && $row['gender']=='Male'){echo "selected";}elseif(isset($_POST['gender']) && $_POST['gender'] =='Male'){echo "Selected";}?> value="Male">Male</option>
                          <option <?php if (isset($diplomat) && is_numeric($diplomat) && $row['gender']=='Female'){echo "selected";}elseif(isset($_POST['gender']) && $_POST['gender'] =='Female'){echo "Selected";}?> value="Female">Female</option>
                      </select>
                    </div>
                  </div>

                  <div class="col-md-4">
                    <div class="form-group date">
                      <label>Date of Birth<span class="required-mark">*</span></label>
                       <input type="text"  maxlength="20" autocomplete="off" placeholder="Date of Birth" class="form-control datepicker"  name="dob" value="<?php if (isset($diplomat) && is_numeric($diplomat)){echo $row['dob'];}elseif(isset($_POST['dob'])){echo $_POST['dob'];}?>">
                     </div>
                  </div>

                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="name">Malital Status<span class="required-mark">*</span></label>
                      <select class="form-control" name="marital_status">
                          <option value=""> -- Select --</option>
                          <option <?php if (isset($diplomat) && is_numeric($diplomat) && $row['marital_status']=='Single'){echo "selected";}elseif(isset($_POST['marital_status']) && $_POST['marital_status'] =='Single'){echo "Selected";}?> value="Single">Single</option>
                          <option <?php if (isset($diplomat) && is_numeric($diplomat) && $row['marital_status']=='Married'){echo "selected";}elseif(isset($_POST['marital_status']) && $_POST['marital_status'] =='Married'){echo "Selected";}?> value="Married">Married</option>
                          <option <?php if (isset($diplomat) && is_numeric($diplomat) && $row['marital_status']=='Divorced'){echo "selected";}elseif(isset($_POST['marital_status']) && $_POST['marital_status'] =='Divorced'){echo "Selected";}?> value="Divorced">Divorced</option>
                      </select>
                    </div>
                  </div>


                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="contact_name">Place of Birth<span class="required-mark">*</span></label>
                      <input type="text" class="form-control" maxlength="255" placeholder="Enter Place Of Birth" name="birth_place" value="<?php if (isset($diplomat) && is_numeric($diplomat)){echo $row['birth_place'];}elseif(isset($_POST['birth_place'])){echo $_POST['birth_place'];}?>">
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

                  <div class="col-md-12"> 

                    <fieldset class="fiedset-type pb-20">
                      <legend class="fieldset-legend">Passport<span class="required-mark">*</span></legend> 
                      
                      <div class="col-md-6">
                          <div class="form-group">
                            <input type="text" class="form-control" name="passport" maxlength="30" placeholder="Passport number"  value="<?php if (isset($diplomat) && is_numeric($diplomat)){echo $row['passport'];}elseif(isset($_POST['passport'])){echo $_POST['passport'];}?>">
                          </div>
                      </div>

                      <div class="col-md-6">
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
                      <div class="col-md-6">
                          <div class="form-group">
                            <input type="text" maxlength="20" autocomplete="off" placeholder="Issued date" class="form-control datepicker"  name="issued_date" value="<?php if (isset($diplomat) && is_numeric($diplomat)){echo $row['issued_date'];}elseif(isset($_POST['issued_date'])){echo $_POST['issued_date'];}?>">
                          </div>
                      </div>
                      <div class="col-md-6">
                          <div class="form-group">
                          <input type="text" maxlength="20" autocomplete="off" placeholder="Expiry date" class="form-control datepicker1"  name="expiry_date" value="<?php if (isset($diplomat) && is_numeric($diplomat)){echo $row['expiry_date'];}elseif(isset($_POST['expiry_date'])){echo $_POST['expiry_date'];}?>">
                        </div>
                      </div>
                      
                    </fieldset>

                  </div>

                  <div class="col-md-12">

                      <fieldset class="fiedset-type pb-20">
                        <legend class="fieldset-legend">Institution</legend>
                        <div class="col-md-12"> 

                            <div class="form-group">
                              <input type="text" name="institution" class="form-control" maxlength="20" placeholder="Enter Institution" value="<?php if (isset($diplomat) && is_numeric($diplomat)){echo $row['ins_name'];}elseif(isset($_POST['institution'])){echo $_POST['institution'];}?>">
                            </div>

                        </div>  
                      </fieldset>

                  </div>

                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="location">Email<span class="required-mark">*</span></label>
                      <input type="text" name="email" class="form-control" maxlength="50" placeholder="Email" value="<?php if (isset($diplomat) && is_numeric($diplomat)){echo $row['email'];}elseif(isset($_POST['email'])){echo $_POST['email'];}?>">
                    </div>
                  </div>


                  <div class="col-md-6"> 

                      <div class="form-group">
                        <label for="name">Telephone</label>
                        <input type="text" class="form-control" name="phone" maxlength="20" placeholder="Telephone"  value="<?php if (isset($diplomat) && is_numeric($diplomat)){echo $row['phone'];}elseif(isset($_POST['phone'])){echo $_POST['phone'];}?>">
                      </div>

                  </div>

                  <div class="col-md-12"> 

                      <fieldset class="fiedset-type pb-20">
                        <legend class="fieldset-legend">Father<span class="required-mark">*</span></legend> 
                      
                          <div class="col-md-6"> 

                              <div class="form-group">
                                <input type="text" class="form-control" maxlength="255" name="father_name" placeholder="Name"  value="<?php if (isset($diplomat) && is_numeric($diplomat)){echo $row['father_name'];}elseif(isset($_POST['father_name'])){echo $_POST['father_name'];}?>">
                              </div>

                          </div>

                          <div class="col-md-6"> 

                              <div class="form-group">
                                <select class="form-control" name="father_country">
                                    <option value="">--Country of orgin--</option>
                                    <?php $query1 = $database->query("SELECT id,name FROM countries ORDER BY id ASC");
                                 $i=0;
                                       while($row1  = $database->fetch_array($query1)) {
                                        if ((isset($row['father_country'])) && ($row1['id'] ==$row['father_country'])) {
                                echo "<option selected value=\"{$row1['id']}\">{$row1['name']}</option>";
                                $i++;
                              }else if(isset($_POST['father_country']) & ($row1['id'] ==$_POST['father_country']) & $i==0){
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
                      
                      
                      
                    </fieldset>

                  </div>


                  <div class="col-md-12"> 

                      <fieldset class="fiedset-type pb-20">
                    <legend class="fieldset-legend">Mother<span class="required-mark">*</span></legend> 

                    <div class="col-md-6"> 
                        <div class="form-group">
                        <input type="text" class="form-control" name="mother_name" maxlength="255"  placeholder="Name"  value="<?php if (isset($diplomat) && is_numeric($diplomat)){echo $row['mother_name'];}elseif(isset($_POST['mother_name'])){echo $_POST['mother_name'];}?>">
                      </div>
                    </div>

                    <div class="col-md-6"> 

                        <div class="form-group">
                          <select class="form-control" name="mother_country">
                              <option value="">--Country of orgin--</option>
                              <?php $query1 = $database->query("SELECT id,name FROM countries ORDER BY id ASC");
                                 $i=0;
                                       while($row1  = $database->fetch_array($query1)) {
                                        if ((isset($row['mother_country'])) && ($row1['id'] ==$row['mother_country'])) {
                                echo "<option selected value=\"{$row1['id']}\">{$row1['name']}</option>";
                                $i++;
                              }else if(isset($_POST['mother_country']) & ($row1['id'] ==$_POST['mother_country']) & $i==0){
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
                  
                 </fieldset>


                  </div>
               
                 
                  <div class="col-md-offset-4 col-md-4 mb-20">
                    <button  type="submit" name="save_diplomat" class="btn fs-15 w-100p">Save</button>
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