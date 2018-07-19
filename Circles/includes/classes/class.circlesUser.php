<?php 


/*Main Wrapper for circles user functions*/

	/**
	* Register user
	* @param - Username
	* @param Email
	* @param - Password
	*/
	class circlesUser
	{

	public static function register($post)
	{ 

		/*This can be used two times Oauth or Normal*/
		$message="";
		$id=0;
		$token="";
		$response=array('state'=>false,'message'=>$message,'sess_id'=>$id);
		if(isset($post['oType']))
		{
				$provider=explode("_",$post['oType']);
				if(circlesUser::check_email_duplication($post['email']))
				{
					$response['message']="The ".$provider[0]." account you are trying to register with is already registered by normal registration method";
					return $response;
				}
				else
				{
					if(circlesUser::check_username_duplication($post['username']))
					{
						$response['message']="Please enter a different username as this username has already been taken";
						return $response;
					}
					else
					{
						$security_token=$post['token'];
						$post['full']=str_replace("-"," ",$post['full']);
						DB::getInstance()->insert("users",array(

								'email'=>$post['email'],		
								'full_name'=>$post['full'],
								'mob'=>$post['mob'],
								'roll_no'=>$post['roll_no']
								
							));
					}
					
				    $response['sess_id']=$id;
				     $response['state']=true;
					return $response;

				}

				//Alright so Oauth will again be for regs or Login ... It will be our job to decicde what has to be done
				//So in the main_login ..As soon as the Oauth is completed Go Insert or Check in the DB .. If email already found , MEANS {LOGIN} ->PUSH HIM TO PROFILE
				// If false . registration has to be done so  {iNSERT THE EMAIL , ETC } -> RETURN ID , PUT THE ID AS AN iNTENT extra 
				/*  then Show him  the username activity based on the extra Id update the username and proceed to the profile class  */
		}
		else
		{
				if(circlesUser::check_email_duplication($post['email']))
				{
					$response['message']="Email adress already taken";
					return $response;
				}

				if( !preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9._-]+)+$/", $post['email']) )
						  {
		                                                 $response['message']="Please enter a valid email adress";
						  		return $response; // Handle regex message in java itself 
						  }


				if(strlen($post['password'])<7)
				{
					return false;//Safety Check , Handle message in java
				}
				if(circlesUser::check_username_duplication($post['username']))
					{
						$response['message']="Please enter a different username as this username has already been taken";
						return $response;
					}
					$post['full']=str_replace("-"," ",$post['full']);
						$security_token=circlesUser::getSecurityToken();
						
				DB::getInstance()->insert("users",array(

						'email'=>$post['email'],
						'password'=>md5($post['password']),
						'username'=>$post['username'],
						'firebase_token'=>$post['firebase_token'],
						'security_token'=>$security_token,
						'full'=>$post['full']


					));
				$id=DB::getInstance()->lastID();
                                                      $query=DB::getInstance()->insert("circles_follow",array(

				  				'user_from'=>$id,
				  				'plateID'=>108

				  		));
				$response['sess_id']=$id;
				 $response['security_token']=$security_token;
				$response['state']=true;
				return $response;
		}
		return null;							        

	}

	public static function getSecurityToken()
	{

			  $chars="abcdefghijklmnopqrstuvxyzABCDEFGHIJKLMNOPQRSTUVXYZ0123456789";
          	  $rand=substr(str_shuffle($chars),0,140);
          	  //$rand=md5($rand);
          	  return $rand;
	}	
	/**
	* Uniqueness of Email
	* @param - EmailID
	*/
	public static function check_email_duplication($email)
	{


		$query=DB::getInstance()->query("SELECT id FROM users WHERE email =?",array($email));
		if ($query->rowCount())
		{
			return 1;
		}
		else

		{

			return 0;
		}

    }
    /**
	* Uniqueness of Email
	* @param - EmailID
	*/
	public static function check_username_duplication($email)
	{


		$query=DB::getInstance()->query("SELECT id FROM users WHERE username =?",array($email));

		if ($query->rowCount())
		{
			return 1;
		}
		else

		{

			return 0;
		}

    }
  
    public static function check_oauth_email_duplication($email,$method)
	{

                  echo "codkd";
		$response=array();           
		$response['error']=false;   
		/*Auth Type*/
		if(circlesUser::oauth_type_num($method)==2)
		{
		

		}
		else
		{

		}
		
	
					$query=DB::getInstance()->query("SELECT id FROM users WHERE email =?  AND oauth !=0 ",array($email));
			                 
					if ($query->rowCount())
					{
			                      
						//Login
						$res=$query->results();
						$response['state']=true;
						$response['sess_id']=$res[0]->id;
						return $response;

					}
					else

					{
						$response['error']=false;
						$response['state']=false;

				    		return $response;// Register Attemp
						
					}
		

    }

  

    /**
	*	Decide oAuth no based on the oAuth type
    **/
    public static function oauth_type_num($oAuth)
    {
    	if($oAuth=="facebook_auth")
    	{
    		return 2;
    	}
    	else
    	{
    		//Gmail
    		return 3;
    	}
    }
   
	 public static function get_val($field,$value)
     {

     		$query=DB::getInstance()->query("SELECT {$field} FROM users WHERE id = ?",array($value));
     		$res=$query->results();
     		$res[0]->$field=utf8_urldecode($res[0]->$field);
     		return $res[0]->$field;

     }
   
     
 
}


?>	