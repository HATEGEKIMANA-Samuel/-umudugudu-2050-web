<?php 
require_once("includes/validate_credentials.php");
$messages = '';
if (isset($_GET['messages']) && $_GET['messages'] ==1) {
	$messages = "<span style='color:red;'>Please fill out this page before continue.</span>";
}
if (isset($_GET['messages']) && $_GET['messages'] ==2) {
	$messages = "<span style='color:red;'>Please fill out all required fields</span>";
}
if (isset($_GET['jpc'])) {
	$jpc = encrypt_decrypt('decrypt', $_GET['jpc']);
	$data = $database->fetch_array($database->query("SELECT id,name,country,area,time,t_next_meet,place,lineministry,timeframe,mou_files,cont_name,cont_phone,cont_email,user FROM jpc WHERE id = '{$jpc}' LIMIT 1"));
}
if (isset($_POST['save1'])) {
	$name = htmlentities($database->escape_value($_POST['name']));
	$area = htmlentities($database->escape_value($_POST['area']));
	$contact_person = htmlentities($database->escape_value($_POST['contact_name']));
	$contact_phone = htmlentities($database->escape_value($_POST['contact_phone']));
	$location = htmlentities($database->escape_value($_POST['location']));
	$country = htmlentities($database->escape_value($_POST['country']));
	$email = htmlentities($database->escape_value($_POST['email']));
  $time = htmlentities($database->escape_value($_POST['time']));
  $t_next_meet = htmlentities($database->escape_value($_POST['t_next_meet']));
  $linemin = htmlentities($database->escape_value($_POST['linemin']));
  $timeframe = htmlentities($database->escape_value($_POST['timeframe']));

	$file = $_FILES['attmou'];

	if (isset($_POST['id_to_edit']) && is_numeric($_POST['id_to_edit']) && !empty($_POST['id_to_edit'])) {
      if ($file['size'] > 0) {
        $getfile = upload($file,$_POST['id_to_edit'],'update');
        $database->query("UPDATE jpc SET mou_files='{$getfile}' WHERE id='{$_POST['id_to_edit']}' ");
      }
      $qr = "UPDATE jpc SET name='{$name}',country='{$country}',area='{$area}',time='{$time}',t_next_meet='{$t_next_meet}',place='{$location}',lineministry='{$linemin}',timeframe='{$timeframe}',cont_name='{$contact_person}',cont_phone='{$contact_phone}',cont_email='{$email}',user='{$_SESSION['id']}' WHERE id='{$_POST['id_to_edit']}'";
		$res = $database->query($qr);
    $database->LogMe($qr,'update jpc','success');
		if ($database->affected_rows()>0) {
      $getfile = upload($file,$_POST['id_to_edit'],'update');
      $database->query("UPDATE jpc SET mou_files='{$getfile}' WHERE id='{$owner}' ");
			$messages = "<span style='color:green;'>JPC Updated Successful</span>";
			header("location:vjpc");
		}else{
			$messages = "<span style='color:red;'>JPC Informations are the same</span>";
      header("location:vjpc");
		}
	}else{
    $qr = "INSERT INTO jpc(name,country,area,time,t_next_meet,place,lineministry,timeframe,cont_name,cont_phone,cont_email,user) VALUES ('{$name}','{$country}','{$area}','{$time}','{$t_next_meet}','{$location}','{$linemin}','{$timeframe}','{$contact_person}','{$contact_phone}','{$email}','{$_SESSION['id']}')";
		$res = $database->query($qr);
      $owner = $database->inset_id();
		if ($owner>0) {
      $getfile = upload($file,$owner,'insert');
      $database->query("UPDATE jpc SET mou_files='{$getfile}' WHERE id='{$owner}' ");
      // $database->query("INSERT INTO notification (action_id,action_type,action_name,deadline,user) VALUES('{$owner}','jpc','{$name}','{$t_next_meet}','{$_SESSION['id']}')");
      $database->LogMe($qr,'new jpc','success');
			$messages = "<span style='color:green;'>JPC Inserted Successful</span>";
			header("location:vjpc");
		}
	}
}
function upload($file='',$owner='',$action='')
{
  global $database;
  $db_file_name = basename($file['tmp_name']);
  $ext = explode(".", $db_file_name);
  $fileExt = end($ext);
  $upload_errors = array(
      // http://www.php.net/manual/en/features.file-upload.errors.php
        UPLOAD_ERR_OK         => "No errors.",
        UPLOAD_ERR_INI_SIZE   => "Larger than upload_max_filesize.",
        UPLOAD_ERR_FORM_SIZE  => "Larger than form MAX_FILE_SIZE.",
        UPLOAD_ERR_PARTIAL    => "Partial upload.",
        UPLOAD_ERR_NO_FILE    => "No file.",
        UPLOAD_ERR_NO_TMP_DIR => "No temporary directory.",
        UPLOAD_ERR_CANT_WRITE => "Can't write to disk.",
        UPLOAD_ERR_EXTENSION  => "File upload stopped by extension."    
      );
  if (!$file || empty($file) || !is_array($file)){
      $messages = "No file was attached";
      } else if($file["error"] != 0){
      $messages = $upload_errors[$file["error"]];
      }
      else if($file["error"] == 0){
      $size = $file['size'];
      $type = $file['type'];
      $temp_name = $file['tmp_name'];
      $db_file_name = basename($file['name']);
      $ext = explode(".", $db_file_name);
      $fileExt =end($ext);
      $taget_file = $owner.'-'.rand_string(20).".".$fileExt;
      $finame = htmlentities($database->escape_value($db_file_name));
      $sql = "INSERT INTO attachments(attachments,name,owner,owner_type,user,time) VALUES('{$taget_file}','{$finame}','$owner','jpc',{$_SESSION["id"]},NOW()) "; 
      $query = $database->query($sql);
      $afected = $database->inset_id();
      $file1 = $afected;
      $path  = "attachments/".$taget_file;
      if(move_uploaded_file($temp_name,$path) && ($afected >=1)){     
        $messages = "<span style='color:green;'>Done</span>";
      }else
        $messages = "<span style='color:red;'>Erros occur!</span>";
      }
      return $file1;
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

        #nav-tab ul li { display: inline;float: left;padding: 7px 18%;background:#272c33;border-right: 1px solid grey }
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
                <div id="nav-tab">
                    <ul>
                    	<li id="active" style="float: none;display: block;"><a href="<?php echo $_SERVER['REQUEST_URI']; ?>">Registration Form For New JPC & JPCC</a></li>
                    </ul>
                </div>
                <div class="tab-content-body mt-20">
                    <!-- <legend>Registration Form  For New JPC & JPCC</legend> -->
                    <!-- <?php echo($messages); ?> -->
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" id="form"  enctype="multipart/form-data">
                	<?php echo $messages;?>
                	<?php if (isset($data) && !empty($data['id'])) {?>
                		<input type="hidden" name="id_to_edit" value="<?php echo $data['id']; ?>" />
                	<?php }?>	

                  <div class="row">
                     <div class="col-lg-4">
                    <div class="form-group">
                      <label for="name">Name<span class="required-mark">*</span></label>
                      <input type="text" class="form-control" id="name" name="name" placeholder="Enter Name"  value="<?php if (isset($data) && !empty($data['name'])){echo $data['name'];}elseif(isset($_POST['name'])){echo $_POST['name'];}?>">
                    </div>
                  </div>

                  <div class="col-lg-4">
                    <div class="form-group">
                      <label>Country</label>
                        <select required class="form-control" id="country" name="country">
                           <option id="option" value="">--SELECT A COUNTRY--</option>
                        <?php $query1 = $database->query("SELECT id,name FROM countries ORDER BY id ASC");
                       $i=0;
                             while($row1  = $database->fetch_array($query1)) {
                              if ((isset($data['country'])) && ($row1['id'] ==$data['country_loc'])) {
                      echo "<option selected value=\"{$row1['id']}\">{$row1['name']}</option>";
                      $i++;
                    }else if(isset($_POST['country']) & ($row1['id'] ==$_POST['country']) & $i==0){
                      echo "<option selected value=\"{$row1['id']}\">{$row1['name']}</option>";
                      $i++;
                    }else if($i==0 && $row1['id'] ==178){
                      echo "<option selected value=\"{$row1['id']}\">{$row1['name']}</option>";
                    }else{
                      echo "<option value=\"{$row1['id']}\">{$row1['name']}</option>";
                    }
                             }
                         ?>
                      </select>
                  </div>
                  </div>

                  <div class="col-lg-4">
                    <div class="form-group">
                      <label for="name">Area Of Cooporation</label>
                      <input class="form-control" type="text" name="area" placeholder="Enter Area" value="<?php if (isset($data) && !empty($data['area'])){echo $data['area'];}elseif(isset($_POST['area'])){echo $_POST['area'];}?>">
                    </div>
                  </div>

                  <div class="col-lg-4">
                    <div class="form-group">
                      <label for="name">Time</label>

                      <input class="form-control" type="date" name="time" placeholder="Enter Time Of Meeting" value="<?php if (isset($data) && !empty($data['time'])){echo $data['time'];}elseif(isset($_POST['time'])){echo $_POST['time'];}?>">
                    </div>
                  </div>

                  <div class="col-lg-4">
                    <div class="form-group">
                      <label for="name">Next meeting schedule (Date)</label>
                      <input class="form-control" type="date" name="t_next_meet" placeholder="Enter Time Of Next Meeting" value="<?php if (isset($data) && !empty($data['t_next_meet'])){echo $data['t_next_meet'];}elseif(isset($_POST['t_next_meet'])){echo $_POST['t_next_meet'];}?>">
                    </div>
                  </div>

                  <div class="col-lg-4">
                    <div class="form-group">
                      <label for="name">Place</label>
                      <input class="form-control" type="text" name="location" placeholder="Enter Place Of Meeting"  value="<?php if (isset($data) && !empty($data['place'])){echo $data['place'];}elseif(isset($_POST['location'])){echo $_POST['location'];}?>">
                    </div>
                  </div>

                  <div class="col-lg-4">
                    <div class="form-group">
                      <label for="name">Line Ministry</label>
                      <input class="form-control" type="text" name="linemin" placeholder="Enter Line Ministry" value="<?php if (isset($data) && !empty($data['lineministry'])){echo $data['lineministry'];}elseif(isset($_POST['linemin'])){echo $_POST['linemin'];}?>">
                    </div>
                  </div>

                  <div class="col-lg-4">
                    <div class="form-group">
                      <label for="name">Time Frame</label>
                      <input class="form-control" type="date" name="timeframe" placeholder="Enter Time Frame" value="<?php if (isset($data) && !empty($data['timeframe'])){echo $data['timeframe'];}elseif(isset($_POST['timeframe'])){echo $_POST['timeframe'];}?>">
                    </div>
                  </div>

                  <div class="col-lg-4">
                    <div class="form-group">
                      <div class="">
                        <label><i class="fa fa-paperclip"></i> Attach MOU</label>
                        <input type="file" name="attmou" id="attmou" >
                      </div>
                      <!-- <input class="form-control" type="file" name="attmou" placeholder="Enter Area"> -->
                    </div>
                  </div>

                  <div class="col-lg-12">
                    <fieldset class="fiedset-type pb-20">
                     <legend class="fieldset-legend">Contact person</legend>

                     <div class="col-lg-4">
                        <div class="form-group">
                          <label>Names</label>
                          <input type="text"  class="form-control" id="contact_name" placeholder="Full names" name="contact_name" value="<?php if (isset($data) && !empty($data['cont_name'])){echo $data['cont_name'];}elseif(isset($_POST['contact_name'])){echo $_POST['contact_name'];}?>">
                        </div>
                     </div>

                     <div class="col-lg-4">
                        <div class="form-group">
                          <label>Phone Number</label>
                          <input type="text" class="form-control" id="contact_phone" placeholder="Phone number" name="contact_phone" value="<?php if (isset($data) && !empty($data['cont_phone'])){echo $data['cont_phone'];}elseif(isset($_POST['contact_phone'])){echo $_POST['contact_phone'];}?>">
                        </div>
                     </div>

                    <div class="col-lg-4">
                      <div class="form-group">
                        <label for="name">Email<span class="required-mark">*</span></label>
                        <input type="text" class="form-control" name="email" id="email" placeholder="Enter  Email" value="<?php if (isset($data) && !empty($data['cont_email'])){echo $data['cont_email'];}elseif(isset($_POST['email'])){echo $_POST['email'];}?>">
                      </div>
                    </div>

                 </fieldset>

                    
                  </div>

                  <div class="col-lg-offset-4 col-lg-4">
                    <button  type="submit" name="save1" class="btn w-100p p-10 btn-lg"><span class="glyphicon glyphicon-ok"></span>  Save </button>
                  </div>

                  </div>
                 
                    </form>
                </div>
            </div> 
        </div>
    </div>

 </div>  
<script src="assets/js/vendor/jquery-1.9.1.js"></script>
<script src="assets/js/vendor/jquery-validate.min.js"></script>
<script src="assets/js/vendor/jquery-2.1.4.min.js"></script>
<script src="assets/js/sweetalert.min.js"></script>
<script src="assets/js/popper.min.js"></script>
<script src="assets/js/plugins.js"></script>
<script src="assets/js/main.js"></script>
<script src="assets/js/custom.js"></script>
<script src="assets/js/lib/vector-map/jquery.vmap.js"></script>
<script src="assets/js/lib/vector-map/jquery.vmap.min.js"></script>
<script src="assets/js/lib/vector-map/jquery.vmap.sampledata.js"></script>
<script src="assets/js/lib/vector-map/country/jquery.vmap.world.js"></script>
