<?php 
	
	$data = json_decode(file_get_contents('php://input'));
	if(!empty($data)){
		$file_id = $data[0]->id;
		$newcomment_json = $data[1];
		$file_path = './../assets/json/comments/'.$file_id.'.json';
		$comments_json = [];
		if(!file_exists($file_path)){
			$FILE = fopen($file_path, 'w');
			fclose($FILE);
			file_put_contents($file_path,  "[]");
		}
		
		$comments_json = json_decode(file_get_contents($file_path));
		array_push($comments_json, $newcomment_json);
		file_put_contents($file_path,  json_encode($comments_json));

	}
		
 ?>