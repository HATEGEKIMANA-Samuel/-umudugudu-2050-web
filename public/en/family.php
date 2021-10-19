<?php
require_once("includes/validate_credentials.php");
require_once "model/family.php";
if (isset($_POST['comment_field']) && !empty($_POST['comment_field'])) {
	$comment = htmlentities($database->escape_value($_POST['comment_field']));
	$sql = "INSERT INTO comments(owner,comment,owner_type,time,user) VALUES({$_POST['diplomat']},'$comment','dpl',NOW(),{$_SESSION["id"]})";
	$database->query($sql);
	$dpl = rawurlencode(encrypt_decrypt('encrypt', $_POST['diplomat']));
	header("location:family?dpl=$dpl");
	exit();
}
///for profile picture
if (isset($_POST['profile']) && !empty($_POST['profile'])) {
	///////
	$message;
	$file = $_FILES['profile_pic'];
	$db_file_name = basename($file['name']);
	$ext = explode(".", $db_file_name);
	$fileExt = end($ext);
	if ($fileExt == "jpeg" || $fileExt == "png" || $fileExt == "jpg" || $fileExt == "gif" || $fileExt == "JPEG" || $fileExt == "PNG" || $fileExt == "JPG" || $fileExt == "GIF" || $fileExt == "ico" || $fileExt == "ICO") {
		$sql = "SELECT photo 
			FROM diplomats WHERE id={$_POST['diplomat']} LIMIT 1 ";
		$query = $database->query($sql);
		$row = $database->fetch_array($query);
		if (!empty($row['photo'])) {
			$path  = "uploads/" . $row['photo'];
			unlink($path);
		}
		$upload_errors = array(
			// http://www.php.net/manual/en/features.file-upload.errors.php
			UPLOAD_ERR_OK 				=> "No errors.",
			UPLOAD_ERR_INI_SIZE  	=> "Larger than upload_max_filesize.",
			UPLOAD_ERR_FORM_SIZE 	=> "Larger than form MAX_FILE_SIZE.",
			UPLOAD_ERR_PARTIAL 		=> "Partial upload.",
			UPLOAD_ERR_NO_FILE 		=> "No file.",
			UPLOAD_ERR_NO_TMP_DIR => "No temporary directory.",
			UPLOAD_ERR_CANT_WRITE => "Can't write to disk.",
			UPLOAD_ERR_EXTENSION 	=> "File upload stopped by extension."
		);
		if (!$file || empty($file) || !is_array($file)) {
			$messages = "No file was attached";
		} else if ($file["error"] != 0) {
			$messages = $upload_errors[$file["error"]];
		} else if ($file["error"] == 0) {
			$size = $file['size'];
			$type = $file['type'];
			$temp_name = $file['tmp_name'];
			$db_file_name = basename($file['name']);
			$ext = explode(".", $db_file_name);
			$fileExt = end($ext);
			$taget_file = $_POST['diplomat'] . '-' . rand_string(20) . "." . $fileExt;
			$sql = "UPDATE diplomats SET photo ='{$taget_file}' WHERE id = {$_POST['diplomat']} ";
			$query = $database->query($sql);
			$afected = $database->affected_rows($query);
			//uploading file
			$wmax = 300;
			$hmax = 300;
			$path  = "profiles/" . $taget_file;
			$path1 = "profiles/" . $taget_file;
			if (move_uploaded_file($temp_name, $path) && ($afected == 1)) {
				include_once("includes/image_resize.php");
				img_resize($path, $path1, $wmax, $hmax, $fileExt);
				$messages = "<span style='color:green;'>Done</span>";
			} else
				$messages = "<span style='color:red;'>Erros occur!</span>";
		}
	} else {
		$messages = "<span style='color:red;'>The selected file was not an image.</span>";
	}
	////
	$messages = rawurlencode($messages);
	$dpl = rawurlencode(encrypt_decrypt('encrypt', $_POST['diplomat'])) . rawurlencode("%additional");
	header("location:family?dpl=$dpl&messagep=$messages");
	exit();
}
//For attachment
if (isset($_POST['attachment_save']) && !empty($_POST['attachment_save'])) {
	///////
	$message;
	$file = $_FILES['attachment'];
	$db_file_name = basename($file['name']);
	$ext = explode(".", $db_file_name);
	$fileExt = end($ext);
	if ($fileExt == "docx" || $fileExt == "pdf") {
		// $sql = "SELECT photo 
		// FROM diplomats WHERE id={$_POST['diplomat']} LIMIT 1 ";
		// $query = $database->query($sql);
		// $row = $database->fetch_array($query);
		// if (!empty($row['photo'])) {
		// $path  = "uploads/".$row['photo'];
		// unlink($path);
		// }
		$upload_errors = array(
			// http://www.php.net/manual/en/features.file-upload.errors.php
			UPLOAD_ERR_OK 				=> "No errors.",
			UPLOAD_ERR_INI_SIZE  	=> "Larger than upload_max_filesize.",
			UPLOAD_ERR_FORM_SIZE 	=> "Larger than form MAX_FILE_SIZE.",
			UPLOAD_ERR_PARTIAL 		=> "Partial upload.",
			UPLOAD_ERR_NO_FILE 		=> "No file.",
			UPLOAD_ERR_NO_TMP_DIR => "No temporary directory.",
			UPLOAD_ERR_CANT_WRITE => "Can't write to disk.",
			UPLOAD_ERR_EXTENSION 	=> "File upload stopped by extension."
		);
		if (!$file || empty($file) || !is_array($file)) {
			$messages = "No file was attached";
		} else if ($file["error"] != 0) {
			$messages = $upload_errors[$file["error"]];
		} else if ($file["error"] == 0) {
			$size = $file['size'];
			$type = $file['type'];
			$temp_name = $file['tmp_name'];
			$db_file_name = basename($file['name']);
			$ext = explode(".", $db_file_name);
			$fileExt = end($ext);
			$taget_file = $_POST['diplomat'] . '-' . rand_string(20) . "." . $fileExt;
			$finame = htmlentities($database->escape_value($db_file_name));
			$sql = "INSERT INTO attachments(attachments,name,owner,owner_type,user,time) VALUES('{$taget_file}','{$finame}',{$_POST['diplomat']},'dpl',{$_SESSION["id"]},NOW()) ";
			$query = $database->query($sql);
			$afected = $database->inset_id();

			$path  = "attachments/" . $taget_file;
			if (move_uploaded_file($temp_name, $path) && ($afected >= 1)) {
				//$messages = "<span style='color:green;'>Done</span>";
			} else
				$messages = "<span style='color:red;'>Erros occur!</span>";
		}
	} else {
		$messages = "<span style='color:red;'>File format is not allowed. $db_file_name</span>";
	}
	////
	$messages = rawurlencode($messages);
	$dpl = rawurlencode(encrypt_decrypt('encrypt', $_POST['diplomat'])) . rawurlencode("%additional");
	header("location:family?dpl=$dpl&message=$messages");
	exit();
}

