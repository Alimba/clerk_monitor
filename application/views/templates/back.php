<?php require_once('header.php'); ?>

<div id="container">
	<div id="back">
		<h4><?php echo $message_display; ?></h4>
		<input type=button class="button" value="Go Back" onclick="location.href='<?php echo base_url('index.php/main/home_admin') ?>'" />
	</div>

</div>

<?php require_once('footer.php'); ?>