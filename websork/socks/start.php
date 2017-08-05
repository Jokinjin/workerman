
<?php
header("Content-Type: text/html;charset=utf-8"); 
use Workerman\Worker;
require_once __DIR__ . './../../Workerman/Autoloader.php';


// 创建一个Worker监听2345端口，使用http协议通讯
$http_worker = new Worker('http://0.0.0.0:2345');

$ws_worker = new Worker("websocket://0.0.0.0:2346");


// 接收到浏览器发送的数据时回复hello world给浏览器
$http_worker->onMessage = function($connection, $data)
{
	//这个地方是防止非法提交的，用redis设置一个有过期时间的key验证会比较好，这里就先这样了
	if(!empty($data['post']['site_id'])){
		$fi_name = date('Ymd his')."_".$data['post']['file_name'];
		//图片分组 判断是否有这个文件夹 没有则创建一个
		$site_path = __DIR__ .'/tmp/'.$data['post']['site_id'];
		if(!is_dir($site_path)){
			mkdir($site_path, 0777);
		}
		$save_path = $site_path.'/'.$fi_name;
	    file_put_contents($save_path, $data['files'][0]['file_data']);
	    $connection->send("上传成功");
	}else{
		$connection->send("非法上传");
	}
};


//websocket回调
$ws_worker->onMessage = function($connection, $data)
{
	$obj_data = json_decode($data);
	if($obj_data->str == "del"){
		$del_path = __DIR__ .'/tmp/'.$obj_data->site_id.'/'.$obj_data->ing_name;
		unlink ($del_path);
		$connection->send("删除成功");
	}

	//查找
	if($obj_data->str == "find"){
		$allowedExts = array("gif", "jpeg", "jpg", "png","gif","bmp");
		$hostdir=dirname(__FILE__).'/tmp/'.$obj_data->site_id;
		//判断是否有这个目录，要是没有则添加一个
		if(!is_dir($hostdir)){
			mkdir($hostdir, 0777);
		}
		//获取目录下的所有文件名
		$file_name=scandir($hostdir);
		$img_name = array(); 
		//读取对应文件夹下面的图片路径和名字
		foreach ($file_name as $key => $val_name) {
			$file_type = explode(".", $val_name);
			foreach ($file_type as $val_type) {
				if(in_array($val_type, $allowedExts)){
					$a  = explode("_", $val_name);
					$img_name[$key]['img_name'] = $a[1];
					$img_name[$key]['path'] = "tmp/".$obj_data->site_id.'/'.$val_name;
					$img_name[$key]['hid_name'] = $val_name;
				}
			}
		}
   		$connection->send(json_encode($img_name));
	}

	
};

Worker::runAll();

