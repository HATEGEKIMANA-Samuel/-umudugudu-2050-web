<?php
require_once "autoload.php";

require_once "routes/queries/db_connection.php";
 session_start();
if (isset($_post['umutekano'])) {
        $umutekano = filter_var($_POST['umutekanoo'], FILTER_SANITIZE_STRING);
        $icyabaye = filter_var($_POST['icyabaye'], FILTER_SANITIZE_STRING);
        $gabo1 = filter_var($_POST['gabo1'], FILTER_SANITIZE_STRING);
        $gore1 = filter_var($_POST['gore1'], FILTER_SANITIZE_STRING);
        $gabo2 = filter_var($_POST['gabo2'], FILTER_SANITIZE_STRING);
        $gore2 = filter_var($_POST['gore2'], FILTER_SANITIZE_STRING);
        $comments = filter_var($_POST['comments'], FILTER_SANITIZE_STRING);
        $location=$_SESSION['location'];

        $sql = $connect->prepare("INSERT INTO `security` (`issue_id`, `icyabaye_id`, `uruhare_gabo`, `uruhare_gore`, `abahohotewe_gabo`, `abahohotewe_gore`, `location`, `comments`) 
        VALUES ('$umutekano', '$icyabaye', '$gabo1', '$gore1', '$gabo2', '$gore2', '$location', '$comments')");
        $sql->execute(array(':issue_id'=>$icyabaye, ':icyabaye_id'=>$icyabaye,':uruhare_gabo'=>$gabo1, ':uruhare_gore'=>$gore1, ':abahohotewe_gabo'=>$gabo2, ':abahohotewe_gore'=>$gore2, ':location'=>$location, ':comments'=>$comments));
        
        redirect::to('./reports');
}else{
    redirect::to('./add-securityphp');
}
?>