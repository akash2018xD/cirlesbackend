<?php 
 
 include 'includes/bootstrap.php';



 $resp=platesUser::register($_GET);

 if($resp['state'])
 {
 		print_r(json_encode($resp));

 }
 else
 {
 		print_r(json_encode($resp));


 }
?>