<?php
	header("Content-Type: text/html;charset=utf-8"); 
	class upload{
		
		function file_detection($files){
			if ($files["file_error"] > 0)
			{
				echo "错误：" . $files["file_error"];exit;
			}
	        $max_file_size=2000000;
			//文件类型
			$allowedExts = array("gif", "jpeg", "jpg", "png","gif","bmp");
			
			$temp = explode(".", $files["file_name"]);
			$extension = end($temp);
			if(in_array($extension, $allowedExts)){
				if($files["file_size"] >= $max_file_size){
					echo "上传文件不可大于20M";exit;
				}
			}else{
				echo "非法文件";exit;
			}
			$this->unload_curl($files);
		} 

		function unload_curl($files){
			$url = "http://127.0.0.1:2345";
			$ch = curl_init();
			curl_setopt($ch , CURLOPT_URL ,$url);
			curl_setopt($ch, CURLOPT_TIMEOUT,10);
			curl_setopt($ch , CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch , CURLOPT_POST, 1);
			curl_setopt($ch , CURLOPT_POSTFIELDS, $files);
			$output = curl_exec($ch);
			curl_close($ch);
			echo $output;
		}
	}

	//文件的名称
	$file_name = $_FILES["file"]["name"];
	//文件的类型
	$file_type = $_FILES["file"]["type"];
	//文件的大小
	$file_size = $_FILES["file"]["size"];
	//存储在服务器的文件的临时副本的名称
	$tmp_name = $_FILES["file"]["tmp_name"];
	//上传错误
	$file_error = $_FILES["file"]["error"];

	$files = array();
	$files['file_name'] = $file_name;
	$files['file_type'] = $file_type;
	$files['file_size'] = $file_size;
	$files['tmp_name'] = '@'.$tmp_name;
	$files['file_error'] = $file_error;
	//用于分组
	$files['site_id'] = 't';

	$upload = new upload;
	$upload->file_detection($files);
	//$upload->unload_curl($files);
	
 ?>