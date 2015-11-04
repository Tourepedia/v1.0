<?php 

include_once 'connect.php';

	// get the json data from user
	$data = json_decode(file_get_contents('php://input')); 
if(!empty($data)){
	$blog_id = $data->id;
$query = <<<EOF
select blog_data.blog_id, blog_data.blog_title, blog_data.blog_info 
	from blog_data 
	inner join (
		select distinct tag_data.blog_id 
		from tag_data 
		inner join (
			SELECT tag_data.id 
			FROM `tag_data` 
			inner join blog_data
			on blog_data.id = tag_data.blog_id
			WHERE blog_data.blog_id = '$blog_id'
		) tags 
		on tag_data.id = tags.id 
	) related_blog_id 
	on related_blog_id.blog_id = blog_data.id
EOF;

	if($result = mysql_query($query)){
		$output =  "[";
		while($row = mysql_fetch_assoc($result)){
			$output .= '{ "id" :"'.$row['blog_id'].'",';
			$output .= '"title" : "'.$row['blog_title'].'",';
			$output .= '"info" : "'.$row['blog_info']. '"},';
		}
		$output = rtrim($output, ',');
		$output .= "]";
		echo $output;
	}else{
		echo mysql_error();
	}
}
mysql_close();

?> 