<?php require_once('templates/header.php'); ?>

<div id="container">

<!-- onsubmit="getValue()" -->
<br><br>
<form name="CalcForm" AUTOCOMPLETE="OFF" action="<?php echo base_url('index.php/main/home_user'); ?>" method="post">

	<div id="login">
	    <div>
	    	<input type="text" name="calcvalues" id="calcfield" value="" size="20" maxlength="4">

	    	<input type="hidden" name="action" id="action" value="<?php echo $property; ?>"/>
	    </div>
	    <div id="row1">
		    <input type="button" name="number" value="1" id="_1" class="btns">
		    <input type="button" name="number" value="2" id="_2" class="btns">
		    <input type="button" name="number" value="3" id="_3" class="btns">
	    </div>
	    <div id="row2">
		    <input type="button" name="number" value="4" id="_4" class="btns">
		    <input type="button" name="number" value="5" id="_5" class="btns">
		    <input type="button" name="number" value="6" id="_6" class="btns">
	    </div>
	    <div id="row3">
		    <input type="button" name="number" value="7" id="_7" class="btns">
		    <input type="button" name="number" value="8" id="_8" class="btns">
		    <input type="button" name="number" value="9" id="_9" class="btns">
	    </div>
	    <div id="row4">
		    <input type="submit" name="sub" value="Submit" id="_sub" class="btns">
		    <input type="button" name="number" value="0" id="_0" class="btns">
		    <input type="button" name="number" value="Clear" id="cls" class="btns">
	    </div>
	</div>
</form>



</div>

<?php require_once('templates/footer.php'); ?>