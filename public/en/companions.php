<?php 
require_once("includes/validate_credentials.php");
if (isset($_POST['comment_field']) && !empty($_POST['comment_field'])) {
      $comment = htmlentities($database->escape_value($_POST['comment_field']));
	  $sql = "INSERT INTO comments(owner,comment,owner_type,time,user) VALUES({$_POST['companions']},'$comment','cpn',NOW(),{$_SESSION["id"]})";
	  $database->query($sql);
	  $sp= rawurlencode(encrypt_decrypt('encrypt', $_POST['companions']));
	  header("location:companions?cpn=$sp");
	  exit();
 }
if (isset($_GET['cpn'])) {
	$kd_data = explode('%', $_GET['cpn']);
	$found = count($kd_data);
	if ($found> 1) {
		$sp = encrypt_decrypt('decrypt', $kd_data[0]);
	} else {
		$sp = encrypt_decrypt('decrypt', $_GET['cpn']);
	}
	
}else{
	header("location:404");
}
if (!isset($sp) || !is_numeric($sp)) {
	header("location:404");
}else{
	$query =$database->query("SELECT * FROM companions WHERE id ='$sp' AND status='1' LIMIT 1 ");
	$found_spouse  = $database->num_rows($query);
	if ($found_spouse ==0) {
		header("location:404");
	}else{
		$spouse  = $database->fetch_array($query);
	}
}

?>
<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang=""> <!--<![endif]-->
<head>
    <?php require_once("includes/head.php"); ?>
	<style>
	#nav-tab{
		border-bottom: 1px solid green;
	}
	 .nav-item.nav-link.active{
	   	border-color: green;
	   	color: green;
	   	border-bottom: none;
	   }
	    .info-list{
	    	border-left: none;
	    	border-right: none;
	    	width: 95%;
	    	margin-left: 2%;
	    	font-size: 10pt;
	    	padding: 5px;
	    }
	    .first-child{
	    	border-top: none;
	    }
	    .last-child{
	    		border-bottom: none;
	    }
		.labels{
			display: inline-block ;
			width: 25%;
		    font-family:inherit;
		    color: #848482;
			
		}
		.datas{
			display: inline-block;
			width:75%;
			font-family: inherit;
			color: black;
			font-weight: 400;
		}
	.edit-link{
		margin-bottom: 30px;
		
	}
	.edit_btn {
            font-size: 12px;
            font-weight: bold;
            background: #435d7d;
            border: none;
            margin-top: 20px;
            margin-left: 2%;
            min-width: 50px;
        }
     .edit_btn:hover, .edit_btn:focus {
            background: #41cba9;
            outline: none !important;
      }	
      
      .back_btn {
            font-size: 12px;
            font-weight: bold;
            background: #ADD8E6;
            border: none;
            margin-top: 20px;
            margin-left: 2%;
            min-width: 50px;
        }
     .back_btn:hover, .back_btn:focus {
            background: #41cba9;
            outline: none !important;
      }	
	#comment_button{
		 /*max-width: 50px;*/
		 max-height: 38px;
		 margin-top: 20px;
		 padding-top:2px;
		 margin-left: 30px;
		 
	}
	#comments{
		 margin-left: 2%;
	}
	#comment_field{
		resize: none;
		min-width: 300px;
	}
	#submit_comment{
		display: block;
		font-size: 12px;
	}
	.comment{
		margin-left:2%;
		margin-right: 2%; 
		margin-bottom: 10px;
	}
   .comment	.comment_data{
   	font-size: 11pt;
   	font-family: inherit;
   }
   .comment .comment_u_t{
   	    color: #848482;
   	    font-size: 9pt;
   	    /*margin-left: 20px;*/
   }
	</style>
</head>
<body>
<!-- Left Panel -->
  <?php require_once 'includes/left_nav.php'; ?>
<!-- Right Panel -->
<div id="right-panel" class="right-panel">
<!-- Header-->
 <?php require_once 'includes/top_nav.php'; ?>
