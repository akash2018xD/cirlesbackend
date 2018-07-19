<?php 
		
		/*A Lists of general functions*/	


			function circles_authenticate_user($code)
			{

                                
                                                         if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    				$id=$_POST['userID'];
				}
				else
				{
					$id=$_GET['userID'];
				}

				$query=DB::getInstance()->query("SELECT id FROM users WHERE id =  ? AND security_token = ?",array($id,$code));
				if($query->rowCount())
				{

				}
				else
				{
					$array=array();
					print_r(json_encode($array));
					die();

				}
                                               
                       
			}
                        function utf8_urldecode($str) {
         $str=html_entity_decode(preg_replace("/%u([0-9a-f]{3,4})/i", "&#x\\1;", urldecode($str)), null, 'UTF-8');
         $str=stripslashes($str);
         return $str;
                                               }
			function putExtraArgs($results,$userID,$size="medium",$fromEditFalse=false,$plateID=0)
			{
					
					foreach ($results as $key => $value) {
		        

		                        $value->full=utf8_urldecode($value->full);
		                        $value->username=utf8_urldecode($value->username);
		                        if($value->pro_pic!="")
		                        {
		                        $dtr=str_replace(ROOT_SITE_COMPLETE,"",$value->pro_pic);
		                        $array=explode(".",$dtr);

		                        $img=$array[0]."_".$size.".".$array[1];
		                        $value->pro_pic=ROOT_SITE_COMPLETE.$img;
		                        }
		                        if($userID!=0)
		                       {
		                       $value->following=circlesUser::check_following($userID,$value->id);
		                       }
		                       if($fromEditFalse)
		                       {
		                       		$value->statusAdded=circlescircles::getPersonMemberStatus($value->id,$plateID);
		                       }
		                   }
    		return $results;

		    	}
		    	function putExtraPostArgs($posts,$userID)
		    	{
				    			foreach ($posts as $key => $value) {
				    				if($value->type==1)
				    						{
		   			$value->authorName=circlesUser::get_val("full",$value->user_id);
		   									}

                                        //   $value->uid=sizeShift($value->uid,"medium");
		                                  

		   			$value->authorPic=sizeShift(circlesUser::get_val("pro_pic",explode(",",$value->user_id)[0]),"thumb");
                            $value->date=friendlyTime($value->date);
		            $value->plateName=circlescircles::get_val("name",$value->circles_id);
                            $value->platePrivacy=circlescircles::get_val("privacy",$value->circles_id);
		             //  $value->comments=circlesPosts::getComments($value->id);
		             $value->owner=circlesPosts::isPostOwner($userID,$value->id);
		              $value->pojo=circlesPosts::isLiked($value->id,$userID);   
		              $value->likeCount=circlesPosts::likeCount($value->id);
		              $value->commentCount=circlesPosts::commentCount($value->id);
		               $value->following=circlesUser::check_following($userID,$value->user_id);
					               if($value->type==2||$value->type==3||$value->type==5)
					               {
					               	$members=circlescircles::getMembers($value->circles_id);
					               	$noSize=explode(",",$members);
					               	$noSize=array_filter($noSize);
					               	$noSize=count($noSize);
					               	$value->imageCount=circlescircles::imageCount($value->circles_id);
					               	$value->privacy=circlescircles::get_val("privacy",$value->circles_id);
					               	 	$value->about=circlescircles::get_val("about",$value->circles_id);
			                       	$value->about=utf8_urldecode($value->about);
					               
			                        $value->memberCount=$noSize;
				 					$value->followerCount=circlescircles::followerCount($value->circles_id);
				 					$value->members=$members;
				 					$value->circlesFollowing=circlescircles::check_following($userID,$value->circles_id);
				 		$value->authorName=circlesNotifications::thisAndthese(array_reverse(explode(",",$value->user_id))); // Iknow Stupid ... But ok
				 				$explode=explode(",",$value->type);
				 					$explode=array_unique($explode);
				 					$newType=implode(",",$explode);
				 					$value->type=$newType;
				 					if(count($explode)>=2)
				 					{
				 						//Can be own and mem , own and foll , mem and fol(Later)
				 						$value->type=4;
				 						$lastID=explode(",",$value->group_id);
				 						$value->lowerLimit=$lastID[0];
				 						$lastID=array_reverse($lastID);

				 						$value->id=$lastID[0];
				 						$memRet=deleteArr($value->user_id,$value->ref_id);
				 						$value->authorName=circlesUser::get_val("full",$value->ref_id);
				 					
				 						$value->memberName=circlesNotifications::thisAndthese(explode(",",$memRet));
				 					 
				 					}
				 				

					               }

   	                           	}
   	                           	return $posts;

		   
			}
			function deleteArr($arr,$del)
			{
				$retArr=array();
				$arr=explode(",",$arr);
				for ($i=0; $i < count($arr); $i++) { 
					if($arr[$i]==$del)
					{

					}
					else
					{
						$retArr[$i]=$arr[$i];
					}
				}
				$retArr=implode(",",$retArr);
				return $retArr;

			}
			 function sizeShift($url,$size)
			{
				if($url!="")
				{
				$str=str_replace(ROOT_SITE_COMPLETE,"",$url);
				 $array=explode(".",$str);
				
		         $img=$array[0]."_".$size.".".$array[1];
		         $img=ROOT_SITE_COMPLETE.$img;
		         return $img;
		     	}
		     	else
		     	{
		     		return "";
		     	}
				
			}
			function friendlyTime($ptime)
                        {
					                            $etime = time() - $ptime;
					    
					
					    if ($etime < 1)
					    {
					        return '0 seconds';
					    }
					
					    $a = array( 365 * 24 * 60 * 60  =>  'year',
					                 30 * 24 * 60 * 60  =>  'month',
					                      24 * 60 * 60  =>  'day',
					                           60 * 60  =>  'hour',
					                                60  =>  'minute',
					                                 1  =>  'second'
					                );
					    $a_plural = array( 'year'   => 'years',
					                       'month'  => 'months',
					                       'day'    => 'days',
					                       'hour'   => 'hours',
					                       'minute' => 'minutes',
					                       'second' => 'seconds'
					                );
					
					    foreach ($a as $secs => $str)
					    {
					        $d = $etime / $secs;
					        if ($d >= 1)
					        {
					            $r = round($d);
					            return $r . ' ' . ($r > 1 ? $a_plural[$str] : $str) . ' ago';
					        }
					    }
					                        
                        
                        
                        }


?>