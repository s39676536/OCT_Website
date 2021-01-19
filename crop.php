<?php
function startsWith($haystack, $needle)
{
     $length = strlen($needle);
     return (substr($haystack, 0, $length) === $needle);
}

function endsWith($haystack, $needle)
{
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }

    return (substr($haystack, -$length) === $needle);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$model = $_POST['model'];
	$rotate = $_POST['rotate'];
	$filename = $_POST['filename'];
	$targ_w = $_POST['w'];
	$targ_h = $_POST['h'];
	$jpeg_quality = 100;
	
	$src = $filename;
	if(endsWith($src, "jpg") || endsWith($src, "jpeg"))
		$img_r = imagecreatefromjpeg($src);	
	else if(endsWith($src, "png"))
		$img_r = imagecreatefrompng($src);	
	$dst_r = ImageCreateTrueColor( $targ_w, $targ_h );

	imagecopyresampled($dst_r,$img_r,0,0,$_POST['x'],$_POST['y'],$targ_w,$targ_h,$_POST['w'],$_POST['h']);	
	
	$dst_r = imagerotate($dst_r, $rotate, 0);
	
	$output = "r" . $filename;
	imagejpeg($dst_r, $output, $jpeg_quality);	
	
	$imagedata = file_get_contents($output);
	$base64 = base64_encode($imagedata);
	echo 'data:image/jpg;base64,' . $base64;

	exit;
}

// If not a POST request, display page below:

?>
