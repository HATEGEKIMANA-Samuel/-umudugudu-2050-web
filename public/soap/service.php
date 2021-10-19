<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
	ID<input type="text" name="idnum" size="50" />
	<input type="submit" autocomplete="off" autofocus="on"  name="search" value="Search" />
	
</form>

<?php
if (isset($_POST['idnum']) && $_POST['idnum'] !=''){
include 'client.php';
$idNum =  array('idnum' => $_POST['idnum']);
$details =  $client->getName($idNum);
echo $details['Surnames']." ";
echo $details['ForeName']."<br /> ";

$srcPhoto = "data:image/png;base64, ".$details['Photo'];
$srcSignature = "data:image/png;base64, ".$details['Signature'];  
		  echo "<img src=\"$srcPhoto\" alt=\"Red dot\" /><br />";
		  echo "<img src=\"$srcSignature\" alt=\"Red dot\" />";
}elseif(isset($_POST['idnum'])){
 	echo "<p style='color:red;'>Please Enter ID<p>";
}
?>