if (isset($_GET['dpl'])) {
	$dpl_data = explode('%', $_GET['dpl']);
	$found = count($dpl_data);
	if ($found > 1) {
		$dpl = encrypt_decrypt('decrypt', $dpl_data[0]);
	} else {
		$dpl = encrypt_decrypt('decrypt', $_GET['dpl']);
	}
} else {
	header("location:404");
}
if (!isset($dpl) || !is_numeric($dpl)) {
	header("location:404");
} else {
	//$query = $database->query("SELECT * FROM diplomats WHERE id ='$dpl' AND status='1' LIMIT 1 ");
	$diplomat = family::getMember($database, $dpl);
	if (count($diplomat) == 0) {
		header("location:404");
	}
}

?>
<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="">
<!--<![endif]-->

<head>
	<?php require_once("includes/head.php"); ?>

	<link rel="stylesheet" href="assets/css/font-roboto-varela.css">
	<link rel="stylesheet" href="assets/css/material-icon.css">
	<link rel="stylesheet" href="css/family.css">
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

						<p class="" style="font-size: 15px">The Head of the family: <?php echo $diplomat['given_name'] . ' ' . $diplomat['family_name'] . ' ' . $diplomat['other_name'];

																					?>
						</p>
					</div>
					<div class="card-body">
						<div class="default-tab">
							<?php


							$tabs = array("info", "additional", "members", "covid_19", "help");
							if (isset($dpl_data[1]) && (in_array($dpl_data[1], $tabs))) {
								$va = $dpl_data[1];
								$$va =  "active";
							} else {
								$info = "active";
							}
							?>
							<nav>
								<div class="nav fs-14 nav-tabs" id="nav-tab" role="tablist">
									<a class="nav-item nav-link <?php if (isset($info)) echo $info; ?>" href="family?dpl=<?php echo rawurlencode($dpl_data[0]) . rawurlencode("%info"); ?>">General profile.</a>
									<a class="nav-item nav-link <?php if (isset($members)) echo $members; ?>" href="family?dpl=<?php echo rawurlencode($dpl_data[0]) . rawurlencode("%members"); ?>">Family members.</a>
									<a class="nav-item nav-link <?php if (isset($help)) echo $help; ?>" href="family?dpl=<?php echo  rawurlencode($dpl_data[0]) . rawurlencode("%help"); ?>">Help</a>
									<a class="nav-item nav-link <?php if (isset($additional)) echo $additional; ?>" href="family?dpl=<?php echo  rawurlencode($dpl_data[0]) . rawurlencode("%additional"); ?>">Additional.</a>
								</div>
							</nav>
						</div>
					</div>
					<!-- tab content here  -->
					<?php

					if (isset($info)) { /// for basic Info.
						echo '<ul class="list-group">';
						echo "<li class='list-group-item info-list'><div class='labels'>Given Name</div><div class='datas'>{$diplomat['given_name']}</div></li>";
						echo "<li class='list-group-item info-list'><div class='labels'>Family Name</div><div class='datas'>{$diplomat['family_name']}</div></li>";
						echo "<li class='list-group-item info-list'><div class='labels'>Gender</div><div class='datas'>{$diplomat['gender']}</div></li>";
						echo "<li class='list-group-item info-list'><div class='labels'>Date of birth</div><div class='datas'>{$diplomat['dob']}</div></li>";
						echo "<li class='list-group-item info-list'><div class='labels'>Place of birth</div><div class='datas'>{$diplomat['birth_place']}</div></li>";
						echo "<li class='list-group-item info-list'><div class='labels'>Status</div><div class='datas'>{$diplomat['marital_status']}</div></li>";
						$location  = $database->fetch_array($database->query("SELECT name FROM countries WHERE id ='{$diplomat['birth_nationality']}' LIMIT 1 "));
						echo "<li class='list-group-item info-list'><div class='labels'>Nationality</div><div class='datas'>{$location['name']}</div></li>";
						echo "<li class='list-group-item info-list'><div class='labels'>ID/Passport</div><div class='datas'>{$diplomat['document_id']}</div></li>";
						$location  = $database->fetch_array($database->query("SELECT name FROM countries WHERE id ='{$diplomat['issued_country']}' LIMIT 1 "));
						// new column
						echo "<li class='list-group-item info-list'><div class='labels'>education level</div><div class='datas'>{$diplomat['level_education']}</div></li>";
						echo "<li class='list-group-item info-list'><div class='labels'>Isibo</div><div class='datas'>{$diplomat['isibo']}</div></li>";

						echo "<li class='list-group-item info-list'><div class='labels'>House rent</div><div class='datas'>{$diplomat['rent_house']}</div></li>";
						if ($diplomat['rent_house'] == "yego") {
							echo "<li class='list-group-item info-list'><div class='labels'>Home information</div><div class='datas'>{$diplomat['house_info']}</div></li>";
						} else {


							echo "<li class='list-group-item info-list'><div class='labels'>Number of houses</div><div class='datas'>{$diplomat['number_house']}</div></li>";
						}
						echo "<li class='list-group-item info-list'><div class='labels'>Profession</div><div class='datas'>{$diplomat['occupation']}</div></li>";
						// end of giba column
						if ($diplomat["type"] != "ID") {
							echo "<li class='list-group-item info-list'><div class='labels'>Issued Country</div><div class='datas'>{$location['name']}</div></li>";
							echo "<li class='list-group-item info-list'><div class='labels'>Issued Date</div><div class='datas'>{$diplomat['issued_date']}</div></li>";
							echo "<li class='list-group-item info-list'><div class='labels'>Expired Date</div><div class='datas'>{$diplomat['expiry_date']}</div></li>";
						}
						echo "<li class='list-group-item info-list'><div class='labels'>Phone</div><div class='datas'>{$diplomat['phone']}</div></li>";
						echo "<li class='list-group-item info-list'><div class='labels'>Email</div><div class='datas'>{$diplomat['email']}</div></li>";
						echo "<li class='list-group-item info-list'><div class='labels'>Family members</div><div class='datas'>{$diplomat['members']}</div></li>";
						echo "<li class='list-group-item info-list'><div class='labels'>Ubudehe</div><div class='datas'>{$diplomat['ubudehe']}</div></li>";
						echo "<li class='list-group-item info-list'><div class='labels'>Registered date</div><div class='datas'>{$diplomat['time']}</div></li>";
						$user  = $database->fetch_array($database->query("SELECT fname,lname FROM user WHERE id ='{$diplomat['user']}' LIMIT 1 "));
						echo "<li class='list-group-item info-list last-child'><div class='labels'>Author</div><div class='datas'>{$user['fname']} {$user['lname']}</div></li>";

						echo '</ul>';
						$href = "add-family?dpl=" . rawurlencode($dpl_data[0]);

					?>
						<a href="<?= $href ?>" class='edit-link'><button class='btn btn-primary w-150 border-radius-0 mb-10 btn-lg edit_btn'>Edit</button></a>
						<button type="button" class="btn text-white bg-dark w-150 border-radius-0  btn-lg" onclick="openModalMovements(this);" data-action="get_movements" data-id="<?= $diplomat['document_id'] ?>">Where he/she lived</button>
						<hr style="margin: 5px 2%;" />

						<h4 class="pt-10" style="font-family: inherit;margin-left: 2%;margin-bottom: 20px;">Comments</h4>
						<?php
						$comment_query = $database->query("SELECT * FROM comments WHERE owner ='$dpl' AND status='1' AND owner_type='dpl' ORDER BY time ASC ");
						while ($comments  = $database->fetch_array($comment_query)) {
							echo "<div class='comment'>";
							$comment = nl2br($comments['comment']);
							$user  = $database->fetch_array($database->query("SELECT fname,lname FROM user WHERE id ='{$comments['user']}' LIMIT 1 "));
							echo "<span class='comment_u_t'><b> <i class='fa fa-user'></i> {$user['fname']} {$user['lname']}</b> <b class='pull-right'>{$comments['time']}</b> <hr><span>";
							echo "<p class='comment_data mb-0'>$comment </p>";
							echo "</div>";
						}

						?>

						<button class="btn btn-primary" id="comment_button" type="button" onclick="myFunction()" title='Write a comment'><i class="fa fa-comments-o fa-2x" aria-hidden="true"></i> <span></span class="fs-18 ">Write a comment</button>

						<center>
							<form id="comments" style="display: none" class="mt-20" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
								<hr>
								<textarea id="comment_field" class="form-control mt-30 backgroung-white w-50p border-radius-0" rows="5" name="comment_field" required placeholder="Your comment" style="background-color: white !important"></textarea>
								<input type="hidden" name="diplomat" value="<?php echo $dpl; ?>" />
								<input type="submit" value="SAVE" id="submit_comment" name="submit_comment" class="btn btn-primary mt-20 w-100 btn-xs" />
							</form>
						</center>
					<?php } ?>

					<!-- For family member -->
					<?php
					if (isset($members)) {
						$href_kids_add = "add-member?dpl=" . rawurlencode($dpl_data[0]);
						echo "<a href=\"$href_kids_add\" class='edit-link'><button style='margin-bottom:20px' class='btn btn-primary btn-lg mt-10 edit_btn'>Add</button></a>";
						$i = 1;
						$sql = "SELECT id,head,given_name,family_name,other_name,document_id,relationship FROM members WHERE head= '$dpl' AND status ='1' ";


						$spouse_query = $database->query($sql);
						echo '
				 <table style="width:98%;margin-left:1%;" class="table table-striped fs-14">
				  <tbody>
				 ';
						while ($kids_data  = $database->fetch_array($spouse_query)) {
							echo '<tr>';
							echo "<td scope='row' class='numbering' style=\"width: 10%;\">$i</td>";
							echo "<td style=\"width: 40%;\">{$kids_data['given_name']} {$kids_data['family_name']} {$kids_data['other_name']}; {$kids_data['document_id']}</td>";
							$kids_data['relationship'] = lang($kids_data['relationship']);
							echo "<td style=\"width: 40%;\">{$kids_data['relationship']}</td>";
							$href = "kids?kd=" . rawurlencode(encrypt_decrypt('encrypt', $kids_data['id']));
							echo "<td style=\"width: 10%;\"><a style href='$href'>
							           <button type='button' class='btn btn-default btn-sm'>
								          <i class=\"ti-user\"></i> More
								       </button>
							       </a>
							</td>";
							echo '</tr>';
							$i++;
						}
						echo " </tbody>
				</table>";
					}
					?>

					<!-- For help -->
					<?php
					if (isset($help)) {
						$href_cars_add = "add-help?dpl=" . rawurlencode($dpl_data[0]);
						echo "<a href=\"$href_cars_add\" class='edit-link'><button style='margin-bottom:20px' class='btn btn-primary btn-lg mt-10 edit_btn'>Add</button></a>";
						$i = 1;
						$sql = "SELECT id,help,time ,giver FROM help WHERE family= $dpl AND status ='1' ";
						$car_query = $database->query($sql);
						echo '
				 <table style="width:98%;margin-left:1%;" class="table table-striped fs-14">
				  <tbody>
				 ';
						while ($cars_data  = $database->fetch_array($car_query)) {
							echo '<tr>';
							echo "<td scope='row' class='numbering' style=\"width: 10%;\">$i</td>";
							echo "<td style=\"width: 20%;\">{$cars_data['time']} </td>";
							echo "<td style=\"width: 20%;\">{$cars_data['help']}</td>";
							echo "<td style=\"width: 20%;\">{$cars_data['giver']}</td>";
							$href = "help?cr=" . rawurlencode(encrypt_decrypt('encrypt', $cars_data['id']));
							echo "<td style=\"width: 10%;\"><a style href='$href'>
							           <button type='button' class='btn btn-default btn-sm'>
								          <i class=\"ti-car\"></i> More
								       </button>
							       </a>
							</td>";
							echo '</tr>';
							$i++;
						}
						echo " </tbody>
				</table>";
					}
					?>

					<!-- For additional info -->
					<?php if (isset($additional)) { ?>
						<?php
						if (isset($diplomat['photo']) && !empty($diplomat['photo'])) {
							$path = "profiles/" . $diplomat['photo'];
						} else {
							$path = "images/default_profile.jpg";
						}
						?>
						<div class="row">

							<div class="col-md-2 mt-20">
								<img src="<?= $path ?>" class="img-thumbnail ml-20" alt="Cinque Terre" width="250" height="250"><br />
								<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
									<input type="hidden" name="diplomat" value="<?= $dpl ?>" />
									<label for="file-upload" class="custom-file-upload w-100p text-center ml-20 mt-10">
										<i class="fa fa-cloud-upload"></i> Change
									</label>
									<input id="file-upload" onchange="show_profile_savebtn('file-upload')" name="profile_pic" type="file" />
									<input type="submit" style="display: none;" value="Save" id="profile_savebtn" name="profile" class="btn btn-outline-secondary" />
								</form>
								<?php
								if (isset($_GET['messagep'])) {
									echo $_GET['messagep'];
								}
								?>
							</div>

							<!-- 			   <div class="col-md-12"><hr class="mt-10 mb-10"></div> -->

							<div class="col-md-10 mt-20 pl-20">
								<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
									<input type="hidden" name="diplomat" value="<?= $dpl ?>" />

									<div class="upload-btn-wrapper">
										<button class="btn btn-primary fs-14"><i class="glyphicon glyphicon-paperclip"></i>Add attachment</button>
										<input onchange="show_attach_savebtn('attachment_in')" id="attachment_in" type="file" name="attachment" />
									</div>
									<input type="submit" style="display: none;" value="Save" id="attach_savebtn" name="attachment_save" class="btn btn-outline-primary" />
								</form>

								<div style="margin-top: 20px;">
									<?php
									$sql = "SELECT id,name,attachments FROM attachments WHERE owner= '$dpl' AND owner_type='dpl' AND  status ='1' ORDER BY id DESC ";
									$attach_query = $database->query($sql);
									while ($attach_data  = $database->fetch_array($attach_query)) {
										echo "<p class='attachments_lists'> <i classs='fa fa-cloud-upload'></i> <a href=\"attachments/{$attach_data['attachments']}\"> {$attach_data['name']} -<small>Attachment</small> </a></p>";
									}

									?>
								</div>

								<?php
								if (isset($_GET['message'])) {
									echo $_GET['message'];
								}

								?>
							</div>
						</div>

					<?php }

					?>

					<!-- End of tab content  -->
				</div>
			</div>
		</div> <!-- .content -->
		<!-- modal used for showing people movement -->
		<div class="modal " tabindex="-1" role="dialog" id="modalMovement" data-backdrop="false">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title"> Migration <b id="mtitle"></b></h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="$('.contentHolder').html('');">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body contentHolder">
						<!-- <p>Modal body text goes here.</p> -->
					</div>
					<div class="modal-footer">
						<!-- <button type="button" class="btn btn-primary">Save changes</button> -->
						<button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="$('.contentHolder').html('');">Funga</button>
					</div>
				</div>
			</div>
		</div>
		<!-- end of modal -->

		<script src="assets/js/vendor/jquery-2.1.4.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script src="js/bootstrap-datepicker.js"></script>
		<script src="assets/js/plugins.js"></script>
		<script src="js/ajax.js"></script>
		<script src="assets/js/main.js"></script>
		<script src="assets/js/custom.js"></script>
		<script src="js/generic.js"></script>
		<script>
			$('.datepicker').datepicker({
				endDate: '0d',
				format: 'yyyy-mm-dd'
			});

			function openModalMovements(e) {
				$("#modalMovement").modal("show");
				showWait(
					".contentHolder",
					"font-size:50px;margin-left:40%;border-radius:4rem;overflow:hide;overflow:hidden;color:darkred;background:#60085f;"
				);
				var formData = new FormData();
				formData.set("action", $(e).attr("data-action"));
				formData.set("rwandan_id", $(e).attr("data-id"));
				post(formData, $(e), ".contentHolder");
			}

			function myFunction() {
				var x = document.getElementById("comments");
				if (x.style.display === "none") {
					x.style.display = "block";
				} else {
					x.style.display = "none";
				}
				$('html, body').animate({
					scrollTop: $("#comments").offset().top
				}, 1000);
			}

			function show_profile_savebtn(profile) {
				var fullPath = document.getElementById(profile).value;
				if (fullPath) {
					_('profile_savebtn').style.display = "inline-block";
				} else {
					_('profile_savebtn').style.display = "none";
				}
			}

			function show_attach_savebtn(attachh) {
				var filePath = document.getElementById(attachh).value;
				if (filePath) {
					_('attach_savebtn').style.display = "inline-block";
				} else {
					_('attach_savebtn').style.display = "none";
				}
			}
		</script>
</body>

</html>