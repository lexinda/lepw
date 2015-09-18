<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="Content-Type"content="text/html;charset=utf-8">
	<title><?php echo ($user); ?></title>
</head>
<body>
	我是头部
	
<?php echo ($user); ?>,<?php echo ($appId); ?>
<?php if($user == '123'): ?>123 <?php elseif($user == '456'): ?> 456<?php else: ?> 789<?php endif; ?>

	我是尾部
</body>
</html>