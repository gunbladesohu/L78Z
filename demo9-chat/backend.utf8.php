<?php
// 配置信息：
// 1,数据库连接的具体信息
// 2,我们要存储的消息的数目
// 3,用户进到聊天室的时候消息显示的数目
$dbhost = "localhost";
$dbuser = "root";
$dbpass = "11111111";
$dbname = "chat";
$store_num = 10;
$display_num = 10;

// 错误报告
error_reporting(E_ALL);

// 头部信息
header("Content-type: text/xml");
header("Cache-Control: no-cache");

//连接mysql
$dbconn = mysqli_connect($dbhost,$dbuser,$dbpass,$dbname);
if (!$dbconn) 
{ 
    die("连接错误: " . mysqli_connect_error()); 
} 
//$dbconn = mysqli_connect("aaaa",$dbuser,$dbpass,$dbname);
mysqli_select_db($dbconn,$dbname);

//为容易操作请求数据,我们为请求中的每个参数设置一个变量,每个变量将把请求中的参数值作为其自己的值
//foreach语句遍历所有的POST数据,并且为每个参数创建一个变量,并且给它赋值
foreach($_POST as $key => $value){
	$$key = mysqli_real_escape_string($dbconn,$value);
}

//屏敝任何错误提示,判断action是否等于 postmsg
if(@$action == "postmsg"){
	//插入数据
	mysqli_query($dbconn, "INSERT INTO messages (`user`,`msg`,`time`) 
	             VALUES ('$name','$message',".time().")");
	//删除数据(因为我们默认值存储10条数据)
	mysqli_query($dbconn, "DELETE FROM messages WHERE id <= ".
				(mysqli_insert_id($dbconn)-$store_num));
}

//查询数据
$messages = mysqli_query($dbconn,"SELECT user,msg
						 FROM messages
						 WHERE time>$time
						 ORDER BY id ASC
						 LIMIT $display_num");
//是否有新记录
if(mysqli_num_rows($messages) == 0) $status_code = 2;
else $status_code = 1;

//返回xml数据结构
echo "<?xml version=\"1.0\"?>\n";
echo "<response>\n";
echo "\t<status>$status_code</status>\n";
echo "\t<time>".time()."</time>\n";
if($status_code == 1){ //如果有记录
	while($message = mysqli_fetch_array($messages)){
		$message['msg'] = htmlspecialchars(stripslashes($message['msg']));
		echo "\t<message>\n";
		echo "\t\t<author>$message[user]</author>\n";
		echo "\t\t<text>$message[msg]</text>\n";
		echo "\t</message>\n";
	}
}
echo "</response>";
?>