
<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
	header("location: login.php?location=" . urlencode($_SERVER['REQUEST_URI']));
	exit;
}
?>
<!doctype html>
<html class="no-js" lang="">
<head>
	<meta charset="utf-8">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<title>QRCode</title>
	<meta name="description" content="Cheali QRCode generator">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<script src="scripts/qrcode.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<style type="text/css">
		#qrcode {
			width:160px;
			height:160px;
			margin-top:15px;
		}
		@media print
		{    
		    .no-print, .no-print *
		    {
		        display: none !important;
		    }
		}
	</style>
</head> 
<body>
	<div id="qrcode"></div>
	<canvas id = "newCanvas"></canvas>
	<div>
	<button onclick="print_canvasr()" class="no-print">Print</button>
	</div>
	<script type="text/javascript">
		$(document).ready(function() {
			var $_GET = getQueryParams(document.location.search);
			if($_GET["text"] != undefined) {
				var size = prompt("Select the image size",128);
				if (size == null || size == "" || isNaN(size)) {
					size = 128;
				}
				var qrcode = new QRCode("qrcode", {
					text: $_GET["text"],
					width: size,
					height: size,
					colorDark : "#000000",
					colorLight : "#ffffff",
					correctLevel : QRCode.CorrectLevel.H
				});
				console.log($("canvas"));
				var ctx = document.getElementsByTagName("canvas")[0].getContext('2d');
				var ctx2 = document.getElementById("newCanvas").getContext('2d');
				var canvas = document.getElementById("newCanvas");
				var oldCanvas = document.getElementsByTagName("canvas")[0];
				canvas.height = oldCanvas.height + 18;
				canvas.width = oldCanvas.width;
				ctx2.drawImage(document.getElementsByTagName("canvas")[0],0,0);
				ctx2.font = '14px serif';
				ctx2.textAlign = "center";
				ctx2.fillStyle = "black";
				$("#newCanvas").attr("title", $_GET["text"]);
				ctx2.fillText($_GET["label"], canvas.width/2, oldCanvas.height + 14);
				document.getElementById("qrcode").remove();
			}
		});
		function getQueryParams(qs) {
			qs = qs.split("+").join(" ");
			var params = {},
			tokens,
			re = /[?&]?([^=]+)=([^&]*)/g;

			while (tokens = re.exec(qs)) {
				params[decodeURIComponent(tokens[1])]
				= decodeURIComponent(tokens[2]);
			}

			return params;
		}
		function print_canvasr(){
			var canvas = document.getElementById("newCanvas");
			//var win1 = window.open('','','width='+canvas.width,'height='+canvas.height);
			var win1 = window.open('','');
			win1.document.write("<br><img src = '"+canvas.toDataURL()+"'/>");
			win1.document.close();
			win1.focus();
			win1.print();
		}
	</script>
</body>
</html>