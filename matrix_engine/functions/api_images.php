<?php  
## =======================================================================  
##  api_images.php														  
## =======================================================================  
##  Version: 		0.03													  											  
##  by: 			S. Elsner											  
## =======================================================================  
##  Description:															  
##    * image manipulation functions	  
## =======================================================================  
##
##  22.11.2003:  
##    * inital setup  
##    * images can be stored in the cache
## =======================================================================  


        
## =======================================================================        
##  API_images_jpg_overlay        
## =======================================================================        
##  copies the second image onto the first image       
##  it sets the transparency to the color of the top/left pixel      
##        
## =======================================================================        

function API_images_overlay(&$src_image,$overlay_image) {		
	imagecolortransparent($overlay_image,imagecolorat($overlay_image,0,0));  
	$insert_x = imagesx($overlay_image); 
	$insert_y = imagesy($overlay_image); 
	
	imagecopymerge($src_image,$overlay_image,0,0,0,0,$insert_x,$insert_y,100); 
}		

## =======================================================================        
##  API_images_resizeMaxPart        
## =======================================================================        
##  resizes an image to a certain width and height-      
##  the ratio remains the same- the image will fill the 
##  target size completely         
## =======================================================================        
function API_images_resizeMaxPart($src, $width, $height) {
	## we basically resize the image and return the resulting image		
	
	## if we need to keep the aspect ratio,
	## we need to check if the height or width is relevant for us
	$current_size_x  = imagesx($src);
	$current_size_y =  imagesy($src);		
	$size_x = $width / $current_size_x;
	$size_y = $height / $current_size_y;
	
	$scale = max($size_x,$size_y);

	## now we create the destination image
	$destination = ImageCreateTrueColor($width, $height);
	
	## and finally scale the whole thing
	imagecopyresampled($destination,$src,0,0,0,0,($current_size_x*$scale),($current_size_y*$scale),$current_size_x,$current_size_y);
	
	return $destination;
}

        
## =======================================================================        
##  API_images_resize        
## =======================================================================        
##  resizes an image to a certain width and height-      
##  the ratio remains the same- the image will fill the 
##  target size completely         
## =======================================================================        
function API_images_resize($src, $width, $height) {
	## we basically resize the image and return the resulting image		
	
	## if we need to keep the aspect ratio,
	## we need to check if the height or width is relevant for us
	$current_size_x  = imagesx($src);
	$current_size_y =  imagesy($src);		
	$size_x = $current_size_x / $width;
	$size_y = $current_size_y / $height;
	
	$scale = min($size_x,$size_y);

	## now we create the destination image
	$destination = ImageCreateTrueColor($width, $height);
	
	## and finally scale the whole thing
	imagecopyresampled($destination,$src,0,0,0,0,($current_size_x/$scale),($current_size_y/$scale),$current_size_x,$current_size_y);
	
	return $destination;
}


## =======================================================================        
##  API_images_resize        
## =======================================================================        
##  resizes an image to a certain width and height-      
##  the ratio remains the same- the image will fill the 
##  target size completely         
## =======================================================================        
function API_images_Crop($src, $top,$left,$width,$height,$target_width,$target_height) {
	## we basically resize the image and return the resulting image		
	## now calculate the scale 
	$current_size_x  = imagesx($src);
	$current_size_y =  imagesy($src);		

	$scale = $current_size_y / $height;
	
	$target_width = intval($target_width);
	$target_height = intval($target_height);
	
	## now we create the destination image
	$destination = imagecreatetruecolor($target_width, $target_height);

	// make the background white
	$rgb = hexdec('ffffff');
	$color = imagecolorallocate($destination, ($rgb&(0xff0000))>>16, ($rgb&(0xff00))>>8, $rgb&0xff);
	imagefill($destination,0,0,$color);

	imagecopyresampled($destination,$src,0,0,$left,$top,$target_width,$target_height,$width,$height);

	return $destination;
}


## =======================================================================        
##  API_images_resize_maxSize        
## =======================================================================        
##  resizes an image to a certain width and height-      
##  the ratio remains the same- the image will fill the 
##  target size completely         
## =======================================================================        
function API_images_resize_maxSize($src, $width, $height) {
	## we basically resize the image and return the resulting image		
	
	## we need to check if the height or width is relevant for us
	$current_size_x  = imagesx($src);
	$current_size_y =  imagesy($src);		
	$size_x = $width / $current_size_x;
	$size_y = $height / $current_size_y;
	
	$scale = min($size_x,$size_y);
	
	if($scale > 1) {
		$scale = 1;
	}
	
	$new_x = intval($current_size_x*$scale);
	$new_y = intval($current_size_y*$scale);
	## now we create the destination image
	$destination = ImageCreateTrueColor($new_x,$new_y);
	
	## and finally scale the whole thing
	imagecopyresampled($destination,$src,0,0,0,0,$new_x,$new_y,$current_size_x,$current_size_y);
	
	return $destination;
}
 
