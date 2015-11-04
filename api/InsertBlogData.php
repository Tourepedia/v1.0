<?php
	include_once 'connect.php';

	// get the json data from user
	$blog_data = json_decode(file_get_contents('php://input')); 
	
	if(!empty($blog_data)){
		$blog_title = $blog_data->title;
		$blog_info = $blog_data->info;
		$blog_id = $blog_data->id;
		$blog_tags = $blog_data->tags;
		$tags_string = implode(',', $blog_tags);

		// query for insertion into blogs table for new blog
		$query_for_blog = "INSERT INTO `blog_data` (`blog_title`, `blog_info`, `blog_id`) VALUES('$blog_title', '$blog_info', '$blog_id')";

		if(mysql_query($query_for_blog)){
			$inserted_blog_id = mysql_insert_id();
			$query_for_tag = "INSERT INTO `tag_data`
				(`blog_id`, `tag`) VALUES ";
			foreach ($blog_tags as $tag) {
				$query_for_tag .= "('$inserted_blog_id', '$tag'),";
			}
			$query_for_tag = rtrim($query_for_tag, ",");

			if(mysql_query($query_for_tag)){
				echo "data successfully updated";
			}else{
				echo "error while inserting data to tag_data table";
			}

		}else{
			echo "error while inserting data to blog_data table";
		}


	}else{
		echo "Data is requred";
	}

	mysql_close();
	
?>