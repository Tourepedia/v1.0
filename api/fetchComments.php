<?php 

	$file_data = json_decode(file_get_contents('php://input')); 

	if(!empty($file_data)){
		$file_id = $file_data->id;
		$file_path = './../assets/json/comments/'.$file_id.'.json';
		if(file_exists($file_path))
			echo	file_get_contents($file_path);
		else
			echo "[]";
	}



 ?>