<!-- Header-->
        <div class="content mt-10">
        <!-- displaypersoning institution basic info -->
        <div class="col-lg-12">
                            <div style="display: inherit; padding-bottom: 30px;" class="card">
                                <div class="card-header">
                                	
                                  <h4><?php echo $spouse['given_name'].' '.$spouse['family_name'].' '.$spouse['other_name'];?> </h4>
                                </div>
                                
					<!-- tab content here  -->
					<?php
					
						echo '<ul class="list-group">';
					
						 //for the the rest
						 echo "<li class='list-group-item info-list'><div class='labels'>Gender</div><div class='datas'>{$spouse['gender']}</div></li>";  
						 echo "<li class='list-group-item info-list'><div class='labels'>Date of birth</div><div class='datas'>{$spouse['dob']}</div></li>"; 
						 echo "<li class='list-group-item info-list'><div class='labels'>Place of birth</div><div class='datas'>{$spouse['birth_place']}</div></li>"; 
						 $location  = $database->fetch_array($database->query("SELECT name FROM countries WHERE id ='{$spouse['birth_nationality']}' LIMIT 1 "));
						 echo "<li class='list-group-item info-list'><div class='labels'>Birth nationality</div><div class='datas'>{$location['name']}</div></li>";  
						 echo "<li class='list-group-item info-list'><div class='labels'>Passport</div><div class='datas'>{$spouse['passport']}</div></li>";  
						 $location  = $database->fetch_array($database->query("SELECT name FROM countries WHERE id ='{$spouse['issued_country']}' LIMIT 1 "));
						 echo "<li class='list-group-item info-list'><div class='labels'>Issued country</div><div class='datas'>{$location['name']}</div></li>";  
						 echo "<li class='list-group-item info-list'><div class='labels'>Issued date</div><div class='datas'>{$spouse['issued_date']}</div></li>";  
						 echo "<li class='list-group-item info-list'><div class='labels'>Expiry date</div><div class='datas'>{$spouse['expiry_date']}</div></li>"; 
						 echo "<li class='list-group-item info-list'><div class='labels'>Email</div><div class='datas'>{$spouse['email']}</div></li>";  
						 echo "<li class='list-group-item info-list'><div class='labels'>Phone</div><div class='datas'>{$spouse['phone']}</div></li>"; 
						 echo "<li class='list-group-item info-list'><div class='labels'>Registered time</div><div class='datas'>{$spouse['time']}</div></li>";
						 $user  = $database->fetch_array($database->query("SELECT fname,lname FROM user WHERE id ='{$spouse['user']}' LIMIT 1 "));  
						 echo "<li class='list-group-item info-list last-child'><div class='labels'>Registered by</div><div class='datas'>{$user['fname']} {$user['lname']}</div></li>";
						
						echo '</ul>';
						
						$href = "add-companions?cpn=".rawurlencode($kd_data[0])."&dpl=".rawurlencode(encrypt_decrypt('encrypt', $spouse['diplomat']));
						$back_href = "diplomats?dpl=".rawurlencode(encrypt_decrypt('encrypt', $spouse['diplomat'])).rawurlencode("%companions");
						
					
					?>
					<a href="<?=$back_href?>" class='edit-link'><button  class='btn btn-primary w-100 mb-10 border-radius-0 btn-lg back_btn'>Back</button></a>
					<a href="<?=$href?>" class='edit-link'><button class='btn w-100 border-radius-0 mb-10 btn-primary btn-lg edit_btn'>Edit</button></a>
					<hr style="margin: 5px 2%;" />
					<h4 class="mt-20" style="font-family: inherit;margin-left: 20px;margin-bottom: 20px;">Comments</h4>
					<?php
					   $comment_query =$database->query("SELECT * FROM comments WHERE owner ='$sp' AND status='1' AND owner_type='cpn' ORDER BY time ASC "); 
					   while ($comments  = $database->fetch_array($comment_query)){
					    	echo "<div class='comment p-15' style='background-color:#fafafa'>";
						       $comment = nl2br($comments['comment']);
							   
							   $user  = $database->fetch_array($database->query("SELECT fname,lname FROM user WHERE id ='{$comments['user']}' LIMIT 1 "));  
							   echo "<p class='comment_u_t pl-0'><b>{$user['fname']} {$user['lname']}</b> <b style='float:right; font-size:11px'>{$comments['time']}</b><hr> <p>";
							   echo "<p class='comment_data mb-0 fs-14 pl-0'>$comment </p>";
						    echo "</div>";
					    }
						
					?>
					
					<button class="btn btn-primary w-150" id="comment_button" type="button" onclick="myFunction()" title='Write a comment' ><i class="fa fa-comments-o fa-2x" aria-hidden="true"></i> <span class="fs-15"></span> Write a comment</button>					    
				    <center>
				    	<form id="comments" style="display: none" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" >
						<textarea id="comment_field" class="form-control mt-20 w-50p" rows="5" name="comment_field" required placeholder="Your comment"></textarea>
						<input type="hidden" name="companions" value="<?php echo $sp; ?>" />
                       <input type="submit" value="SAVE" id="submit_comment" name="submit_comment"  class="btn btn-success p-8 w-150 mt-10 btn-xs" />																									
                    	</form>
				    </center>
				 
                   <!-- End of tab content  -->                
                 </div>         
               </div>
   
       </div> <!-- .content -->
  
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
     
function myFunction() {
    var x = document.getElementById("comments");
    if (x.style.display === "none") {
        x.style.display = "block";
    } else {
        x.style.display = "none";
    }
}

</script>

</body>
</html>