<html>
<head>
	<title><?php echo $name . ' Page'; ?></title>
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('css/style.css'); ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('css/print.css'); ?>">

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

	<script type="text/javascript">

		onload=function(){ attachHandlers(); }

		function attachHandlers(){
		  var the_nums = document.getElementsByName("number");
		  for (var i=0; i < the_nums.length; i++) { 
		  		the_nums[i].onclick=inputNumbers; 
		  }
		}

		function inputNumbers() {
		  var the_field = document.getElementById('calcfield');
		  var the_value = this.value;
		  switch (the_value) {
		    case 'Clear' :
		      the_field.value = '';
		      break;
		    default : document.getElementById("calcfield").value += the_value;
		      break;
		  }
		  document.getElementById('calcfield').focus();
		  return true;
		}

	</script>


<link rel="stylesheet" type="text/css" href="<?php echo base_url('css/style.css'); ?>">
	

	<script language="javascript" type="text/javascript">
       function printDiv(divID) {
           //Get the HTML of div
           var divElements = document.getElementById(divID).innerHTML;
           //Get the HTML of whole page
           var oldPage = document.body.innerHTML;

           //Reset the page's HTML with div's HTML only
           document.body.innerHTML = 
             "<html><head><title></title></head><body><div id='print'>" + 
             divElements + "</div></body>";

           //Print Page
           window.print();

           //Restore orignal HTML
           document.body.innerHTML = oldPage;
       }
	</script>


</head>
<body>
	<div id="header">
		<nav><?php 
				echo '<h4 id="welcome">Hello <b><i>' . $name . '</i>! </b><br></h4>';

				if ($name == 'Admin')
				{
					echo '<a href="' . base_url('index.php/main/home_admin') . '">Home</a>';
					echo '<a href="' . base_url('index.php/main/salons/1') . '">Soho</a>';
					echo '<a href="' . base_url('index.php/main/salons/2') . '">Charing Cross</a>';
					echo '<a href="' . base_url('index.php/main/salons/3') . '">Goodge Street</a>';
					echo '<a href="' . base_url('index.php/main/salons/4') . '">New Row</a>';
					echo '<a href="' . base_url('index.php/main/salons/5') . '">Office</a>';
					echo '<a href="' . base_url('index.php/reports_controller/reports') . '">Reports</a>';
				} 
				else 
				{
					echo '<a href="' . base_url('index.php/main/home_user') . '">Home</a>';
				}

				echo '<a href="' . base_url('index.php/user_authentication/logout') . '">Logout</a>'; 
			?>
		</nav>		
	</div>

	<div id="wraper">

