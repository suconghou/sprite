<!DOCTYPE html>
<html lang="en">
<head>
	<?=base::meta('project')?>
	<?=base::css('style')?>
	<style type="text/css"><?=$data['css']?></style>
</head>
<body>
	<header class="header">
		
	</header>
	<div class="wrapper">
		<div class="sprite-example">
			<ul class="icon-list">
				<?php foreach($data['class'] as $item):?>
				<li title="icon-<?=substr($item,1)?>">
					<i class="icon icon-<?=substr($item,1)?>"></i>
				</li>
				<?php endforeach?>
			</ul>
		</div>
	</div>
	<footer class="footer">
		<?=base::js('jquery-main')?>
	</footer>
</body>
</html>