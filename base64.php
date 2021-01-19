<?php
$filename = "";
if ($_FILES["photo"]["error"] > 0 || count($_FILES["photo"]["name"]) == 0){
	$filename = $_POST["example"];
} else {
	$imagedata = file_get_contents($_FILES["photo"]["tmp_name"]);
	$base64 = base64_encode($imagedata);
	echo 'data:image/jpg;base64,' . $base64;
	//move_uploaded_file($_FILES["file"]["tmp_name"], "upload/".$_FILES["file"]["name"]);
	//$filename = "upload/".$_FILES["file"]["name"];
}
?>