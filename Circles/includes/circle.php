<?php 
	include 'includes/bootstrap.php';

	if($_GET['code']=="cjoin")
			{
					circleCircles::joinCircle($_GET["c_id"],$_GET['user_id']);

			}
			else if($_GET['code']=="cget")
			{

					$query=DB::getInstance()->query("SELECT GROUP_CONCAT(`user_from`) AS stuff FROM circle_join WHERE user_from = 18 ");
					$res=$query->results();
					$less=$res[0]->stuff;

					$qx=DB::getInstance()->query("SELECT  * FROM sub_circles WHERE id IN ($less)");
					$resz=$qx->results();
					print_r(json_encode($resz));
			}
			else
			{

					//Display
				$res=circlesCircle::getSubCircles();
				print_r($res);


			}

?>