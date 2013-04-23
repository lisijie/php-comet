<?php
/**
 * Comet聊天演示
 * 
 * 很简单的原理：
 * 客户端请求服务端获取数据，服务端检查是否有新数据，有就返回新数据，没有就轮询保持连接，直到有新数据进来
 * 
 * @copyright (c) 2013 www.lisijie.org
 * @author lisijie <lsj86@qq.com>
 * @version $Id$
*/

define('DATA_FILE', 'chat.dat');
define('LOCK_FILE', 'chat.lock');
set_time_limit(0); //防止服务器超时

$do = $_GET['do'];

if (empty($_COOKIE['name'])) {
	out(array('ret'=>-1, 'msg'=>'缺少昵称'));
}

//发送消息
if ($do == 'send') {
	$msg = strip_tags(trim($_POST['msg']));
	$msg = nl2br($msg);
	
	$data = array('user'=>$_COOKIE['name'], 'msg'=>$msg, 'time'=>date('H:i:s', time()));
	file_put_contents(DATA_FILE, json_encode($data)."\n", FILE_APPEND);
	file_put_contents(LOCK_FILE, filesize(DATA_FILE));
	clearstatcache();

	out(array('ret'=>0,'time'=>time()));

//拉取消息
} elseif ($do == 'get') {
	//最后拉取位置
	$lastpos = $_GET['pos'] > 0 ? intval($_GET['pos']) : filesize(DATA_FILE);
	
	//原理就是下面这个循环了
	do {
		$pos = file_get_contents(LOCK_FILE);
		usleep(100000);
	} while ($pos <= $lastpos);

	$ret = array('ret'=>0, 'pos'=>$pos, 'data'=>array());

	$fp = fopen(DATA_FILE, 'rb');
	fseek($fp, $lastpos, SEEK_SET);
	while(!feof($fp)) {
		$line = fgets($fp, 1024);
		if ($line) {
			$ret['data'][] = json_decode($line);
		}
	}

	out($ret);
}

//JSON输出
function out($data) {
	echo json_encode($data);
	exit;
}
