<?php
require_once("includes/validate_credentials.php");
require "model/family.php";

///for profile picture
$level = session::get("level");
if (isset($_POST['profile']) && !empty($_POST['profile'])) {
	///////
	$message;
	$file = $_FILES['profile_pic'];
	$db_file_name = basename($file['name']);
	$ext = explode(".", $db_file_name);
	$fileExt = end($ext);
	if ($fileExt == "jpeg" || $fileExt == "png" || $fileExt == "jpg" || $fileExt == "gif" || $fileExt == "JPEG" || $fileExt == "PNG" || $fileExt == "JPG" || $fileExt == "GIF" || $fileExt == "ico" || $fileExt == "ICO") {
		$sql = "SELECT photoPassport as photo 
			FROM citizen WHERE citizenId={$_POST['diplomat']} LIMIT 1 ";
		$query = $database->query($sql);
		$row = $database->fetch_array($query);
		if (!empty($row['photo'])) {
			$path  = "profiles/" . $row['photo'];
			if (file_exists($path))
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
			$sql = "UPDATE citizen SET photoPassport ='{$taget_file}' WHERE citizenId = {$_POST['diplomat']} ";
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
	exit(0);
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
	exit(0);
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

						<p class="" style="font-size: 15px">Umukuru w' umuryango: <?php echo $diplomat['givenName'] . ' ' . $diplomat['familyName'] . ' ' . $diplomat['otherName'];

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
									<a class="nav-item  navigate nav-link <?php if (isset($info)) echo $info; ?>" href="family?dpl=<?php echo rawurlencode($dpl_data[0]) . rawurlencode("%info"); ?>">Umwirondoro rusange.</a>
									<a class="nav-item navigate nav-link <?php if (isset($members)) echo $members; ?>" href="family?dpl=<?php echo rawurlencode($dpl_data[0]) . rawurlencode("%members"); ?>">Abagize umuryango.</a>
									<a class="nav-item navigate  nav-link <?php if (isset($help)) echo $help; ?>" href="family?dpl=<?php echo  rawurlencode($dpl_data[0]) . rawurlencode("%help"); ?>">Abashyitsi</a>
									<a class="nav-item navigate  nav-link <?php if (isset($additional)) echo $additional; ?>" href="family?dpl=<?php echo  rawurlencode($dpl_data[0]) . rawurlencode("%additional"); ?>">Andi makuru.</a>
								</div>
							</nav>
						</div>
					</div>
					<!-- tab content here  -->
					<?php
					ob_start();
					if (isset($info)) { /// for basic Info.
						echo '<ul class="list-group">';
						echo "<li class='list-group-item info-list'><div class='labels'>Izina rusange</div><div class='datas'>{$diplomat['givenName']}</div></li>";
						echo "<li class='list-group-item info-list'><div class='labels'>Izina ry'umuryango</div><div class='datas'>{$diplomat['familyName']}</div></li>";
						echo "<li class='list-group-item info-list'><div class='labels'>Igitsina</div><div class='datas'>{$diplomat['gender']}</div></li>";
						echo "<li class='list-group-item info-list'><div class='labels'>Itariki yamavuko</div><div class='datas'>{$diplomat['dob']}</div></li>";
						echo "<li class='list-group-item info-list'><div class='labels'>Aho yavukiye</div><div class='datas'>{$diplomat['birthplace']}</div></li>";
						echo "<li class='list-group-item info-list'><div class='labels'>Irangamimerere</div><div class='datas'>{$diplomat['martialstatus']}</div></li>";
						$location  = $database->fetch_array($database->query("SELECT name FROM countries WHERE id ='{$diplomat['birthNationality']}' LIMIT 1 "));
						$location["name"] = $diplomat['birthNationality'];
						if (is_numeric($diplomat['birthNationality'])) {
							$location  = $database->fetch_array($database->query("SELECT name FROM countries WHERE id ='{$diplomat['birthNationality']}' LIMIT 1 "));
						}
						echo "<li class='list-group-item info-list'><div class='labels'>Ubwenegihugu</div><div class='datas'>{$location['name']}</div></li>";
						if ($level == 2) {
							echo "<li class='list-group-item info-list'><div class='labels'>ID/Pasiporo</div><div class='datas'>{$diplomat['documentNumber']}</div></li>";
						}
						$location["name"] = $diplomat['issuedCountry'];
						if (is_numeric($diplomat['issuedCountry'])) {
							if ($diplomat['issuedCountry'] != 0) {
								$location  = $database->fetch_array($database->query("SELECT name FROM countries WHERE id ='{$diplomat['issuedCountry']}' LIMIT 1 "));
							} else {
								$location["name"] = "";
							}
						}
						//$location  = $database->fetch_array($database->query("SELECT name FROM countries WHERE id ='{$diplomat['issuedCountry']}' LIMIT 1 "));
						// new column
						echo "<li class='list-group-item info-list'><div class='labels'>Urwego ry'uburezi</div><div class='datas'>{$diplomat['level_of_education']}</div></li>";
						echo "<li class='list-group-item info-list'><div class='labels'>Isibo</div><div class='datas'>{$diplomat['isibo']}</div></li>";
						$is_rent = !empty($diplomat['landLord']) ? "yego" : "hoya";
						echo "<li class='list-group-item info-list'><div class='labels'>Arakodesha</div><div class='datas'>{$is_rent}</div></li>";
						if (!empty($diplomat['landLord'])) {
							echo "<li class='list-group-item info-list'><div class='labels'>Amakuru ya nyirinzu</div><div class='datas'>{$diplomat['landLord']}</div></li>";
						} else {
							echo "<li class='list-group-item info-list'><div class='labels'>Umubare w'inzu</div><div class='datas'>{$diplomat['number_of_rent_house']}</div></li>";
						}
						echo "<li class='list-group-item info-list'><div class='labels'>Umwuga</div><div class='datas'>{$diplomat['occupation']}</div></li>";
						// end of giba column
						if ($diplomat["documentType"] != "ID") {
							echo "<li class='list-group-item info-list'><div class='labels'>Igihugu cyayitanze</div><div class='datas'>{$location['name']}</div></li>";
							echo "<li class='list-group-item info-list'><div class='labels'>Igihe yatangiwe</div><div class='datas'>{$diplomat['issuedDate']}</div></li>";
							echo "<li class='list-group-item info-list'><div class='labels'>Igihe izarangira</div><div class='datas'>{$diplomat['expiryDate']}</div></li>";
						}
						echo "<li class='list-group-item info-list'><div class='labels'>Telefone</div><div class='datas'>{$diplomat['mobile']}</div></li>";
						echo "<li class='list-group-item info-list'><div class='labels'>Emeli</div><div class='datas'>{$diplomat['email']}</div></li>";
						$diplomat['members'] = family::getTotal($database, "citizen", " where familyId='{$dpl}'");
						echo "<li class='list-group-item info-list'><div class='labels'>Abagize umuryango</div><div class='datas'>{$diplomat['members']}</div></li>";
						echo "<li class='list-group-item info-list'><div class='labels'>Icyiciro cy'ubudehe</div><div class='datas'>{$diplomat['ubudehe']}</div></li>";
						echo "<li class='list-group-item info-list'><div class='labels'>Igihe yanditswe</div><div class='datas'>{$diplomat['created_at']}</div></li>";
						$user  = $database->fetch_array($database->query("SELECT fname,lname FROM user WHERE id ='{$diplomat['created_by']}' LIMIT 1 "));
						$user_names = $user['fname'] . ' ' . $user['lname'];
						echo "<li class='list-group-item info-list last-child'><div class='labels'>Uwamwanditse</div><div class='datas'>$user_names</div></li>";

						echo '</ul>';
						$href = "add-family?dpl=" . rawurlencode($dpl_data[0]);

					?>
						<a href="<?= $href ?>" class='edit-link'><button class='btn btn-primary w-150 border-radius-0 mb-10 btn-lg edit_btn'>Hindura</button></a>
						<button type="button" class="btn text-white bg-dark w-150 border-radius-0  btn-lg navigate" onclick="openModalMovements('movement-list?dpl=<?= $_GET['dpl'] ?>');" data-action="get_movements">Reba Aho yabaye</button>
						<hr style="margin: 5px 2%;" />

						<h4 class="pt-10" style="font-family: inherit;margin-left: 2%;margin-bottom: 20px;">Comments</h4>
						<div id="commentHolder">
							<!-- comment here -->
						</div>

						<button class="btn btn-primary" id="comment_button" type="button" onclick="myFunction(this)" title='Andika Igitekerezo'><i class="fa fa-comments-o fa-2x" aria-hidden="true"></i> <span></span class="fs-18 ">Andika igitekerezo</button>
						<center>
							<form id="comments" style="display: none" class="mt-20" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
								<hr>
								<textarea id="comment_field" class="form-control mt-30 backgroung-white w-50p border-radius-0" rows="5" name="comment_field" required placeholder="Igitekerezo Cyawe" style="background-color: white !important"></textarea>
								<input type="hidden" name="owner" value="<?php echo $dpl; ?>" />
								<input type="hidden" name="type" value="dpl" />
								<input type="hidden" name="user" value="<?= $user_names ?>" />
								<input type="hidden" name="action" value="savecomment" />
								<input type="submit" value="SAVE" id="submit_comment" name="submit_comment" class="btn btn-primary mt-20 w-100 btn-xs" />
							</form>
						</center>
					<?php } ?>

					<!-- For family member -->
					<?php
					if (isset($members)) {
						$href_kids_add = "add-member?dpl=" . rawurlencode($dpl_data[0]);
						echo "<a href=\"$href_kids_add\" class='edit-link'><button style='margin-bottom:20px' class='btn btn-primary btn-lg mt-10 edit_btn'>Ongeraho</button></a>";
						$i = 1;
						$sql = "SELECT  citizenId as id,familyId as head,givenName,familyName,otherName, documentNumber as document_id, familyCategory as relationship FROM citizen WHERE familyId= '$dpl' ";

						$spouse_query = $database->query($sql);
						echo '
				 <table style="width:98%;margin-left:1%;" class="table table-striped fs-14">
				  <tbody>
				 ';

						while ($kids_data  = $database->fetch_array($spouse_query)) {

							echo '<tr>';
							echo "<td scope='row' class='numbering' style=\"width: 10%;\">$i</td>";
							if ($level == 2) {
								echo "<td style=\"width: 40%;\">{$kids_data['givenName']} {$kids_data['familyName']} {$kids_data['otherName']}   {$kids_data['document_id']}</td>";
							} else {
								echo "<td style=\"width: 40%;\">{$kids_data['givenName']} {$kids_data['familyName']} {$kids_data['otherName']}</td>";
							}
							$kids_data['relationship'] = $kids_data['relationship'] == "HEAD" ? 'umunyamuryango' : $kids_data['relationship'];
							$make_heady = (isset($kids_data['document_id']) && !empty($kids_data['document_id'])) ? "&nbsp;&nbsp;<span class='d-block text-primary' onclick=\"makeHeadOfFamily({$kids_data['id']},{$kids_data['head']})\" >Mugire umukuru w'umuryango</span>" : "";
							echo "<td style=\"width: 40%;\">{$kids_data['relationship']}  $make_heady
							    </td>";
							$href = "kids?kd=" . rawurlencode(encrypt_decrypt('encrypt', $kids_data['id']));
							echo "<td style=\"width: 10%;\"> <a style href='$href' class='navigate'>
							           <button type='button' class='btn btn-default btn-sm'>
								          <i class=\"ti-user\"></i> Byinshi
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
						$i = 1;
						//$href_kids_add = "add-member?dpl=" . rawurlencode($dpl_data[0]);
						// $href_cars_add = "add-help?dpl=" . rawurlencode($dpl_data[0]);
						// echo "<a href=\"$href_cars_add\" class='edit-link'><button style='margin-bottom:20px' class='btn btn-primary btn-lg mt-10 edit_btn'>Ongeraho</button></a>";
						// $i = 1;
						// $sql = "SELECT id,help,time,giver FROM help WHERE family= $dpl AND status ='1' ";
						// $car_query = $database->query($sql);
						//echo "<a href=\"$href_kids_add\" class='edit-link'><button style='margin-bottom:20px' class='btn btn-primary btn-lg mt-10 edit_btn'>Ongeraho</button></a>";
						echo '
				   <table style="width:98%;margin-left:1%;" class="table table-striped fs-14">
				  <tbody>
				 ';
						// get visitors 
						// check if family has visitors
						$response = family::familyHasVisitor($database, $dpl);
						if ($response > 0) {
							// get names and their id
							$visitors = family::getCurrentVisitorsInFamily($database, $dpl);
							foreach ($visitors as $key => $visitor) {
								echo "<tr id=\"tr-{$visitor['visitor_id']}\"\>";
								echo "<td scope='row' class='numbering' style=\"width: 10%;\">$i</td>";
								echo "<td style=\"width: 40%;\">{$visitor['givenName']} {$visitor['familyName']} {$visitor['otherName']}; {$visitor['document_id']}</td>";
								echo "<td style=\"width: 40%;\">Umushyitsi</td>";
								$table = $visitor["tb"] == "0" ? "kids?kd=" : "family?dpl=";
								$href = "$table" . rawurlencode(encrypt_decrypt('encrypt', $visitor['id']));
								echo "<td style=\"width: 10%;\">
								  <span style=\"display: flex; justify-content: space-between; align-items: center\">
								  	<a  href='#' class='visitorLeave' id=\"link-{$visitor['visitor_id']}\" onclick=\"visitorOutOfFamily({$visitor['visitor_id']})\">
							           <button type='button' class='btn btn-info btn-sm'>
								          <i class=\"ti-user\"></i> Yarahavuye?
								       </button>
							       </a>
									<a style href='$href' class='navigate'>
							           <button type='button' class='btn btn-default btn-sm'>
								          <i class=\"ti-user\"></i> Byinshi
								       </button>
							       </a>
								  </span>
							</td>";
								echo '</tr>';
								$i++;
							}
						}
						/*while ($cars_data  = $database->fetch_array($car_query)) {
							echo '<tr>';
							echo "<td scope='row' class='numbering' style=\"width: 10%;\">$i</td>";
							echo "<td style=\"width: 20%;\">{$cars_data['time']} </td>";
							echo "<td style=\"width: 20%;\">{$cars_data['help']}</td>";
							echo "<td style=\"width: 20%;\">{$cars_data['giver']}</td>";
							$href = "help?cr=" . rawurlencode(encrypt_decrypt('encrypt', $cars_data['id']));
							echo "<td style=\"width: 10%;\"><a style href='$href'>
							           <button type='button' class='btn btn-default btn-sm'>
								          <i class=\"ti-car\"></i> Byinshi
								       </button>
							       </a>
							</td>";
							echo '</tr>';
							$i++;
						}*/
						echo " </tbody>
				</table>";
					}
					?>
					<!-- For additional info -->
					<?php if (isset($additional)) {
					?>
						<?php
						if (isset($diplomat['photoPassport']) && !empty($diplomat['photoPassport'])) {
							$path = "profiles/" . $diplomat['photoPassport'];
						} else {
							$path = "images/default_profile.jpg";
						}
						?>
						<div class="row">
							<!-- Uploaded photo -->
							<div class="col-md-2 mt-20">
								<img src="<?= $path ?>" class="img-thumbnail ml-20" alt="Cinque Terre" width="250" height="250"><br />
								<!-- $_SERVER['PHP_SELF']; -->
								<?php if ($level == 2) { ?>
									<form action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="post" enctype="multipart/form-data">
										<input type="hidden" name="diplomat" value="<?= $dpl ?>" />
										<label for="file-upload" class="custom-file-upload w-100p text-center ml-20 mt-10">
											<i class="fa fa-cloud-upload"></i> Hindura
										</label>
										<input id="file-upload" onchange="show_profile_savebtn('file-upload')" name="profile_pic" type="file" />
										<input type="submit" style="display: none;" value="Save" id="profile_savebtn" name="profile" class="btn btn-outline-secondary" />
									</form>
								<?php
								}
								if (isset($_GET['messagep'])) {
									echo $_GET['messagep'];
								}
								?>
							</div>

							<!-- For Nida content -->
							<div class="col-md-3 mt-20">

								<?php
								//include 'soap/client.php';
								$idNum =  array('idnum' => $diplomat['documentNumber']);
								//	$details =  $client->getName($idNum);
								//if (!empty($details['DocumentNumber'])) {
								//$srcPhoto = "data:image/png;base64, " . $details['Photo'];
								?>
								<!-- <img src="<?= $srcPhoto ?>" class="img-thumbnail ml-20" alt="Cinque Terre" width="250" height="250"><br /> -->
								<?php
								// echo $details['Surnames'] . " ";
								// echo $details['ForeName'] . "<br /> ";
								// echo $details['DocumentNumber'] . "<br /> ";
								//	}
								?>
							</div>

							<!-- 			   <div class="col-md-12"><hr class="mt-10 mb-10"></div> -->

							<div class="col-md-7 mt-20 pl-20">
								<?php if ($level == 2) : ?>
									<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
										<input type="hidden" name="diplomat" value="<?= $dpl ?>" />

										<div class="upload-btn-wrapper">
											<button class="btn btn-primary fs-14"><i class="glyphicon glyphicon-paperclip"></i>Ongeraho attachment</button>
											<input onchange="show_attach_savebtn('attachment_in')" id="attachment_in" type="file" name="attachment" />
										</div>
										<input type="submit" style="display: none;" value="Save" id="attach_savebtn" name="attachment_save" class="btn btn-outline-primary" />
									</form>
								<?php endif ?>

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
					ob_end_flush();
					?>

					<!-- End of tab content  -->
				</div>
			</div>
		</div> <!-- .content -->
		<!-- modal used for showing people movement -->

		<!-- end of modal -->

		<script src="assets/js/vendor/jquery-2.1.4.min.js"></script>
		<script>
			function openModalMovements(url) {
				window.location.href = url;
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
		<script src="js/bootstrap.min.js"></script>
		<script src="js/bootstrap-datepicker.js"></script>
		<script src="assets/js/plugins.js"></script>
		<script src="js/ajax.js"></script>
		<script src="assets/js/main.js"></script>
		<script src="assets/js/custom.js"></script>
		<script src="js/generic.js"></script>
		<script>
			let owner = "<?= $dpl ?>";
			loadComments(owner);
			$("#comments").submit(function(e) {
				e.preventDefault();
				$.post("ajax/getComments", $(this).serialize(), function(data) {
					if (data.status) {
						let html = `<div class='comment p-15' style='background-color: #fafafa' ><p class='comment_u_t'><b>${data.user}</b> <b style='float : right; font-size: 11px'>${data.time}</b><hr> <p><p class='comment_data mb-0 fs-14'>${data.comment} </p></div>`;
						$("#commentHolder").append(html);
						$("#comment_field").val('');
						myFunction();
					}
				}, "json");
			});
			// load comment
			function loadComments(o) {
				$("#commentHolder").html("<h1>tegereza comment ...</h1>");
				$.get(`ajax/getComments?owner=${o}&type=dpl&action=loaddata`, function(data) {
					$("#loader").remove();
					$("#commentHolder").html(data);
				});
			}
			$('.datepicker').datepicker({
				endDate: '0d',
				format: 'yyyy-mm-dd'
			});
			// make member to become head of family
			function makeHeadOfFamily(...elem) {
				if (confirm("Uremeza iki gikorwa cyo guhindura umukuru w'umuryango?")) {
					$(".navigate").click();
					$.ajax({
						type: "POST",
						data: {
							new_head: elem[0],
							head: elem[1],
							action: "changefamilyhead"
						},
						url: "controller/familyController.php",
						dataType: "json",
						beforeSend: function() {
							$(".navigate").click();
						},
						success: function(data) {
							alert("Umukuru w'umuryango Yahindutse");
							//window.location.reload();
							window.location.href = `family?dpl=${data.head}`;
						},
						error: function(xhr) {
							$("#page-loader").addClass("d-none");
							alert("Igikorwa cyo guhindura Umukuru w'umuryango Ntabwo gikunze mwongere mugerageze");
							// alert(xhr.responseText);
						},
					});

				}
			}

			// confirm if visitor was leaved
			function visitorOutOfFamily(visitor_id) {
				let link = `#link-${visitor_id}`;
				if (confirm("Emeza igikorwa ")) {
					// showWait(link);
					$(link).append(
						`<i class='fa fa-spinner fa-spin gifWait text-warning' style="font-size:20px"></i>`
					);
					$(link).attr("disabled", "disabled");
					$.post(
						"controller/familyController.php", {
							action: "visitor_leave",
							id: visitor_id
						},
						function(res) {
							$(".gifWait").remove();
							if (res.status) {
								$("#tr-" + visitor_id).remove();
								return;
							}
							alert("Ibyo mushaka ntibikunze mwongere mugerageze");
						}, "json");
				}
			}
		</script>
</body>

</html>