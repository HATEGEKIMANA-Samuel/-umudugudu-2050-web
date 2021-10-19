<?php 
require_once("includes/validate_credentials.php");
$messages = '';
if (isset($_GET['messages']) && $_GET['messages'] ==1) {
	$messages = "<span style='color:red;'>Please fill out this page before continue.</span>";
}
if (isset($_GET['messages']) && $_GET['messages'] ==2) {
	$messages = "<span style='color:red;'>Please fill out all required fields</span>";
}
if (isset($_POST['save1'])) {
$country_loc = htmlentities($database->escape_value($_POST['country-loc']));
$phone = htmlentities($database->escape_value($_POST['phone']));
$contact_person = htmlentities($database->escape_value($_POST['contact_name']));
$contact_phone = htmlentities($database->escape_value($_POST['contact_phone']));
$location = htmlentities($database->escape_value($_POST['location']));
$email = htmlentities($database->escape_value($_POST['email']));
if (!isset($_POST['id_to_edit'])){
	$query =$database->query("SELECT id FROM institutions WHERE country_loc='$country_loc' AND category_id=3");
	$found_instutition  = $database->num_rows($query);
	if ($country_loc =='' || $email=='' || $location =='') {
		$messages = "<span style='color:red;'>Please fill out all required fields</span>";
	}else if ($found_instutition >0) {
		$messages = "<span style='color:red;'>Rwandan embassy in selected country is already registered</span>";
	} else {
	    $sql= "INSERT INTO institutions(country_loc,category_id,telephone,contact_person,location,country,contact_phone,email,time,user) VALUES('$country_loc',3,'$phone','$contact_person','$location','178','$contact_phone','$email',NOW(),{$_SESSION["id"]})";
	    if ($database->query($sql)) {
	        $id=encrypt_decrypt('encrypt', $database->inset_id());
	        header("location:display?inst=$id");
	    }
	}
}else{
	$id =$_POST['id_to_edit'];
	if ($email=='' || $location =='') {
		$messages = "<span style='color:red;'>Please fill out all required fields</span>";
		$id=encrypt_decrypt('encrypt', $id);
		header("location:register-rwandan-embassy?institution=$id&messages=2");
	}else{
    $sql = "UPDATE institutions SET telephone='$phone',contact_person='$contact_person',location='$location',contact_phone='$contact_phone',email='$email' WHERE id=$id";
    if ($database->query($sql)) {
        $id=encrypt_decrypt('encrypt', $id);
        header("location:display?inst=$id");
    }
   }
}	

}
if (isset($_GET['institution'])) {
	$institution = encrypt_decrypt('decrypt', $_GET['institution']);
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

        #nav-tab ul li { display: inline;float: left;padding: 8px 18%;background:#272c33;border-right: 1px solid grey }
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
                    		$query =$database->query("SELECT * FROM institutions WHERE id = '$institution' LIMIT 1");
				            $row  = $database->fetch_array($query);
							}
                    		?>
                         
                <div class="tab-content-body">
                   <div class="row">
                     <div class="col-lg-12 mt-10">
                        <h4 class="text-center fs-18 fw-500">Rwandan Embassy Registration Form <hr class="mt-20 mb-20"></h4>
                     </div>
                   </div>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" id="form">
                  <div class="row">
                     <div class="col-lg-12 fs-13"><?php echo $messages;?></div>
                   </div>
                	
                	<?php if (isset($institution) && is_numeric($institution)) {?>
                		<input type="hidden" name="id_to_edit" value="<?php echo $institution; ?>" />
                	<?php }?>
                  <div class="row">
                     <div class="col-lg-12 ">
                         <fieldset class="fiedset-type pb-20">
                           <legend class="fieldset-legend">Embassy location<span class="required-mark">*</span></legend>
                            
                            <div class="col-lg-6">
                              <div class="form-group">
                                  <select <?php if (isset($institution) && is_numeric($institution)){echo 'disabled';} ?> class="form-control" id="country-loc" name="country-loc">
                                       <option id="option" value="">--SELECT A LOCATION OF THE EMBASSY--</option>
                                    <?php $query1 = $database->query("SELECT id,name FROM countries ORDER BY id ASC");
                                   $i=0;
                                         while($row1  = $database->fetch_array($query1)) {
                                          if ((isset($row['country_loc'])) && ($row1['id'] ==$row['country_loc'])) {
                                  echo "<option selected value=\"{$row1['id']}\">{$row1['name']}</option>";
                                  $i++;
                                }else if(isset($_POST['country-loc']) & ($row1['id'] ==$_POST['country-loc']) & $i==0){
                                  echo "<option selected value=\"{$row1['id']}\">{$row1['name']}</option>";
                                  $i++;
                                }else if($row1['id'] ==178){
                                  continue;
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
                                <input type="text" name="location" class="form-control" id="location" placeholder="Full dddress" value="<?php if(isset($institution) && is_numeric($institution)){echo $row['location'];}elseif(isset($_POST['location'])){echo $_POST['location'];}?>" >
                              </div>                              
                            </div>

                       </fieldset>
                     </div>

                     <div class="col-lg-6">
                        <div class="form-group">
                        <label for="name">Email<span class="required-mark">*</span></label>
                        <input type="text" class="form-control" name="email" id="email" placeholder="Enter  Email" value="<?php if (isset($institution) && is_numeric($institution)){echo $row['email'];}elseif(isset($_POST['email'])){echo $_POST['email'];}?>">
                      </div>
                     </div>
                     <div class="col-lg-6">
                        <div class="form-group">
                        <label for="name">Phone</label>
                        <input type="text" class="form-control" name="phone" id="phone" placeholder="Enter  Phone" maxlength="13" value="<?php if (isset($institution) && is_numeric($institution)){echo $row['telephone'];}elseif(isset($_POST['phone'])){echo $_POST['phone'];}?>">
                      </div>
                     </div>
                     <div class="col-lg-12">

                        <fieldset class="fiedset-type pb-20">
                             <legend class="fieldset-legend">Contact person</legend>
                             <div class="col-lg-6">
                               <div class="form-group">
                                <input type="text"  class="form-control" id="contact_name" placeholder="Full names" name="contact_name" value="<?php if (isset($institution) && is_numeric($institution)){echo $row['contact_person'];}elseif(isset($_POST['contact_name'])){echo $_POST['contact_name'];}?>">
                              </div>
                             </div>
                             <div class="col-lg-6">
                                <div class="form-group">
                                  <input type="text" class="form-control" id="contact_phone" placeholder="Phone number" name="contact_phone" value="<?php if (isset($institution) && is_numeric($institution)){echo $row['contact_phone'];}elseif(isset($_POST['contact_phone'])){echo $_POST['contact_phone'];}?>">
                                </div>
                             </div>
                         </fieldset>
                        
                     </div>
                     <div class="col-lg-4 col-lg-offset-4">
                       <button  type="submit" name="save1" class="btn w-100p fs-15 mt-10 h-35">Save</button>
                     </div>
                   </div>	
                  
                    </form>
                </div>
            </div> 
        </div>
    </div>
 </div>  
<script src="assets/js/vendor/jquery-2.1.4.min.js"></script>
<script src="assets/js/vendor/jquery-validate.min.js"></script>
<script src="assets/js/sweetalert.min.js"></script>
<script src="assets/js/popper.min.js"></script>
<script src="assets/js/plugins.js"></script>
<script src="assets/js/main.js"></script>
<script src="assets/js/custom.js"></script>
<script src="assets/js/lib/vector-map/jquery.vmap.js"></script>
<script src="assets/js/lib/vector-map/jquery.vmap.min.js"></script>
<script src="assets/js/lib/vector-map/jquery.vmap.sampledata.js"></script>
<script src="assets/js/lib/vector-map/country/jquery.vmap.world.js"></script>