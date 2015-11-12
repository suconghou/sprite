<!DOCTYPE html>
<html lang="en">
<head>
	<?=base::meta('Css Sprite')?>
	<?=base::css('style')?>
</head>
<body>
	<header class="header">
		
	</header>
	<div class="wrapper">
		<div class="uploadForm">
			<p>
				<input type="text" id="store-project">
				<input type="text" id="store-name">
				<input type="text" id="store-comment">
				<textarea id="store-css"></textarea>
				<input type="file" id="store-img">
				<button onclick="app.sprite.test()">TEST</button>
			</p>
		</div>
	</div>
	<footer class="footer">
		<?=base::js('jquery-main')?>
	</footer>
</body>
</html>