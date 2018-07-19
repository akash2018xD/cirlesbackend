<?php 

		
		class circlesCircle
		{

				public static function getMainCirclesInit()
				{
						$query = DB::getInstance()->query("SELECT * FROM circles ");

						$res=$query->results();

						foreach ($res as $key => $value) {
							$value->subCircles=circlesCircle::getSubCircles($value->id);
						}


				}
				public static function getSubCircles()
				{	

					$q=DB::getInstance()->query("SELECT * FROM sub_circle ");
					$results=$q->results();
					if($q->rowCount())
					{
					return $results;
					}
					else
					{
						$array=array();
						return $array;

					}

				}
				public static function joinCircle($id,$user_id)
				{
					DB::getInstance()->insert("activity_joined",array(

								'user_from'=>$user_id,
								'activity_id'=>$id

						));

				}

		}
?>