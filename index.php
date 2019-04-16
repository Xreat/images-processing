<form action="index.php?action=create" method="POST" enctype="multipart/form-data">
         <input type="file" name="image" /><br>
         <input type="submit" value="Dodaj"/><br><br><br>
</form>
<?php
$action = $_GET['action'];


if($action == 'create' && isset($_FILES['image'])) {

$errors= array();
      $file_name = $_FILES['image']['name'];
      $file_size =$_FILES['image']['size'];
      $file_tmp =$_FILES['image']['tmp_name'];
      $file_type=$_FILES['image']['type'];
      $file_ext=strtolower(end(explode('.',$_FILES['image']['name'])));
      
      $extensions= array("jpeg","jpg","png");
      
      if(in_array($file_ext,$extensions)=== false){
         $errors[]="extension not allowed, please choose a JPEG or PNG file.";
      }
      
      if($file_size > 2097152){
         $errors[]='File size must be excately 2 MB';
      }
      
      if(empty($errors)==true){
         move_uploaded_file($file_tmp,"images_src/".$file_name);
      }else{
         print_r($errors);
      }

//set the source image (foreground)
$sourceImage = 'mark.png';

//set the destination image (background)
$destImage = "images_src/".$file_name;

//get the size of the source image, needed for imagecopy()
list($srcWidth, $srcHeight) = getimagesize($sourceImage);

//create a new image from the source image
$src = imagecreatefrompng($sourceImage);

$pieces = explode('.', $file_name);

$size = sizeof($pieces) - 1;
//create a new image from the destination image
if($pieces[$size] == 'jpg' || $pieces[$size] == 'jpeg') {
$dest = imagecreatefromjpeg($destImage);
}
if($pieces[$size] == 'png') {
$dest = imagecreatefrompng($destImage);
}
$dest = imagescale($dest, 1024);

list($destWidth, $destHeight) = getimagesize($destImage);
	$newDestHeight = $destHeight/$destWidth * 1024;
//set the x and y positions of the source image on top of the destination image
$src_xPosition = 1024-256; //75 pixels from the left
$src_yPosition = $newDestHeight-256; //50 pixels from the top

//set the x and y positions of the source image to be copied to the destination image
$src_cropXposition = 0; //do not crop at the side
$src_cropYposition = 0; //do not crop on the top


//merge the source and destination images
imagecopy($dest,$src,$src_xPosition,$src_yPosition,$src_cropXposition,$src_cropYposition,$srcWidth,$srcHeight);

//output the merged images to a file
/*
 * '100' is an optional parameter,
 * it represents the quality of the image to be created,
 * if not set, the default is about '75'
 */

$timestamp = time();
imagejpeg($dest,'images/'.$timestamp.'.jpg',100);

echo('<img src="images/'.$timestamp.'.jpg">');
//destroy the source image
imagedestroy($src);

//destroy the destination image
imagedestroy($dest);
}
?>
