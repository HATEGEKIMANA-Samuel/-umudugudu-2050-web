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
if (isset($_GET['leave'])){
	$leaves = encrypt_decrypt('decrypt', $_GET['leave']);
}
if (isset($leaves) && !is_numeric($leaves)) {
    $dpl=rawurlencode(encrypt_decrypt('encrypt', $diplomat));
	header("location:add-courses?dpl=$dpl");
}

$messages = '';
if (isset($_GET['messages']) && $_GET['messages'] ==1){
	$messages = "<span style='color:red;'>Found another  course with same name</span>";
}
if (isset($_GET['messages']) && $_GET['messages'] ==2) {
	$messages = "<span style='color:red;'>Please fill out all required fields</span>";
}
if (isset($_POST['save_leave'])) {
	 
$leave_type = htmlentities($database->escape_value($_POST['leave_type']));
$from_date = htmlentities($database->escape_value($_POST['from_date']));
$to_date = htmlentities($database->escape_value($_POST['to_date']));
$description = htmlentities($database->escape_value($_POST['description']));

if (!isset($_POST['id_to_edit'])){
	if ($leave_type =='' || $from_date =='' || $to_date =='') {
		$messages = "<span style='color:red;'>Please fill out all required fields</span>";
	} else {
		
	    $sql= "INSERT INTO leaves(dpl,leave_type,from_date,to_date,description,time,user) 
	                       VALUES($diplomat,$leave_type,'$from_date','$to_date','$description',NOW(),{$_SESSION["id"]})";
	    if ($database->query($sql)) {
	        $id=rawurlencode(encrypt_decrypt('encrypt', $diplomat)).rawurlencode("%leave");
	        header("location:diplomats?dpl=$id");
	    }
	}
}else{
	$id =$_POST['id_to_edit'];
	if ($leave_type =='' || $from_date =='' || $to_date =='') {
		$id=rawurlencode(encrypt_decrypt('encrypt', $id));
		$dpl = rawurlencode(encrypt_decrypt('encrypt', $diplomat));
		header("location:add-courses?dpl=$dpl&cre=$id&messages=2");
	}else{
    $sql = "UPDATE leaves SET leave_type=$leave_type,from_date='$from_date',to_date='$from_date',description='$description' WHERE id=$id";
    if ($database->query($sql)) {
        $dpl=rawurlencode(encrypt_decrypt('encrypt', $diplomat)).rawurlencode("%leave");
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
       #decriptilon{
       	resize: none;
		min-width: 300px;
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
						$text = "Adding";
						if (isset($leaves) && is_numeric($leaves)) {
	                    		$query =$database->query("SELECT * FROM leaves WHERE id = '$leaves' AND status='1' LIMIT 1");
					            $row  = $database->fetch_array($query);
							$text ="Editing";	
						}
                     ?>
                          
                <div class="tab-content-body">
                   <h4 class="mt-20 fs-18" style="font-family: georgia;text-align: center;margin-bottom: 20px;"><?=$text?> <span style="color:green;"><?php echo "{$diplomat_data['given_name']} {$diplomat_data['family_name']}"; ?></span>'s Leave <hr class="mt-20 mb-10"></h4>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" id="form">
                	<?php echo $messages;?>
                	<?php if (isset($leaves) && is_numeric($leaves)) {?>
                		<input type="hidden" name="id_to_edit" value="<?php echo $leaves; ?>" />
                	<?php }?>
                	
                	<?php if (isset($diplomat) && is_numeric($diplomat)) {?>
                		<input type="hidden" name="diplomat" value="<?php echo $diplomat; ?>" />
                	<?php }?>

                    <div class="col-md-12 otherIfAny">
                          <div class="form-group">
                            <label for="country">Leave type</label>
                                <select class="form-control"  name="leave_type">
                                <option value="0">--Select--</option>
                                 <?php 
                                 $query1 = $database->query("SELECT id,Name FROM leave_types  ORDER BY id ASC");
                                 $i=0;
                                while($row1  = $database->fetch_array($query1)) {
                                    if ((isset($row['leave_type'])) && ($row1['id'] ==$row['leave_type'])) {
                                    echo "<option selected value=\"{$row1['id']}\">{$row1['Name']}</option>";
                                    $i++;
                                  }else if(isset($_POST['leave_type']) && ($row1['id'] ==$_POST['leave_type'])){
                                    echo "<option selected value=\"{$row1['id']}\">{$row1['Name']}</option>";
                                    $i++;
                                  }else{
                                    echo "<option value=\"{$row1['id']}\">{$row1['Name']}</option>";
                                  }
                                          
                                }
                                 ?>
                                </select>
                          </div>  
                    </div>
                  
                        <div class="col-md-6">
                          <div class="form-group">
                            <input type="text" maxlength="20" autocomplete="off" placeholder="From" class="form-control datepicker required"  name="from_date" value="<?php if (isset($leaves) && is_numeric($leaves)){echo $row['from_date'];}elseif(isset($_POST['from_date'])){echo $_POST['from_date'];}?>">
                          </div>
                        </div>

                        <div class="col-md-6">
                          <div class="form-group">
                            <input type="text" axlength="20" autocomplete="off" placeholder=" To" class="form-control datepicker required"  name="to_date" value="<?php if (isset($leaves) && is_numeric($leaves)){echo $row['to_date'];}elseif(isset($_POST['to_date'])){echo $_POST['to_date'];}?>">
                          </div>
                        </div>
                 
                  <div class="form-group">
                    <label for="name">More info.</label>
                    <textarea class="form-control" rows="5" maxlength="250" id="decriptilon" placeholder="Additional info(If any)"  name="description"><?php if (isset($leaves) && is_numeric($leaves)){echo $row['description'];}elseif(isset($_POST['description'])){echo $_POST['description'];}?></textarea>
                  </div>

                  <div class="text-center">
                    <button  type="submit" name="save_leave" class="btn mt-10 w-50p p-10 btn-primary fs-15 text-center  pull-center">Save</button>
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
       format: 'yyyy-mm-dd'
     });
     
     $('.datepicker1').datepicker({
       format: 'yyyy-mm-dd'
     });
  
</script>

</body>
</html>