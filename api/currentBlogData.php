<?php 
	include_once 'connect.php';
	$blog = json_decode(file_get_contents("php://input"));
	if(!empty($blog)){
		$blog_id = $blog->id;
		$query = "select * from blog_data where blog_data.blog_id='$blog_id'";

		if($result = mysql_query($query)){
			if(mysql_num_rows($result) == 1){
				$output = "{";
				while($row = mysql_fetch_assoc($result)){
					$output .= '"_id" : "'.$row['id'].'",';
					$output .= '"id": "'.$row['blog_id'].'",';
					$output .= '"title" : "'.$row['blog_title'].'",';
					$output .= '"info" : "'.$row['blog_info'].'",';
					// now add the tags array
					$blog_id = $row['id'];
					$tag_query = "select tag_data.tag from tag_data where tag_data.blog_id = $blog_id";
					$tags_array = [];	
					if($tag_result = mysql_query($tag_query)){
						while($tag = mysql_fetch_assoc($tag_result)){
							array_push($tags_array, $tag['tag']);
						}
						$output .= '"tags" : ["'. implode('","', $tags_array).'"]';
					}else{
						echo $tag_query;
						echo mysql_error();
					}
				}
				$output = rtrim($output, ',');
				$output .= "}";
				echo $output;
				}else{
					echo mysql_error();
				}
		}else{
			echo mysql_error();
		}
	}else{
		echo "blog data required";
	}

	mysql_close();
 ?>