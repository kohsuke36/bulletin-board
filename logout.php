<?php
session_start();
$_SESSION = array();
session_destroy();
?>

<!DOCTYPE html>
<html>
<head>
<title>ログアウト</title>
</head>
<body>
<h1>
<font size='5'>ログアウトしました</font>
</h1>
<!--この下にホームのURL-->
<p><a href='https://tech-base.net/tb-250435/mission6/unlogged.php'>ホームに戻る</a></p>
</body>
</html>
