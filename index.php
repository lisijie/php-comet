<?php
if (isset($_GET['name'])) {
	setcookie('name', htmlspecialchars(trim($_GET['name'])));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Comet 聊天演示</title>
	<meta name="Author" content="lsj86@qq.com">
	<script type="text/javascript" src="http://lib.sinaapp.com/js/jquery/1.9.1/jquery-1.9.1.min.js"></script>
	<script type="text/javascript">
		function sendmsg() {
			var msg = $(':input[name="msg"]').val();
			if (msg) {
				var url = "server.php?do=send&t="+Math.random();
				$.post(url, {msg:msg}, function(ret) {
					eval("ret = ("+ret+");");
					if (ret.ret == 0) {
						$(':input[name="msg"]').val('');
					} else {
						alert(ret.msg);
					}
				});
			}
		}
		function load(pos) {
			var url = "server.php?do=get&pos="+pos+"&t="+Math.random();
			$.getJSON(url, function(msg) {
				append(msg);
				load(msg.pos);
			});
		}
		function append(msg) {
			var obj = $("#content");
			for (row in msg.data) {
				obj.append("<p><span>"+msg.data[row].user+" ("+msg.data[row].time+")</span><br />"+msg.data[row].msg+"</p>");
			}
			document.getElementById('content').scrollTop = document.getElementById('content').scrollHeight;
		}

		window.onload = setTimeout("load(0)", 200);
	</script>
	<style type="text/css">
		body {background:#f0f0f0;font:13px Verdana, Arial, Helvetica, sans-serif;}
		h1 {font-size:14px; margin:0; padding:0; background:#f0f0f0; text-align:center; line-height:150%; border-bottom:1px solid #ccc;}
		p {margin:0; margin-bottom:10px; line-height:120%}
		#main {background:#fff; width:500px; border:1px solid #ccc; margin:0 auto; margin-top:50px}
		#content {height:370px; overflow-y:scroll; padding:10px; font-size:12px;}
		#content span {color:blue;}
		#input {height:40px; border-top:1px solid #ccc; padding:5px 0 5px 5px}
	</style>
</head>
<body>
	<?php
		if ($_GET['name']):
	?>
	<div id="main">
		<h1>Comet聊天演示</h1>
			<div id="content"></div>
			<div id="input">
			<form method="post" onsubmit="sendmsg();return false;">
				<input type="text" style="padding:3px;" size="40" name="msg" />
				<input type="submit" style="padding:3px;" value=" 发送 " />
			</form>
			</div>
	</div>
	<?php
		else :
	?>
	<div id="main">
		<h1>Comet聊天演示</h1>
		<p>
		<form method="get">
			&nbsp;昵称：<input type="text" name="name" />
			<input type="submit" value=" 进入 " />
		</form>
		</p>
	</div>
	<?php endif; ?>
</body>
</html>
