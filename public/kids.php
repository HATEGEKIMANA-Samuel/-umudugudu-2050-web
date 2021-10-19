<?php
require_once("includes/validate_credentials.php");
$level = session::get("level");
if (isset($_POST['comment_field']) && !empty($_POST['comment_field'])) {
	$comment = htmlentities($database->escape_value($_POST['comment_field']));
	$sql = "INSERT INTO comments(owner,comment,owner_type,time,user) VALUES({$_POST['kids']},'$comment','kd',NOW(),{$_SESSION["id"]})";
	$database->query($sql);
	$sp = rawurlencode(encrypt_decrypt('encrypt', $_POST['kids']));
	header("location:kids?kd=$sp");
	exit();
}
if (isset($_GET['kd'])) {
	$kd_data = explode('%', $_GET['kd']);
	$found = count($kd_data);
	if ($found > 1) {
		$sp = encrypt_decrypt('decrypt', $kd_data[0]);
	} else {
		$sp = encrypt_decrypt('decrypt', $_GET['kd']);
	}
} else {
	header("location:404");
}
if (!isset($sp) || !is_numeric($sp)) {
	header("location:404");
} else {
	$query = $database->query("SELECT * FROM citizen WHERE citizenId ='$sp'  LIMIT 1 ");
	$found_spouse  = $database->num_rows($query);
	if ($found_spouse == 0) {
		header("location:404");
	} else {
		$spouse  = $database->fetch_array($query);
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
	<link rel="stylesheet" href="css/kids.css">
</head>

<body>
	<!-- Left Panel -->
	<?php require_once 'includes/left_nav.php'; ?>
	<!-- Right Panel -->
	<div id="right-panel" class="right-panel">
		<!-- Header-->
		<?php require_once 'includes/top_nav.php'; ?>
		<!-- Header-->
		<div class="content mt-3">
			<!-- displaypersoning institution basic info -->
			<div class="col-lg-12">
				<div style="display: inherit; padding-bottom: 30px;" class="card">
					<div class="card-header">
						<?php $father = $database->fetch("SELECT * from citizen where citizenId={$spouse['familyId']} limit 1")[0]; ?>
						<h4><?php echo $spouse['givenName'] . ' ' . $spouse['familyName'] . ' ' . $spouse['otherName']; ?>
							ubarizwa mumuryango wa
							<span class="text-primary"><?php
														if ($level != 2) {
															$father['documentNumber'] = "";
														}
														echo $father['documentNumber'] . ' ' .  $father['givenName'] . ' ' . $father['familyName'] . ' ' . $father['otherName']; ?></span>
						</h4>
					</div>
					<!-- tab content here  -->
					<?php
					if ($level != 2) {
						$spouse['documentNumber'] = "";
					}
					echo '<ul class="list-group">';

					//for the the rest
					echo "<li class='list-group-item info-list'><div class='labels'>Igitsina</div><div class='datas'>{$spouse['gender']}</div></li>";
					echo "<li class='list-group-item info-list'><div class='labels'>Itariki yamavuko</div><div class='datas'>{$spouse['dob']}</div></li>";
					echo "<li class='list-group-item info-list'><div class='labels'>Aho yavukiye</div><div class='datas'>{$spouse['birthplace']}</div></li>";
					$location["name"] = $spouse['birthNationality'];
					if (is_numeric($spouse['birthNationality'])) {
						$location  = $database->fetch_array($database->query("SELECT name FROM countries WHERE id ='{$spouse['birthNationality']}' LIMIT 1 "));
					}

					echo "<li class='list-group-item info-list'><div class='labels'>Birth nationality</div><div class='datas'>{$location['name']}</div></li>";
					echo "<li class='list-group-item info-list'><div class='labels'>ID/Pasiporo</div><div class='datas'>{$spouse['documentNumber']}</div></li>";
					$location["name"] = $spouse['issuedCountry'];
					if (is_numeric($spouse['issuedCountry'])) {
						if ($spouse['issuedCountry'] != 0) {
							$location  = $database->fetch_array($database->query("SELECT name FROM countries WHERE id ='{$spouse['issuedCountry']}' LIMIT 1 "));
						} else {
							$location["name"] = "";
						}
						//	$location  = $database->fetch_array($database->query("SELECT name FROM countries WHERE id ='{$spouse['issuedCountry']}' LIMIT 1 "));
					}
					echo "<li class='list-group-item info-list'><div class='labels'>Igihugu cyayitanze</div><div class='datas'>{$location['name']}</div></li>";
					echo "<li class='list-group-item info-list'><div class='labels'>Igihe yatangiwe</div><div class='datas'>{$spouse['issuedDate']}</div></li>";
					echo "<li class='list-group-item info-list'><div class='labels'>Igihe izarangirira</div><div class='datas'>{$spouse['expiryDate']}</div></li>";
					echo "<li class='list-group-item info-list'><div class='labels'>Emeli</div><div class='datas'>{$spouse['email']}</div></li>";
					echo "<li class='list-group-item info-list'><div class='labels'>telefone</div><div class='datas'>{$spouse['mobile']}</div></li>";
					$spouse['relationship'] = lang($spouse['familyCategory']);
					echo "<li class='list-group-item info-list'><div class='labels'>Isano </div><div class='datas'>{$spouse['familyCategory']}</div></li>";
					// new column
					echo "<li class='list-group-item info-list'><div class='labels'>Urwego ry'uburezi</div><div class='datas'>{$spouse['level_of_education']}</div></li>";

					echo "<li class='list-group-item info-list'><div class='labels'>Umwuga</div><div class='datas'>{$spouse['occupation']}</div></li>";
					// ubudehe
					echo "<li class='list-group-item info-list'><div class='labels'>Icyiciro cy'ubudehe</div><div class='datas'>{$spouse['ubudehe']}</div></li>";
					// end of giba column
					echo "<li class='list-group-item info-list'><div class='labels'>Igihe yanditswe</div><div class='datas'>{$spouse['created_at']}</div></li>";
					$user  = $database->fetch_array($database->query("SELECT fname,lname FROM user WHERE id ='{$spouse['created_by']}' LIMIT 1 "));
					$user_names = $user['fname'] . ' ' . $user['lname'];
					echo "<li class='list-group-item info-list last-child'><div class='labels'>Uwamwanditse</div><div class='datas'>$user_names</div></li>";
					echo '</ul>';

					$href = "add-member?kd=" . rawurlencode($kd_data[0]) . "&dpl=" . rawurlencode(encrypt_decrypt('encrypt', $spouse['familyId']));
					$back_href = "family?dpl=" . rawurlencode(encrypt_decrypt('encrypt', $spouse['familyId'])) . rawurlencode("%members");


					?>
					<!-- $back_href -->
					<a href="#" class='edit-link back navigate' onclick=" return back(this);"><button class='btn w-150 mb-10 border-radius-0  btn-primary btn-lg back_btn'>Garuka</button></a>
					<a href="<?= $href ?>" class='edit-link'><button class='btn w-150 mb-10 border-radius-0 btn-primary btn-lg edit_btn'>Hindura</button></a>
					<button type="button" class="btn text-white bg-dark w-150 border-radius-0  btn-lg m-t-1 navigate" onclick="openModalMovements('movement-list?dpl=<?= $_GET['kd'] ?>');" data-action="get_movements" data-type="MEMBER" data-id="<?= $spouse['citizenId'] ?>">Reba Aho yabaye</button>
					<!-- <button type="button" class="btn btn-mint w-150 border-radius-0  btn-lg m-t-1" onclick="changeFamilyHead(this);" data-action="changefamilyhead" data-head="<?= $spouse['familyId'] ?>" data-new_head="<?= $spouse['citizenId'] ?>">Kumugira Umukuru</button> -->
					<hr style="margin: 5px 2%;" />
					<h4 style="font-family: inherit;margin-left: 20px; padding-top: 20px ;margin-bottom: 20px;">Comments</h4>
					<div id="commentHolder">
						<!-- comment here -->
					</div>
					<button class="btn btn-primary w-150 border-radius-0 mb-20" id="comment_button" type="button" onclick="myFunction()" title='Write a comment'><i class="fa fa-comments-o fa-2x" aria-hidden="true"></i> Write a comment</button>
					<center>
						<form id="comments" style="display: none" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
							<textarea id="comment_field" class="form-control w-50p" rows="5" name="comment_field" required placeholder="Your comment"></textarea>
							<input type="hidden" name="owner" value="<?php echo $sp; ?>" />
							<input type="hidden" name="type" value="kd" />
							<input type="hidden" name="user" value="<?= $user_names ?>" />
							<input type="hidden" name="action" value="savecomment" />
							<input type="submit" value="SAVE" id="submit_comment" name="submit_comment" class="btn btn-success w-150 mt-10 btn-xs" />
						</form>

					</center>

					<!-- End of tab content  -->
				</div>
			</div>

		</div> <!-- .content -->
		<!-- modal used for showing people movement -->
		<div class="modal " tabindex="-1" role="dialog" id="modalMovement" data-backdrop="false">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title"> Kwimuka <b id="mtitle"></b></h5>
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
		<script>
			function back(e) {
				window.history.back()
				// console.log(e);
				// e.preventDefault();
				//window.history.go(-1);
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
			// load comment
			let owner = "<?= $sp ?>";
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
			$('.datepicker').datepicker({
				endDate: '0d',
				format: 'yyyy-mm-dd'
			});

			function loadComments(o) {
				$("#commentHolder").html("<h1>tegereza comment ...</h1>");
				$.get(`ajax/getComments?owner=${o}&type=kd`, function(data) {
					$("#loader").remove();
					$("#commentHolder").html(data);
				});
			}

			function openModalMovements(url) {
				window.location.href = url;
				return;
			}

			function changeFamilyHead(e) {
				if (confirm("Uremeza iki gikorwa")) {
					var formData = new FormData();
					formData.set("action", $(e).attr("data-action"));
					formData.set("head", $(e).attr("data-head"));
					formData.set("new_head", $(e).attr("data-new_head"));
					post(formData, $(e), ".contentHolder");
					alert("Umukuru w'umuryango Yahindutse");
					window.location.reload();
				}
			}

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