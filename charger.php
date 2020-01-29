<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
	header("location: login.php?location=" . urlencode($_SERVER['REQUEST_URI']));
	exit;
}
require_once "config.php";
?>
<!doctype html>
<html class="no-js" lang="">
<head>
	<meta charset="utf-8">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<title></title>
	<meta name="description" content="">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link rel="manifest" href="site.webmanifest">
	<link rel="apple-touch-icon" href="icon.png">
	<!-- Place favicon.ico in the root directory -->

	<link rel="stylesheet" href="css/normalize.css">
	<link rel="stylesheet" href="css/main.css">
	<link rel="stylesheet" href="css/charger.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
	<div id="menu" ></div>
</head>
<body>
        <!--[if lte IE 9]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="https://browsehappy.com/">upgrade your browser</a> to improve your experience and security.</p>
        <![endif]-->

        <!-- Add your site or application content here -->
        <div class="container maincontainer">
        	<div class="table-wrapper">
        		<div class="table-title">
        			<div class="row table-title-first">
        				<div class="col-sm-6">
        					<h2><b>Battery Name</b></h2>
        				</div>
        				<div class="col-sm-2">
        					<div class = "row">
        						<a><span>Status</span></a>
        					</div>
        					<div class = "row">
        						<h3><span class="badge badge-secondary">New</span></h3>
        					</div>
        				</div>
        				<div class="col-sm-2">
        					<div class = "row">
        						<a><span>Error</span></a>
        					</div>
        					<div class = "row">
        						<h3><span class="badge badge-secondary">New</span></h3>
        					</div>
        				</div>
        				<div class="col-sm-2">
        					<div class = "row">
        						<a><span>Program</span></a>
        					</div>
        					<div class = "row">
        						<h3><span class="badge badge-secondary">New</span></h3>
        					</div>
        				</div>
        			</div>
        		</div>
        	</div>
        	<a class="btn btn-primary" data-toggle="collapse" href="#settings" role="button" aria-expanded="false" aria-controls="settings"><i class="material-icons">settings_applications</i> <span>Load settings</span></a> 
        	<div class="container collapse" id="settings">
                    <hr />
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Backlight:</span>
                                </div>
                                <input type="number" id="backlight" class="form-control" aria-label="With textarea"></input>
                            </div>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Fan temperature on</span>
                                </div>
                                <input type="number" id="fan_temperature_on" class="form-control" aria-label="With textarea"></input>
                            </div>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Discharge temp. off</span>
                                </div>
                                <input type="number" id="discharge_temperature_off" class="form-control" aria-label="With textarea"></input>
                            </div>
                             <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Input voltage low</span>
                                </div>
                                <input type="number" id="input_voltage_low" class="form-control" aria-label="With textarea"></input>
                            </div>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Minimum Ic</span>
                                </div>
                                <input type="number" id="minimum_ic" class="form-control" aria-label="With textarea"></input>
                            </div>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Minimum Id</span>
                                </div>
                                <input type="number" id="minimum_id" class="form-control" aria-label="With textarea"></input>
                            </div>


                             <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Maximum Ic</span>
                                </div>
                                <input type="number" id="maximum_ic" class="form-control" aria-label="With textarea"></input>
                            </div>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Maximum Id</span>
                                </div>
                                <input type="number" id="maximum_id" class="form-control" aria-label="With textarea"></input>
                            </div>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Maximum Pc</span>
                                </div>
                                <input type="number" id="maximum_pc" class="form-control" aria-label="With textarea"></input>
                            </div>
                        </div>
                         <div class="col-sm-6">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Fan on</span>
                                </div>
                                <select class="form-control" id="fan_on">
                                	<option value = 0>Disable</option>
                                	<option value = 1>Always</option>
                                	<option value = 2>Program</option>
                                	<option value = 3>Temperature</option>
                                	<option value = 4>Program temperature</option>
                                </select>
                            </div>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">UART</span>
                                </div>
                                <select class="form-control" id="uart">
                                	<option value = 0>Disable</option>
                                	<option value = 1>Normal</option>
                                	<option value = 2>ExtControl</option>
                                	<option value = 3>Debug</option>
                                	<option value = 4>ExtDebug</option>
                                	<option value = 5>ExtDebugADC</option>
                                </select>
                            </div>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">UART Speed</span>
                                </div>
                                <select class="form-control" id="uart_speed">
                                	<option value = 0>9600</option>
                                	<option value = 1>19200</option>
                                	<option value = 2>38400</option>
                                	<option value = 3>57600</option>
                                	<option value = 4>115200</option>
                                </select>
                            </div> 



                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">UART output</span>
                                </div>
                                <select class="form-control" id="uart_output">
                                	<option value = 0>Temperature output</option>
                                	<option value = 1>Separated</option>
                                </select>
                            </div>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Menu Type</span>
                                </div>
                                <select class="form-control" id="menu_type">
                                	<option value = 0>Simple</option>
                                	<option value = 1>Advanced</option>
                                </select>
                            </div>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Menu buttons</span>
                                </div>
                                <select class="form-control" id="menu_buttons">
                                	<option value = 0>Normal</option>
                                	<option value = 1>Reversed</option>
                                </select>
                            </div>
                            <div class="input-group">
	                            <span class="button-checkbox">
	                                <button type="button" class="btn  mt-2" data-color="info">Audio beep</button>
	                                <input id="audio_beep" type="checkbox" class="d-none" />
	                            </span>
                        	</div>
                        	<div class="input-group">
	                            <span class="button-checkbox">
	                                <button type="button" class="btn  mt-2" data-color="info">ADC noise</button>
	                                <input id="adc_noise" type="checkbox" class="d-none" />
	                            </span>
	                        </div> 
                        </div>
                    </div>
           	</div>
        </div>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
        crossorigin="anonymous"></script>
        <script>window.jQuery || document.write('<script src="scripts/vendor/jquery-3.4.1.min.js"><\/script>')</script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
        <script src="scripts/common.js"></script>
        <script src="scripts/charger.js"></script>
    </body>
    </html>