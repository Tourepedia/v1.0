<?php 
	include_once 'connect.php';
	$tags_json = json_decode(file_get_contents("php://input"));
	if(!empty($tags_json)){
		$tags = $tags_json->tags;
		$tag_search_string = '"'. implode('","', $tags). '"';
		$query = "select blog_data.* 
		from blog_data inner join
		 (select distinct tag_data.blog_id from tag_data 
		 	where tag_data.tag in ( $tag_search_string ))
		related_blogs_id on blog_data.id = related_blogs_id.blog_id";

		if($result = mysql_query($query)){
			$output = "[";
			while($row = mysql_fetch_assoc($result)){
				$output .= "{";
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
				$output .= "},";
			}
			$output = rtrim($output, ',');
			$output .= "]";
			echo $output;
		}else{
			echo mysql_error();
		}
	}else{
		echo "tags are required";
	}
	mysql_close();
 ?>