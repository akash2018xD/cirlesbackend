<?php 
	include 'includes/bootstrap.php';

	if($_GET['code']=="cjoin")
			{
					circleCircles::joinCircle($_GET["c_id"],$_GET['user_id']);

			}
			else
			{

					//Display
				$res=circlesCircle::getSubCircles();
				print_r($res);


			}

?>