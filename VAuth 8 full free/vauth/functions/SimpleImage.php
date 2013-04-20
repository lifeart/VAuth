<?php
 
/*
* File: SimpleImage.php
* Author: Simon Jarvis
* Copyright: 2006 Simon Jarvis
* Date: 08/11/06
* Link: http://www.white-hat-web-design.co.uk/articles/php-image-resizing.php
*
* This program is free software; you can redistribute it and/or
* modify it under the terms of the GNU General Public License
* as published by the Free Software Foundation; either version 2
* of the License, or (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details:
* http://www.gnu.org/licenses/gpl.html
*
*/
 
class SimpleImage {
 
   var $image;
   var $image_type;
 
   function load($filename) {
 
      $image_info = getimagesize($filename);
      $this->image_type = $image_info[2];
      if( $this->image_type == IMAGETYPE_JPEG ) {
 
         $this->image = imagecreatefromjpeg($filename);
      } elseif( $this->image_type == IMAGETYPE_GIF ) {
 
         $this->image = imagecreatefromgif($filename);
      } elseif( $this->image_type == IMAGETYPE_PNG ) {
 
         $this->image = imagecreatefrompng($filename);
      }
   }
   function save($filename, $image_type=IMAGETYPE_JPEG, $compression=10, $permissions=null) {
 
      if( $image_type == IMAGETYPE_JPEG ) {
         imagejpeg($this->image,$filename,$compression);
      } elseif( $image_type == IMAGETYPE_GIF ) {
 
         imagegif($this->image,$filename);
      } elseif( $image_type == IMAGETYPE_PNG ) {
 
         imagepng($this->image,$filename);
      }
      if( $permissions != null) {
 
         @chmod($filename,$permissions);
      }
   }
   function output($image_type=IMAGETYPE_JPEG) {
 
      if( $image_type == IMAGETYPE_JPEG ) {
         imagejpeg($this->image);
      } elseif( $image_type == IMAGETYPE_GIF ) {
 
         imagegif($this->image);
      } elseif( $image_type == IMAGETYPE_PNG ) {
 
         imagepng($this->image);
      }
   }
   function getWidth() {
 
      return imagesx($this->image);
   }
   function getHeight() {
 
      return imagesy($this->image);
   }
   function resizeToHeight($height) {
 
      $ratio = $height / $this->getHeight();
      $width = $this->getWidth() * $ratio;
      $this->resize($width,$height);
   }
 
   function resizeToWidth($width) {
      $ratio	= $width / $this->getWidth();
      $height	= $this->getheight() * $ratio;
      $this->resize($width,$height);
   }
 
   function scale($scale) {
      $width	= $this->getWidth() * $scale/100;
      $height	= $this->getheight() * $scale/100;
      $this->resize($width,$height);
   }
 
		#Добавим хитрую VAuth функцию, которая будет делать "Заебись"
	function crop_to_square($width,$square_hor_shift,$square_vert_shift) {
		#Масштабируем по заданной ширине
			
			$img_width = $this->getWidth();
			
			if ( $img_width < $width )  $this->resizeToWidth($width);
			
			
			$width2	=	$width;
		
			$x	=	0;
			$y	=	0;
		
			if ($this->getWidth()<$this->getHeight()) {
			
				$ratio	= $width / $this->getWidth();
				$height = $this->getheight() * $ratio;
				$this->resize($width,$height);
				
				if ($square_vert_shift==1) {
					$ratio2	=	$this->getheight()/$this->getWidth();
					$shift	=	$width2*$ratio2;
					$shift	=	$shift-$width2;
					$shift	=	$shift/2;
					$y		=	$shift;
				}
			
			}
			
			if ($this->getWidth()==$this->getHeight()) {
				$ratio	= $width / $this->getWidth();
				$height	= $this->getheight() * $ratio;
				$this->resize($width,$height);
			}
			
			if ($this->getWidth()>$this->getHeight())
			{
				$height=$width;
				$ratio = $height / $this->getHeight();
				$width = $this->getWidth() * $ratio;
				$this->resize($width,$height);
			
				if ($square_hor_shift	==	1) {
					$ratio2	=	$this->getWidth()/$this->getheight();
					$shift	=	$width2*$ratio2;
					$shift	=	$shift-$width2;
					$shift	=	$shift/2;
					$x		=	$shift;
				}

			}
			
			$old_image = imagecreatetruecolor($width, $height);
			imagecopyresampled($old_image, $this->image, 0, 0, $x, $y, $width, $height, $this->getWidth(), $this->getHeight());

		#Масштабируем по заданной ширине
	
		$height=$width2;
		$new_image = imagecreatetruecolor($width2, $width2);
		imagecopy($new_image, $old_image, 0, 0, 0, 0, $width2, $width2);

		$this->image = $new_image;
   } 
 
   function resize($width,$height) {
      $new_image = imagecreatetruecolor($width, $height);
      imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
      $this->image = $new_image;
   }
}
?>