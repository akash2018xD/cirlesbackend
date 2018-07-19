<?php 
	
	include 'includes/bootstrap.php';


	if(isset($_GET['method']))
	{

			$response=platesUser::check_oauth_email_duplication($_GET['email'],$_GET['method']);
		print_r(json_encode($response));

	}
	else
	{



	}


?>