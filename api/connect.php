<?php 
	$conn = mysql_connect('localhost', 'root', '');
	if($conn){
		if(mysql_select_db('blog')){
			
		}else{
			echo "Error while selecting database";
		}
	}else{
		echo "Problem why connecting to server";
	}

 ?>