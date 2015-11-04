<?php 

	if(isset($_FILES['file']) and !empty($_FILES['file']) and isset($_POST) and !empty($_POST)){
		$tmp_name = $_FILES['file']['tmp_name'];
		$location = $_POST['fileDestination'];
		$file_name = $_POST['fileId'];
		$filename = $_FILES['file']['name'];
		$tempFileName = $filename;
		while (strpos($tempFileName,'.')) {			
				$tempFileName = substr($tempFileName, strpos($tempFileName, '.') + 1);
		}	
		$extension = $tempFileName;
		$newFileName = $location.$file_name.'.'.$extension;
		if(move_uploaded_file($tmp_name, $newFileName)){
			echo "<h3>File succesfully uploaded</h3> with $newFileName";
		}else{
			echo "<h3>Error while uploading your file. Please try after some time.</h3>";
		}
	}	
	
 ?>