## =======================================================================        
##  API_images_resize        
## =======================================================================        
##  resizes an image to a certain width and height-      
##  the ratio remains the same- the image will fill the 
##  target size completely         
## =======================================================================        
function API_images_resizeToheight($src, $width,$height) {
	## we basically resize the image and return the resulting image		
	## now calculate the scale 
	$current_size_x  = imagesx($src);
	$current_size_y =  imagesy($src);		

	$scale = $current_size_y / $height;
	
	## now we create the destination image
	$destination = ImageCreateTrueColor($width, $height);
	imagefilledrectangle($destination, 0,0, $width,$height, imagecolorallocate($destination, 255,255,255)); 	

	## and finally scale the whole thing
	imagecopyresampled($destination,$src,0,0,0,0,($current_size_x/$scale),$height,$current_size_x,$current_size_y);

	return $destination;
}
    
## =======================================================================        
##  API_images_resizeTowidth        
## =======================================================================        
##  resizes an image to a certain width and height-      
##  the ratio remains the same- the image will fill the 
##  target size completely         
## =======================================================================        
function API_images_resizeTowidth($src, $width,$height) {
	## we basically resize the image and return the resulting image		
	## now calculate the scale 
	$current_size_x  = imagesx($src);
	$current_size_y =  imagesy($src);		

	$scale = $current_size_x / $width;
	
	## now we create the destination image
	$destination = ImageCreateTrueColor($width, $height);
	imagefilledrectangle($destination, 0,0, $width,$height, imagecolorallocate($destination, 255,255,255)); 	

	## and finally scale the whole thing
	imagecopyresampled($destination,$src,0,0,0,0,$width,($current_size_y/$scale),$current_size_x,$current_size_y);

	return $destination;
}    
            
## =======================================================================        
##  API_images_loadImage        
## =======================================================================        
##  loads an image no matter what type it is            
## =======================================================================        
function API_images_loadImage($path_to_image) {
	## her we setup the type of images we can process
	$supported_images = array('jpg'=>'JPEG','jpeg'=>'JPEG','gif'=>'GIF','png'=>'PNG');
	

	if (!@file_exists($path_to_image)) {
		return -1;
	}
	
	## first we split the filename
	$extension = strtolower(substr(strrchr($path_to_image, ".") ,1));

	switch($extension) {
		case 'jpg' :
		case 'jpeg':
			$image = @ImageCreateFromJPEG($path_to_image);
			break;
		case 'gif' :
			$image = @ImageCreateFromGIF($path_to_image);
			break;
		case 'png' :
			$image = @ImageCreateFromPNG($path_to_image);
			break;
		default:
			$image = -1;
			break;
	}
	
	return $image;	
} 

## =======================================================================        
##  ImageColorAllocateHEX        
## =======================================================================        
##  assigns a hex color to an image
## =======================================================================
function API_images_imageColorAllocateHEX(&$image,$color) {
	## first strip off the leading #
	if ($s[0]=="#") {
		$color = substr($color,1);
	}
	
	## convert the hex value
	$bg_dec = hexdec($color);

	$color = imagecolorallocate($image,($bg_dec & 0xFF0000) >> 16,($bg_dec & 0x00FF00) >> 8,($bg_dec & 0x0000FF));
	return $color;
}



## =======================================================================        
##  API_imagesFitImage        
## =======================================================================        
##  resizes and crops an image- this is a compound function
##  does support imagemagick wehn enabled in the settings file
## =======================================================================
function API_imagesResizeImage($filename,$path,$width,$height=0,$type='jpeg') {
	## we will need store the file for caching
	$fileInfo= explode(".",$filename);
	$mainImage = false;
	## check if it already exists
	if(!file_exists($path.$fileInfo[0].'_'.$width.'_'.$height.'.'.$fileInfo[1])) {
		## we need to generate the image
		$mainImage = API_images_loadImage($path.$filename);
		if($mainImage !== false) {
			if(USE_IMAGEMAGICK === true) {
				## okay we need to generate that image- we will utilize image magic
				
				## we do require the image size though
				$current_size_x = imagesx($mainImage);
				$current_size_y = imagesy($mainImage);
				$size_x = $width / $current_size_x;
				$size_y = $height / $current_size_y;
				
				if($size_x > $size_y) {
					$scale = 'width';
				} else {
					$scale = 'height';
				}
		
				## okay we now can do the two step scling and cropping process
				$parameters = $path.$filename.' ';
				if($scale == 'width') {
					## set the parameter
					$parameters .= ' -resize '.$width.'x ';
				} else {
					$parameters .= ' -resize x'.$height.' ';
				}
		
				$parameters .= '-quality 100 -gravity center -crop '.$width.'x'.$height.'+0+0 +repage '.$path.$fileInfo[0].'_'.$width.'_'.$height.'.'.$fileInfo[1];
				
				exec(IMAGEMAGICK_PATH.'convert '.$parameters,$array_output, $return_value);
				## okay we are done- call the imagemagick shell script
			} else{
				## resize the image according to the setup				
				$mainImage = API_images_resizeToheight($mainImage,$width,$height);	
				## save the image
				if($type =='jpeg') {
					@Imagejpeg($mainImage,$path.$fileInfo[0].'_'.$width.'_'.$height.'.'.$fileInfo[1]);
				} else {
					@Imagegif($mainImage,$path.$fileInfo[0].'_'.$width.'_'.$height.'.'.$fileInfo[1]);
				}
			}
			@ImageDestroy($mainImage);
			return $fileInfo[0].'_'.$width.'_'.$height.'.'.$fileInfo[1];
		}
	} else {
		return $fileInfo[0].'_'.$width.'_'.$height.'.'.$fileInfo[1];
	}
				
	
}				
?>
