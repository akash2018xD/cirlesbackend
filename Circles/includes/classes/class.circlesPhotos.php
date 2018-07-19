<?php 

    /*Main Wrapper For Uploading and Display related photos*/
    class circlesPhotos
    {
        public static function upload_personal_profile($post)
        {
            $imageFile=$post['image_file'];
            $userID=$post['userID'];
            $chars="abcdefghijklmnopqrstuvxyzABCDEFGHIJKLMNOPQRSTUVXYZ0123456789";
            $rand=substr(str_shuffle($chars),0,25);
            $rand_folder=substr(str_shuffle($chars),0,16);
            mkdir("userdata/profile/$rand_folder");
            
            $image_name=$rand.".jpg";
            $path_to_put="userdata/profile/$rand_folder/".$image_name;
            
            file_put_contents($path_to_put,base64_decode($imageFile));  
            $path_db=ROOT_SITE_COMPLETE.$path_to_put;
            
           
            circlesPhotos::fix_android_iphone_imgs($path_to_put);
            circlesPhotos::createThumb($path_to_put);
            circlesPhotos::createMedium($path_to_put);
            $query=DB::getInstance()->query("UPDATE `users` SET `pro_pic` = ? WHERE `id` = ?  ",array($path_db,$userID));
               return $path_db;
          
        }
         public static function upload_circles_display($imageFile)
        {
            $chars="abcdefghijklmnopqrstuvxyzABCDEFGHIJKLMNOPQRSTUVXYZ0123456789";
            $rand=substr(str_shuffle($chars),0,25);
            $rand_folder=substr(str_shuffle($chars),0,16);
            mkdir("userdata/circles_display/$rand_folder");
            
            $image_name=$rand.".jpg";
            $path_to_put="userdata/circles_display/$rand_folder/".$image_name;
            file_put_contents($path_to_put,base64_decode($imageFile));  

                       
            circlesPhotos::fix_android_iphone_imgs($path_to_put);
            circlesPhotos::createThumb($path_to_put);
            circlesPhotos::createMedium($path_to_put);
           return ROOT_SITE_COMPLETE.$path_to_put;

        }
          public static function upload_circles_singleton($imageFile)
        {
            $chars="abcdefghijklmnopqrstuvxyzABCDEFGHIJKLMNOPQRSTUVXYZ0123456789";
            $rand=substr(str_shuffle($chars),0,25);
            $rand_folder=substr(str_shuffle($chars),0,16);
            mkdir("userdata/circles_uploads/$rand_folder");
            
            $image_name=$rand.".jpg";
            $path_to_put="userdata/circles_uploads/$rand_folder/".$image_name;
            file_put_contents($path_to_put,base64_decode($imageFile));  
            
            
                            
            circlesPhotos::fix_android_iphone_imgs($path_to_put);
            circlesPhotos::createThumb($path_to_put);
            circlesPhotos::createMedium($path_to_put);
             circlesPhotos::createFixed($path_to_put);
           return ROOT_SITE_COMPLETE.$path_to_put;   
        }
           /**
            * method for resizing an image into a normal medium size with dividing factoer 200 . almost full res
            * @param - path of the site
            */

        public static function createFixed($post_insert)
        {


                                       
                           $originalFile=$post_insert;
                            $path=$post_insert;
                            $imagex=explode(".",$originalFile);
                            $check_array=array('jpg','jpeg');
                            if (in_array($imagex[1],$check_array))
                            {
                                
                                    $image=$originalFile;
                                    
                            }
                            else
                            {

                                
                                $image=$imagex[0].".png";
                                
                                     $images = imagecreatefrompng($originalFile);
                                    $ox=imagepng($images, $image,100);
                            }
                            
                            
                            
                            
                            
                            $image_dims=getimagesize($image);
                            $image_width=$image_dims[0];
                            $image_height=$image_dims[1];
                            $exif_data=exif_read_data($path);
                            $val=$exif_data['Orientation'];
                               
                              
                                if($val==1||$val==3)
                                {
                                   $compress=245;
                                }
                                else
                                {
                                    $compress=315;
                                }
                            $new_size=($image_width+$image_height)/($image_width*($image_height/$compress));
                            
                           
                            $new_width=$image_width * $new_size;
                            $new_height=$image_height * $new_size;
                            $new_image = imagecreatetruecolor($new_width, $new_height);
                            $old_image= imagecreatefrompng($image);

                            imagecopyresampled ($new_image,$old_image,0,0,0,0,$new_width,$new_height,$image_width,$image_height);
                            imagesavealpha($new_image, true);
                            imagepng($new_image,$imagex[0].'_fixed.png');
                           

                            

                    


            

        }
        public static function fix_android_iphone_imgs($post_insert)
        {
            $originalFile=$post_insert;
                            $path=$post_insert;
                                    $imagex=explode(".",$originalFile);
                                    $check_array=array('jpg','jpeg');
                                    if (in_array($imagex[1],$check_array))
                                    {
                                        
                                            $image=$originalFile;
                                            
                                    }
                                    else
                                    {

                                        
                                        $image=$imagex[0].".jpg";
                                        
                                             $images = imagecreatefrompng($originalFile);
                                            $ox=imagejpeg($images, $image,100);
                                    }


                                $image = imagecreatefromjpeg($path);
                                  $exif = exif_read_data($path);
                                                                  if (!empty($exif['Orientation']))
                                   {
                                    switch ($exif['Orientation']) {
                                      case 3:
                                        $image = imagerotate($image, 180, 0);
                                         break;
                                      case 6:
                                        $image = imagerotate($image, -90, 0);
                                        
                                        break;
                                      case 8:
                                        $image = imagerotate($image, 90, 0);
                                        
                                        break;
                                    }
                                  }
                                    else
                                    {
                                   
                                    
                                    }
                                    imagejpeg($image, $path);

        }
        public static function createThumb($path)
            {



                            $originalFile=$path;
                            $imagex=explode(".",$originalFile);
                            $check_array=array('jpg','jpeg');
                            if (in_array($imagex[1],$check_array))
                            {
                                
                                    $image=$originalFile;

                            }
                            else
                            {

                                
                                $image=$imagex[0].".jpg";
                                
                                     $images = imagecreatefrompng($originalFile);
                                    $ox=imagejpeg($images, $image,100);
                            }
                            
                            
                            
                            $image_dims=getimagesize($image);
                            $image_width=$image_dims[0];
                            $image_height=$image_dims[1];
                            $new_size=($image_width+$image_height)/($image_width*($image_height/45));
                            $new_width=$image_width * $new_size;
                            $new_height=$image_height * $new_size;
                            $new_image = imagecreatetruecolor($new_width, $new_height);
                            $old_image= imagecreatefromjpeg($image);

                            imagecopyresampled ($new_image,$old_image,0,0,0,0,$new_width,$new_height,$image_width,$image_height);
                            imagejpeg($new_image,$imagex[0].'_thumb.jpg');
                            

                    


            }

            /**
            * method for resizing an image into a normal medium size with dividing factoer 200 . almost full res
            * @param - path of the site
            */

            public static function createMedium($path)
            {



                                      
                            $originalFile=$path;
                            $imagex=explode(".",$originalFile);
                            $check_array=array('jpg','jpeg');
                            if (in_array($imagex[1],$check_array))
                            {
                                
                                    $image=$originalFile;
                                    
                            }
                            else
                            {

                                
                                $image=$imagex[0].".jpg";
                                
                                     $images = imagecreatefrompng($originalFile);
                                    $ox=imagejpeg($images, $image,100);
                            }
                            
                            
                            
                            
                            
                            $image_dims=getimagesize($image);
                            $image_width=$image_dims[0];
                            $image_height=$image_dims[1];
                            $new_size=($image_width+$image_height)/($image_width*($image_height/205));
                            $new_width=$image_width * $new_size;
                            $new_height=$image_height * $new_size;
                            $new_image = imagecreatetruecolor($new_width, $new_height);
                            $old_image= imagecreatefromjpeg($image);

                            imagecopyresampled ($new_image,$old_image,0,0,0,0,$new_width,$new_height,$image_width,$image_height);
                            imagejpeg($new_image,$imagex[0].'_medium.jpg');
                            

                    


            }





    }

?>