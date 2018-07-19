<?php 

		/*The Wrapprer Handles Getting and Creation of posts*/

		class circlesPosts
			{
				  public static function upload_image($post)
				  {
				  	    $path=circlesPhotos::upload_circles_singleton($post['image_file']);
                       	$body=addslashes($post['caption']);
								$body=strip_tags($body);
                        $query=DB::getInstance()->insert("circles_uploads",array(

				  				'circles_id'=>$post['plate_id'],
				  				'caption'=>$body,
				  				'user_id'=>$post['user_id'],
				  				'uid'=>$path,
				  				'date'=>time()

				  			));
                       echo $path;
                       print_r($post);

				  		

				  }
                                                                                
			public static function mastercirclesList($userID)
				  {
				  	$circlesFollowing=circlescircles::getFollowcircles($userID);
				  	$circlesOwned=circlescircles::getOwnedcircles($userID);
				  	
				  	$circlesConts=circlescircles::getContribcircles($userID);
				  	
				  	
				  	$circlesFollowingArray=explode(",",$circlesFollowing);
				  	$circlesOwnedArray=explode(",",$circlesOwned);
				  	$circlesContsArray=explode(",",$circlesConts);

				  	
                    $twoM=array_merge($circlesFollowingArray,$circlesOwnedArray);
                    $masterArray=array_merge($twoM,$circlesContsArray);
                    $tempArray=$masterArray;
					$masterArray=array_unique($masterArray);				  	
					$masterArray=array_filter($masterArray);
					$masterList=implode(",",$masterArray);
					return $masterList;
				  }
				  public static function informEveryone($bundle)
				  {

				  		$members=circlescircles::getMembers($bundle['plate_id']);
				  		$ownerWith=circlescircles::get_val("owner",$bundle['plate_id']).",".$members;
				  		$queru1=DB::getInstance()->query("SELECT GROUP_CONCAT(`user_from`) AS cnt FROM circles_follow WHERE plateID  = ? ORDER BY id DESC ",array($bundle['plate_id']));
				  		$res=$queru1->results();
				  		$list=$res[0]->cnt;
				  		$ownerWithFoll=$ownerWith.",".$list;
				  		$ownerWithFoll=explode(",",$ownerWithFoll);
				  		$ownerWithFoll=array_unique($ownerWithFoll);
				  		$ownerWithFoll=array_filter($ownerWithFoll);
				  		$ownerAll=array();
				  		$ctr=0;
				  		//Sort keys
				  		foreach ($ownerWithFoll as $key => $value) 
				  		{
				  		
				
			  			if($value)
				  			{
				  				$ownerAll[$ctr]=$value;
				  				$ctr++;
				  			}
				  		}
				  		$ownerWithFoll=$ownerAll;
				  			
				  			$data=array();
							$data['hasPosts']=false;
							$data['message']="pickupNots";
                                      error_reporting(E_ALL);
                                                           print_r($ownerWithFoll);
				  		for($i=0;$i<count($ownerWithFoll);$i++)
				  		{      

                                                             echo $bundle['userID']."--".$ownerWithFoll[$i]."<br>INDIA";
                              

				  				                                       
				  				if($bundle['userID']!=$ownerWithFoll[$i] && $ownerWithFoll[$i])
				  	{
											$notArray=array('user_from'=>$bundle['userID'],'user_to'=>$ownerWithFoll[$i],'type'=>circlesNotifications::type_newPlatePosts,'read_status'=>0,'ref_id'=>$bundle['plate_id'],"desc_id"=>0);
							                                  print_r($notArray);
											circlesNotifications::buildNotification($notArray);
										$firebase_token=circlesUser::get_val("firebase_token",$ownerWithFoll[$i]);
										if($firebase_token!="null" || $firebase_token!="" || $firebase_token!="alias")
										{
											circlesNotifications::sendFCM($firebase_token,$data);
											//Simple Liitle Push That Come pick up and deploy the notifications
										}
								}

                                                                                                                  
				  		}


				  }
					public static function getPhotosNots($plateID,$userID)
					{
						$lim_id=circlesUser::get_val("lim_id",$userID);
                          $posts=DB::getInstance()->query("SELECT COUNT(id) AS cnt FROM circles_uploads WHERE (circles_id = ? AND id > $lim_id) AND type = 1",array($plateID));
                          $results=$posts->results();
                         
                          $idS=$results[0]->cnt;
                          return $idS;

					}
					  public static function getPosts($circlesID)
				  {

				  		$query=DB::getInstance()->query("SELECT * FROM circles_uploads WHERE circles_id = ? AND type=1 ORDER BY id DESC LIMIT 5",array($circlesID));
				  		if($query->rowCount())
				  		{
				  			$results=$query->results();
				  			return $results;
				  		}
				  		else
				  		{
				  		  return -1;
				  		}
				  }
				  public static function getLatestPosts($lastID,$plateID)
				  {

				  	$query=DB::getInstance()->query("SELECT * FROM circles_uploads WHERE (circles_id = ?  AND id  > ?) AND type=1 ORDER BY id DESC",array($plateID,$lastID));
				  	$results=$query->results();
				  	return $results;

				  }
				  public static function getIndex($personStuff,$masterList)
				  {
				  		
					$query=DB::getInstance()->query("SELECT id FROM `circles_uploads` WHERE type IN (2,3) AND user_id IN ($personStuff)  GROUP BY circles_id 
					 UNION ALL
					  SELECT id FROM circles_uploads WHERE  (circles_id IN ($masterList) AND type=1) or (user_id IN($personStuff) AND type=5 ) 

					  ORDER BY id DESC LIMIT 5");
                                                                                                                                            
				  		$results=$query->results();
				  		$results=array_reverse($results);
				  		foreach ($results as $z => $value) {
				  			return $value->id;//first id
				  		}
				  }
                                                                                                              
                                       public static function getIndexLower($personStuff,$masterList,$lim)
				  {
				  	

                                                                              $query=DB::getInstance()->query("SELECT id FROM `circles_uploads` WHERE ( (type= 2 || type = 3) AND (user_id IN ($personStuff)) ) AND (id < $lim ) GROUP BY circles_id
					 UNION ALL
					  SELECT id FROM circles_uploads WHERE (  (circles_id IN ($masterList) AND type=1) or (user_id IN($personStuff) AND type=5 ) ) AND (id < $lim ) 
					  ORDER BY id DESC LIMIT 5");
				
				
				
                                                                                      
				  		$results=$query->results();
				  		$results=array_reverse($results);
				  		foreach ($results as $z => $value) {
				  			return $value->id;//first id
				  		}
				  }                         
				  public static function getGeneralPosts($userID)
				  {
				  	$circlesFollowing=circlescircles::getFollowcircles($userID);
				  	$circlesOwned=circlescircles::getOwnedcircles($userID);
				  	
				  	$circlesConts=circlescircles::getContribcircles($userID);
				  	
				  	
				  	$circlesFollowingArray=explode(",",$circlesFollowing);
				  	$circlesOwnedArray=explode(",",$circlesOwned);
				  	$circlesContsArray=explode(",",$circlesConts);

				  	
                    $twoM=array_merge($circlesFollowingArray,$circlesOwnedArray);
                    $masterArray=array_merge($twoM,$circlesContsArray);
                    $tempArray=$masterArray;
					$masterArray=array_unique($masterArray);				  	
					$masterArray=array_filter($masterArray);
					$masterList=implode(",",$masterArray);

					$personStuff=circlesUser::getFollowers($userID).",".circlescircles::getOwnersPls($masterArray);
					$personStuff=explode(",",$personStuff);
					$personStuff=array_filter($personStuff);
					$personStuff=array_unique($personStuff);
					$personStuff=implode(",", $personStuff);
                    

					if($masterList=='')
					{
						$masterList="''";
					}
					if($personStuff=='')
					{
						$personStuff="''";
					}
					if($masterList==''||$personStuff=='')
						{
							return -1;
						}
						
						$key=circlesPosts::getIndex($personStuff,$masterList);
                                                           
                                                           
					$query=DB::getInstance()->query("SELECT max(id) AS id,uid,caption,circles_id,GROUP_CONCAT(`user_id`) AS user_id,max(date) AS date,GROUP_CONCAT(`type`) AS type ,ref_id,GROUP_CONCAT(`id`) AS group_id FROM `circles_uploads` WHERE type IN (2,3) AND user_id IN ($personStuff) AND id >= $key  GROUP BY circles_id 
					 UNION ALL
					  SELECT * FROM ( SELECT * FROM circles_uploads ORDER BY id DESC ) AS circles_uploads WHERE  ((circles_id IN ($masterList) AND type=1) or (user_id IN($personStuff) AND type=5 )) AND id > $key 

					  GROUP BY circles_id ORDER BY id DESC LIMIT 5");




					
                                       
                                              
					if($query->rowCount() && !array_key_exists("plate_ids",$query->results()[0]))
				  	{

				  	return $query->results();
				   	}
				   	else
				   	{
				   		return -1;
				   	}
				  }
				  public static function getOldPosts($lastID,$plateID)
				  {

				  	$query=DB::getInstance()->query("SELECT * FROM circles_uploads WHERE (circles_id = ?  AND id  < ?) AND type=1 ORDER BY id DESC LIMIT 4",array($plateID,$lastID));
				  	$results=$query->results();
				  	return $results;


				  }
					

				  public static function getNewPostGen($userID,$lim)
				  {
				  	$circlesFollowing=circlescircles::getFollowcircles($userID);
				  	$circlesOwned=circlescircles::getOwnedcircles($userID);
				  	$circlesConts=circlescircles::getContribcircles($userID);
				  	
				  	$circlesFollowingArray=explode(",",$circlesFollowing);
				  	$circlesOwnedArray=explode(",",$circlesOwned);
				  	$circlesContsArray=explode(",",$circlesConts);

				  	
                    $twoM=array_merge($circlesFollowingArray,$circlesOwnedArray);
                    $masterArray=array_merge($twoM,$circlesContsArray);
					$masterArray=array_unique($masterArray);	
					$masterArray=array_filter($masterArray);	
					$masterList=implode(",",$masterArray);


				  	$personStuff=circlesUser::getFollowers($userID).",".circlescircles::getOwnersPls($masterArray);
					$personStuff=explode(",",$personStuff);
					$personStuff=array_filter($personStuff);
					$personStuff=array_unique($personStuff);
					$personStuff=implode(",", $personStuff);
                    

					if($masterList=='')
					{
						$masterList="''";
					}
					if($personStuff=='')
					{
						$personStuff="''";
					}
					if($masterList==''||$personStuff=='')
						{
							return -1;
						}

					$query=DB::getInstance()->query("(SELECT  max(id) AS id,uid,caption,circles_id,GROUP_CONCAT(`user_id`) AS user_id,max(date) AS date,GROUP_CONCAT(`type`) AS type ,ref_id,GROUP_CONCAT(`id`) AS group_id FROM `circles_uploads` WHERE ((type= 2 || type = 3 ) AND user_id IN ($personStuff) ) AND id > $lim GROUP BY circles_id )
					 UNION ALL
					  (SELECT * FROM circles_uploads WHERE ( (circles_id IN ($masterList) AND type=1 ) or (user_id IN($personStuff) AND type=5 ) ) AND id > $lim ) 
                                              ORDER BY id ASC
					   ");
     
					/*Weird Reason  Maybe reversed in JAVA ... No Time Couldnt Find It ORDER BY id DESC LIMIT 10*/

				  
				  	return $query->results();
				  
				  }	
				  public static function getOldPostsGeneral($userID,$lim)			  
				  {

				  		$circlesFollowing=circlescircles::getFollowcircles($userID);
				  	$circlesOwned=circlescircles::getOwnedcircles($userID);
				  	$circlesConts=circlescircles::getContribcircles($userID);
				  	
				  	$circlesFollowingArray=explode(",",$circlesFollowing);
				  	$circlesOwnedArray=explode(",",$circlesOwned);
				  	$circlesContsArray=explode(",",$circlesConts);

				  	
                    $twoM=array_merge($circlesFollowingArray,$circlesOwnedArray);
                    $masterArray=array_merge($twoM,$circlesContsArray);
					$masterArray=array_unique($masterArray);	
					$masterArray=array_filter($masterArray);	
					$masterList=implode(",",$masterArray);
					

					$personStuff=circlesUser::getFollowers($userID).",".circlescircles::getOwnersPls($masterArray);
					$personStuff=explode(",",$personStuff);
					$personStuff=array_filter($personStuff);
					$personStuff=array_unique($personStuff);
					$personStuff=implode(",", $personStuff);
                    

					if($masterList=='')
					{
						$masterList="''";
					}
					if($personStuff=='')
					{
						$personStuff="''";
					}
					if($masterList==''||$personStuff=='')
						{
							return -1;
						}

					$index=circlesPosts::getIndexLower($personStuff,$masterList,$lim);

					

					$query=DB::getInstance()->query("(SELECT max(id) AS id,uid,caption,circles_id,GROUP_CONCAT(`user_id`) AS user_id,max(date) AS date,GROUP_CONCAT(`type`) AS type ,ref_id,GROUP_CONCAT(`id`) AS group_id FROM `circles_uploads` WHERE ((type= 2 || type = 3) AND user_id IN ($personStuff) ) AND (id >= $index AND id < $lim) GROUP BY circles_id )
					 UNION ALL
					  (SELECT * FROM circles_uploads WHERE (  (circles_id IN ($masterList) AND type=1) or (user_id IN($personStuff) AND type=5 ) ) AND id < $lim ) 

					  ORDER BY id DESC LIMIT 5");

					
				  	
				  	return $query->results();
				   

				  }
				  public static function getLikedPosts($userID)
				  {
				  		$masterPosts=array();
				  		$likePeople="";
				  		$likeQ=DB::getInstance()->query("SELECT id,postID FROM likes WHERE userID = ? ORDER BY id DESC LIMIT 8",array($userID));
				  		$likePeople=$likeQ->results();
				  		foreach ($likePeople as $key => $value) {
				  			 

				  			
				  			$qU=DB::getInstance()->query("SELECT * FROM circles_uploads WHERE id = ?",array($value->postID));
				  			$rzS=$qU->results();
				  			foreach ($rzS as $keys => $valuez) {
				  					$valuez->likeID=$value->id;
				  				}
				  				
				  			$masterPosts[$key]=$rzS[0];
				  			
				  			}

				  		return $masterPosts;	
				  
				  }
				  public static function getLikedPostsLoadMore($userID,$lim)
				  {
				  		$masterPosts=array();
				  		$likePeople="";
				  		$likeQ=DB::getInstance()->query("SELECT id,postID FROM likes WHERE userID = ? AND id < ? ORDER BY id DESC LIMIT 3",array($userID,$lim));
				  		$likePeople=$likeQ->results();
				  		foreach ($likePeople as $key => $value) {
				  			 

				  			$qU=DB::getInstance()->query("SELECT * FROM circles_uploads WHERE id = ?",array($value->postID));
				  			$rzS=$qU->results();
				  			foreach ($rzS as $keys => $valuez) {
				  					$valuez->likeID=$value->id;
				  				}


				  					$masterPosts[$key]=$rzS[0];
				  			
				  			}

				  		return $masterPosts;	
				  
				  }

				  public static function get_val($field,$plateID)
				  {
				  		//echo $field."-".$plateID;
				  		$querys=DB::getInstance()->query("SELECT {$field} FROM circles_uploads WHERE id  = ?",array($plateID));
				  		$res=$querys->results();
				  		//print_r($res);
				  		return $res[0]->$field;

				  }
				   public static function deletePost($postID,$userID)
				  {
				  	if(circlesPosts::isPostOwner($userID,$postID))
				  	{
				  	DB::getInstance()->query("DELETE FROM circles_uploads WHERE id  = ?",array($postID));
				  	DB::getInstance()->query("DELETE FROM likes WHERE postID = ?",array($postID));
				  		
				  	DB::getInstance()->query("DELETE FROM notifications WHERE ref_id = ? AND (type=? OR type = ?)",array($postID,circlesNotifications::type_Comment,circlesNotifications::type_Like));
				  	DB::getInstance()->query("DELETE FROM comments WHERE postID = ?",array($postID));
				  	}
				  	else
				  	{
				  		return ;
				  	}
				  }
				  public static function commentPosts($postArray)
				  {
				  		$body=addslashes($postArray['body']);
				  		$body=str_replace("%23","#",$body);
				  		$body=strip_tags($body);
				  		$time=time();
				  		$body=str_replace("-"," ",$body);


				  			$query=DB::getInstance()->insert("comments",array(


				  						'userID'=>$postArray['userID'],
				  						'postID'=>$postArray['postID'],
				  						'body'=>$body,
				  						'time'=>$time


				  				));
				  			$com_id=DB::getInstance()->lastID();
				  			$array=array('id'=>$com_id,'time'=>friendlyTime(time()));
				  			$notArray=array('user_from'=>$postArray['userID'],'user_to'=>circlesPosts::get_val("user_id",$postArray['postID']),'type'=>circlesNotifications::type_Comment,'read_status'=>0,'ref_id'=>$postArray['postID'],"desc_id"=>$com_id);
				  			circlesNotifications::buildNotification($notArray);
				  	
				  			return $array;
				  }
				  public static function getCommentsLim10($postID,$ids)
				  {
				  		$query=DB::getInstance()->query("SELECT * FROM comments WHERE postID = ?  AND id > ? ORDER BY id DESC",array($postID,$ids));
				  		$res=$query->results();
				  		return $res;
				  }
				  public static function getCommentsLoadMore($postID,$ids)
				  {
				  	$query=DB::getInstance()->query("SELECT * FROM comments WHERE postID = ?  AND id < ? ORDER BY id DESC",array($postID,$ids));
				  		$res=$query->results();
				  		return $res;	
				  }
				  public static function removeComment($commentID)
				  {
				  	   DB::getInstance()->query("DELETE FROM notifications WHERE desc_id = ?",array($commentID));
				  		DB::getInstance()->query("DELETE FROM comments WHERE id  = ?",array($commentID));
				  }
				  public static function getComments($postID,$lim)
				  {
				  		if($lim)
				  		{
				  			$query=DB::getInstance()->query("SELECT * FROM comments WHERE postID =  ? ORDER BY id DESC LIMIT 4" ,array($postID));
				  		
				  		}
				  		else
				  		{
				  		$query=DB::getInstance()->query("SELECT * FROM comments WHERE postID =  ? ORDER BY id DESC LIMIT 10" ,array($postID));
				  		}
				  		$res=$query->results();
				  		return $res;
				  }
				  public static function isLiked($postID,$userID)
				  {

				  		$query=DB::getInstance()->query("SELECT id FROM likes WHERE postID = ?  AND userID = ?",array($postID,$userID));
				  		if($query->rowCount())
				  		{

				  			return true;
				  		}
				  		else
				  		{
				  			return false;
				  		}
				  }
				  public static function removeLike($postID,$userID)
				  {
				  		circlesNotifications::removeNotificationFrom($userID,$postID,circlesNotifications::type_Like);
				  		DB::getInstance()->query("DELETE FROM likes WHERE userID = ? AND postID = ?",array($userID,$postID));
				  }
				  public static function likePost($postID,$userID)
				  {
				  	$notArray=array('user_from'=>$userID,'user_to'=>circlesPosts::get_val("user_id",$postID),'type'=>circlesNotifications::type_Like,'read_status'=>0,'ref_id'=>$postID);
				  	circlesNotifications::buildNotification($notArray);
				  	
				  	DB::getInstance()->insert("likes",array(

				  			'postID'=>$postID,
				  			'userID'=>$userID
				  		));
				  }
				  public static function likeOperation($post)
				  {
				  	$token="";
				  	if(circlesPosts::isLiked($post['postID'],$post['userID']))
				  	{
				  			//Remove Like
				  		circlesPosts::removeLike($post['postID'],$post['userID']);
				  		$token="remLike";
				  	}
				  	else
				  	{		

				  		$token="likeP";
				  		circlesPosts::likePost($post['postID'],$post['userID']);
				  			//Like Post
				  	}	
				  	return $token;
				  }
				  public static function getImagescirclesFeed($circlesID)
				  {
				  		$query=DB::getInstance()->query("SELECT uid FROM circles_uploads WHERE circles_id = ? ORDER BY id DESC LIMIT 4",array($circlesID));
				  		$results=$query->results();
				  		return $results;

				  }
				  public static function getUserFeedImage($userID)
				  {
				  		$listName=circlescircles::getOwnedcircles($userID,true);
				  		$query=DB::getInstance()->query("SELECT id,name FROM circles WHERE id IN ($listName)");
				  		$results=$query->results();
				  		return $results;

				  		
				  }
				  public static function isPostOwner($userID,$postID)
				  {

				  	$query=DB::getInstance()->query("SELECT id FROM circles_uploads WHERE user_id = ? AND id = ?",array($userID,$postID));
				  	if($query->rowCount())
				  		{

				  				return true;
				  		}
				  		else
				  		{
				  			return false;
				  		}
				  }
				  public static function likeCount($postID)
				  {
				  	$query=DB::getInstance()->query("SELECT COUNT(`id`) AS  cnt FROM likes WHERE postID =  ?",array($postID));
				  	$results=$query->results();
				  	return $results[0]->cnt;
				  }

				  public static function commentCount($postID)
				  {
				  	$query=DB::getInstance()->query("SELECT COUNT(`id`) AS cnt FROM comments WHERE postID =  ?",array($postID));
				  	$results=$query->results();
				  	return $results[0]->cnt;
				  }

				  public static function getLikers($postID)
				  {
				  	$first=DB::getInstance()->query("SELECT id,userID FROM likes WHERE postID = ? ORDER BY id DESC LIMIT 20",array($postID));
				  	$results=$first->results();
				  	return $results;
				  	
				  }
				   public static function getLikersSwipeToRef($postID,$lim)
				  {
				  	$first=DB::getInstance()->query("SELECT id,userID FROM likes WHERE postID = ? AND id > ? ORDER BY id DESC LIMIT 20",array($postID,$lim));
				  	$results=$first->results();
				  	return $results;
				  }
				  public static function getLikersLoadMore($postID,$lim)
				  {
				  	$first=DB::getInstance()->query("SELECT id,userID FROM likes WHERE postID = ? AND id < ? ORDER BY id DESC LIMIT 20",array($postID,$lim));
				  	$results=$first->results();
				  	return $results;	
				  }
				 
				  public static function getCommentersLoadMore($postID,$lim)
				  {
				  	$query=DB::getInstance()->query("SELECT * FROM comments WHERE postID =  ? AND id < ? ORDER BY id DESC LIMIT 4" ,array($postID,$lim));
				  		$res=$query->results();
				  		return $res;	
				  }

				  /*Grid Layout Back end */

				  public static function getGridLayoutcirclesSingleton($plateID)
				  {
				  	$query=DB::getInstance()->query("SELECT id,uid FROM circles_uploads WHERE circles_id = ? AND type=1 ORDER BY id DESC LIMIT 15",array($plateID));
				  	
				  	$results=$query->results();
				  	foreach ($results as $result) {
				  		$result->uid=sizeShift($result->uid,"medium");

				  	}
				  	return $results;

				  }
				   public static function getGPSLoadMore($lim,$plateID)
				  {
				  		$query=DB::getInstance()->query("SELECT id,uid FROM circles_uploads WHERE (circles_id = ? AND id > $lim) AND type=1 ORDER BY id DESC LIMIT 3",array($plateID));
				  	
				  	$results=$query->results();
				  	foreach ($results as $result) {
				  		$result->uid=sizeShift($result->uid,"medium");

				  	}
				  	return $results;

				  }
				  public static function getGPSLoadMoreScroll($lim,$plateID)
				  {
				  		$query=DB::getInstance()->query("SELECT id,uid FROM circles_uploads WHERE (circles_id = ? AND id < $lim )AND type=1  ORDER BY id DESC LIMIT 9",array($plateID));
				  	
				  	$results=$query->results();
				  	foreach ($results as $result) {
				  		$result->uid=sizeShift($result->uid,"medium");

				  	}
				  	return $results;

				  }
				   public static function getImagesPlateLIM6($plateID,$userID)
				  {
				  	$query=DB::getInstance()->query("SELECT id,uid FROM circles_uploads WHERE (circles_id = ? AND user_id = ?) AND type=1 ORDER BY id DESC LIMIT 9",array($plateID,$userID));
				  	if($query->rowCount())
				  	{
						  	$results=$query->results();
						  	foreach ($results as $result) {
						  		$result->uid=sizeShift($result->uid,"medium");

						  	}
						  	return $results;
					}
					else
					{
						return -1;
					}

				  }
				  public static function getUserFollowerCount($userID)
				  {
				  	$query=DB::getInstance()->query("SELECT id FROM relationship WHERE user_to = ? AND type = 'follow'",array($userID));
				  	$count=$query->rowCount();
				  	return $count;

				  }
				    public static function getUserFollowingCount($userID)
				  {
				  	$query=DB::getInstance()->query("SELECT id FROM relationship WHERE user_from = ? AND type = 'follow'",array($userID));
				  	$count=$query->rowCount();
				  	return $count;

				  }
				  public static function getUsercirclesCount($userID)
				  {
				  	$query=DB::getInstance()->query("SELECT id FROM circles WHERE owner = ?",array($userID));
				  	$count=$query->rowCount();
				  	return $count;
				  }

				  public static function getProfileStats($userID)
				  {
				  	 $array=array();
				  	 $array['plateCount']=circlesPosts::getUserFollowingCount($userID);//Name Problem But Fine
				  	 $array['followerCount']=circlesPosts::getUserFollowerCount($userID);
				  	 $array['username']=circlesUser::get_val("username",$userID);
				  	 $array['url']=circlesUser::get_val("url",$userID);
				  	 $array['about']=circlesUser::get_val("about",$userID);
				  	 return $array;
				 		 		
				  }

				  public static function getGridLayoutProfileSingleton($userID,$token)
				  {

				  	/**
					* This is will be populated like this , there are going to be sections (OWN + MEMBER )
					* Only those circles will be shown where some image has been contributed 
					* OWN -> circles only images posted by the profile Person will be shown
					* MEMBER -> All Images of profile person in his / her respective circles will be shown
					* NAME {PLATE } IMAGES (Max 6)
					* First get all circles in the category and in results all images , if now image found , remove that plate from the results arrat
				  	*/
				  	if($token=="self")
				  	{
				  		$finalRes=array();
				  		$query=DB::getInstance()->query("SELECT id,name,display_pic FROM circles WHERE owner = ? ORDER BY id DESC LIMIT 4 ",array($userID));
				  		$results=$query->results();
				  		foreach ($results as $key => $value) {
				  			
				  			$res=circlesPosts::getImagesPlateLIM6($value->id,$userID);

				  			if($res!=-1)
				  			{
				  				$value->images=$res;
				  				$finalRes[$key]=$value;
				  			}
				  			else
				  			{
				  				$finalRes[$key]=$value;
				  										  			
				  					//Delete No Photo circles from the above array
				  		    }
				  		    $value->display_pic=sizeShift($value->display_pic,"thumb");
				  			

				  		}
				  		return $finalRes;
				  	}
				  	else
				  	{
						$finalRes=array();
				  		$query=DB::getInstance()->query("SELECT id,circles_id FROM circles_members WHERE member_id = ? AND status =1   ORDER BY id DESC LIMIT 4 ",array($userID));
				  		$results=$query->results();
				  		foreach ($results as $key => $value) {
				  			
				  			$res=circlesPosts::getImagesPlateLIM6($value->circles_id,$userID);
				  			$value->name=circlescircles::get_val("name",$value->circles_id);
				  			$value->images=circlesPosts::getImagesPlateLIM6($value->circles_id,$userID);
				  			$value->display_pic=sizeShift(circlescircles::get_val("display_pic",$value->circles_id),"thumb");

				  			

				  		}
				  		return $results;				  	
				  	}
				  }
				   public static function getGridLayoutProfileSingletonScrollUp($userID,$token,$lim)
				  {
				  	if($token=="self")
				  	{
				  		$finalRes=array();
				  		$query=DB::getInstance()->query("SELECT id,name,display_pic FROM circles WHERE owner = ? AND id > $lim ORDER BY id DESC  ",array($userID));
				  		$results=$query->results();
				  		foreach ($results as $key => $value) {
				  			
				  			$res=circlesPosts::getImagesPlateLIM6($value->id,$userID);
				  			if($res!=-1)
				  			{
				  				$value->images=$res;
				  				$finalRes[$key]=$value;
				  			}
				  			else
				  			{
				  				$finalRes[$key]=$value;				  										  			
				  		    }
				  		    $value->display_pic=sizeShift($value->display_pic,"thumb");
				  		}
				  		return $finalRes;
				  	}
				  	else
				  	{
						$finalRes=array();
				  		$query=DB::getInstance()->query("SELECT id,circles_id FROM circles_members WHERE (member_id = ? AND id > $lim) AND status = 1 ORDER BY id DESC ",array($userID));
				  		$results=$query->results();
				  		foreach ($results as $key => $value) {
				  			
				  			$res=circlesPosts::getImagesPlateLIM6($value->circles_id,$userID);
				  			$value->name=circlescircles::get_val("name",$value->circles_id);
				  			$value->images=circlesPosts::getImagesPlateLIM6($value->circles_id,$userID);
				  			$value->display_pic=sizeShift(circlescircles::get_val("display_pic",$value->circles_id),"thumb");

				  		}
				  		return $results;				  	
				  	}
				  }
				     public static function getGridLayoutProfileSingletonScrollDown($userID,$token,$lim)
				  {
				  	if($token=="self")
				  	{
				  		$finalRes=array();
				  		$query=DB::getInstance()->query("SELECT id,name,display_pic FROM circles WHERE owner = ? AND id < $lim ORDER BY id DESC  LIMIT 3",array($userID));
				  		$results=$query->results();
				  		foreach ($results as $key => $value) {
				  			
				  			$res=circlesPosts::getImagesPlateLIM6($value->id,$userID);
				  			if($res!=-1)
				  			{
				  				$value->images=$res;
				  				$finalRes[$key]=$value;
				  			}
				  			else
				  			{
				  				$finalRes[$key]=$value;				  										  			
				  		    }
				  		    $value->display_pic=sizeShift($value->display_pic,"thumb");

				  		}
				  		return $finalRes;
				  	}
				  	else
				  	{
						$finalRes=array();
				  		$query=DB::getInstance()->query("SELECT id,circles_id FROM circles_members WHERE (member_id = ? AND id < $lim) AND status = 1 ORDER BY id DESC LIMIT 3",array($userID));
				  		$results=$query->results();
				  		foreach ($results as $key => $value) {
				  			
				  			$res=circlesPosts::getImagesPlateLIM6($value->circles_id,$userID);
				  			$value->name=circlescircles::get_val("name",$value->circles_id);
				  			$value->images=circlesPosts::getImagesPlateLIM6($value->circles_id,$userID);
				  			$value->display_pic=sizeShift(circlescircles::get_val("display_pic",$value->circles_id),"thumb");


				  		}
				  		return $results;				  	
				  	}
				  }

				  public static function circlesSinglePost($id)
				  {

				  	$query=DB::getInstance()->query("SELECT * FROM circles_uploads WHERE id  = ?",array($id));
				  	
				  	$results=$query->results()[0];
				  	return $results;

				  }

				  public static function circlesGetLatestThree($circles_id,$id)
				  {

				  	$query=DB::getInstance()->query("SELECT * FROM `circles_uploads` WHERE circles_id = ? AND id < $id ORDER  BY id DESC LIMIT 3 ",array($circles_id));
				  	$results=$query->results();
				  	$results=putExtraPostArgs($results,$_GET['userID']);  
				  	return $results;			

				  }

				  /*The Three new classes for post fetch*/

				  public static function getGeneralPostsNew($userID)
				  {
					  	$circlesFollowing=circlescircles::getFollowcircles($userID);
					  	$circlesOwned=circlescircles::getOwnedcircles($userID);
					  	
					  	$circlesConts=circlescircles::getContribcircles($userID);
					  	
					  	
					  	$circlesFollowingArray=explode(",",$circlesFollowing);
					  	$circlesOwnedArray=explode(",",$circlesOwned);
					  	$circlesContsArray=explode(",",$circlesConts);

					  	
	                    $twoM=array_merge($circlesFollowingArray,$circlesOwnedArray);
	                    $masterArray=array_merge($twoM,$circlesContsArray);
	                    $tempArray=$masterArray;
						$masterArray=array_unique($masterArray);				  	
						$masterArray=array_filter($masterArray);
						$masterList=implode(",",$masterArray);

						$personStuff=circlesUser::getFollowers($userID).",".circlescircles::getOwnersPls($masterArray);
						$personStuff=explode(",",$personStuff);
						$personStuff=array_filter($personStuff);
						$personStuff=array_unique($personStuff);
						$personStuff=implode(",", $personStuff);
	                    

						if($masterList=='')
						{
							$masterList="''";
						}
						if($personStuff=='')
						{
							$personStuff="''";
						}
						if($masterList==''||$personStuff=='')
							{
								return -1;
							}

							$key=circlesPosts::getIndex($personStuff,$masterList);
	                                                                                                                         
						$query=DB::getInstance()->query("SELECT max(id) AS id,uid,caption,circles_id,GROUP_CONCAT(`user_id`) AS user_id,max(date) AS date,GROUP_CONCAT(`type`) AS type ,ref_id,GROUP_CONCAT(`id`) AS group_id FROM `circles_uploads` WHERE type IN (2,3) AND user_id IN ($personStuff) AND id >= $key  GROUP BY circles_id 
						 UNION ALL
						  SELECT * FROM ( SELECT * FROM circles_uploads ORDER BY id DESC ) AS circles_uploads WHERE  ((circles_id IN ($masterList) AND type=1) or (user_id IN($personStuff) AND type=5 )) AND id >= $key 

						  GROUP BY circles_id ORDER BY id DESC LIMIT 5");




						
	                                       
	                                              
						if($query->rowCount() && !array_key_exists("plate_ids",$query->results()[0]))
					  	{

					  	return $query->results();
					   	}
					   	else
					   	{
					   		return -1;
					   	}
				  }

				  public static function getOldPostsGeneralNew($userID,$lim)
				  {



						  		$circlesFollowing=circlescircles::getFollowcircles($userID);
						  	$circlesOwned=circlescircles::getOwnedcircles($userID);
						  	$circlesConts=circlescircles::getContribcircles($userID);
						  	
						  	$circlesFollowingArray=explode(",",$circlesFollowing);
						  	$circlesOwnedArray=explode(",",$circlesOwned);
						  	$circlesContsArray=explode(",",$circlesConts);

						  	
		                    $twoM=array_merge($circlesFollowingArray,$circlesOwnedArray);
		                    $masterArray=array_merge($twoM,$circlesContsArray);
							$masterArray=array_unique($masterArray);	
							$masterArray=array_filter($masterArray);	
							$masterList=implode(",",$masterArray);
							

							$personStuff=circlesUser::getFollowers($userID).",".circlescircles::getOwnersPls($masterArray);
							$personStuff=explode(",",$personStuff);
							$personStuff=array_filter($personStuff);
							$personStuff=array_unique($personStuff);
							$personStuff=implode(",", $personStuff);
		                    

							if($masterList=='')
							{
								$masterList="''";
							}
							if($personStuff=='')
							{
								$personStuff="''";
							}
							if($masterList==''||$personStuff=='')
								{
									return -1;
								}

							$index=circlesPosts::getIndexLower($personStuff,$masterList,$lim);

							$query=DB::getInstance()->query("SELECT max(id) AS id,uid,caption,circles_id,GROUP_CONCAT(`user_id`) AS user_id,max(date) AS date,GROUP_CONCAT(`type`) AS type ,ref_id,GROUP_CONCAT(`id`) AS group_id FROM `circles_uploads` WHERE ((type= 2 || type = 3) AND user_id IN ($personStuff) ) AND (id >= $index AND id < $lim) GROUP BY circles_id 
							 UNION ALL
							   SELECT * FROM ( SELECT * FROM circles_uploads ORDER BY id DESC ) AS circles_uploads WHERE  ( (circles_id IN ($masterList) AND type=1) or (user_id IN($personStuff) AND type=5 )) AND (id >= $index  AND id <$lim)

						  GROUP BY circles_id ORDER BY id DESC LIMIT 5");

							


							
						  	
						  	return $query->results();
				  }

			}





?>