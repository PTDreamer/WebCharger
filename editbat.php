<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php?location=" . urlencode($_SERVER['REQUEST_URI']));
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Bootstrap User Management Data Table</title>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto|Varela+Round">
        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
        <script src="scripts/common.js"></script>
        <script src="scripts/editbat.js"></script>
        <style type="text/css">
            body {
                color: #566787;
                background: #f5f5f5;
                font-family: 'Varela Round', sans-serif;
                font-size: 13px;
            }
            .table-wrapper {
                background: #fff;
                padding: 20px 25px;
                margin: 30px 0;
                border-radius: 3px;
                box-shadow: 0 1px 1px rgba(0,0,0,.05);
            }
            .table-title {
                padding-bottom: 15px;
                background: #299be4;
                color: #fff;
                padding: 16px 30px;
                margin: -20px -25px 10px;
                border-radius: 3px 3px 0 0;
            }
            .table-title h2 {
                margin: 5px 0 0;
                font-size: 24px;
            }
            .table-title .btn {
                color: #566787;
                float: right;
                font-size: 13px;
                background: #fff;
                border: none;
                min-width: 50px;
                border-radius: 2px;
                border: none;
                outline: none !important;
                margin-left: 10px;
            }
            .table-title .btn:hover, .table-title .btn:focus {
                color: #566787;
                background: #f2f2f2;
            }
            .table-title .btn i {
                float: left;
                font-size: 21px;
                margin-right: 5px;
            }
            .table-title .btn span {
                float: left;
                margin-top: 2px;
            }
            table.table tr th, table.table tr td {
                border-color: #e9e9e9;
                padding: 12px 15px;
                vertical-align: middle;
            }
            table.table tr th:first-child {
                width: 60px;
            }
            table.table tr th:last-child {
                width: 100px;
            }
            table.table-striped tbody tr:nth-of-type(odd) {
                background-color: #fcfcfc;
            }
            table.table-striped.table-hover tbody tr:hover {
                background: #f5f5f5;
            }
            table.table th i {
                font-size: 13px;
                margin: 0 5px;
                cursor: pointer;
            }	
            table.table td:last-child i {
                opacity: 0.9;
                font-size: 22px;
                margin: 0 5px;
            }
            table.table td a {
                font-weight: bold;
                color: #566787;
                display: inline-block;
                text-decoration: none;
            }
            table.table td a:hover {
                color: #2196F3;
            }
            table.table td a.settings {
                color: #2196F3;
            }
            table.table td a.delete {
                color: #F44336;
            }
            table.table td a.use_bat {
                color: #33cc33;
            }
            table.table td i {
                font-size: 19px;
            }
            table.table .avatar {
                border-radius: 50%;
                vertical-align: middle;
                margin-right: 10px;
            }
            .status {
                font-size: 30px;
                margin: 2px 2px 0 0;
                display: inline-block;
                vertical-align: middle;
                line-height: 10px;
            }
            .text-success {
                color: #10c469;
            }
            .text-info {
                color: #62c9e8;
            }
            .text-warning {
                color: #FFC107;
            }
            .text-danger {
                color: #ff5b5b;
            }
            .pagination {
                float: right;
                margin: 0 0 5px;
            }
            .pagination li a {
                border: none;
                font-size: 13px;
                min-width: 30px;
                min-height: 30px;
                color: #999;
                margin: 0 2px;
                line-height: 30px;
                border-radius: 2px !important;
                text-align: center;
                padding: 0 6px;
            }
            .pagination li a:hover {
                color: #666;
            }	
            .pagination li.active a, .pagination li.active a.page-link {
                background: #03A9F4;
            }
            .pagination li.active a:hover {        
                background: #0397d6;
            }
            .pagination li.disabled i {
                color: #ccc;
            }
            .pagination li i {
                font-size: 16px;
                padding-top: 6px
            }
            .hint-text {
                float: left;
                margin-top: 10px;
                font-size: 13px;
            }
        </style>
        <div id="menu" ></div>
    </head>
    <body>
        <div class="container maincontainer">
            <form method="post" enctype="multipart/form-data" name="mainForm">
                <div class="form-group">
                    <img id="bat_image"> 
                    <div class="custom-file mb-3">
                        <input type="file" class="custom-file-input" id="customFile" name="upfile" capture="camera">
                        <label class="custom-file-label" for="customFile">Choose file</label>
                    </div>
                </div>
                <div class="form-group custom-control custom-switch">
					  <input type="checkbox" class="custom-control-input" id="avancedSwitch" name="avancedSwitch">
					  <label class="custom-control-label" for="avancedSwitch">Show advanced</label>
				</div>
                <div class="form-group">
                    <label for="name">Name</label>
                    <textarea class="form-control"  name="name" id="name" required rows="1"></textarea>
                </div>
                <div class="form-group">
                    <label for="type">Battery type</label>
                    <select class="form-control" id="type" name="type">
                       
                    </select>
                </div>                
                <div class="form-group">
                    <label for="cells">Number of Cells</label>
                    <select class="form-control" id="cells" name="cells">
                        <option value=1>1</option>
                        <option value=2>2</option>
                        <option value=3>3</option>
                        <option value=4>4</option>
                        <option value=5>5</option>
                        <option value=6>6</option>
                    </select>
                </div>
                <div class="form-group" >
                    <label for="Vc_per_cell_forLixx">Charge voltage per cell</label>
                    <input type="number"  min="0" max="5000" class="form-control" name="Vc_per_cell" id="Vc_per_cell_forLixx" >
                </div>
                <div class="form-group" >
                    <label for="Vc_per_cell_forUnk">Charge voltage per cell</label>
                    <input type="number"  min="0" max="27000" class="form-control" name="Vc_per_cell" id="Vc_per_cell_forUnk" >
                </div>
                <div class="form-group" >
                    <label for="Vc_per_cell_forNixx">Voltage Cut Off</label>
                    <input type="number" min="1200" max="2000" class="form-control" name="Vc_per_cell" id="Vc_per_cell_forNixx" >
                </div>
                <div class="form-group" >
                    <label for="Vc_per_cell">Voltage Cut Off</label>
                    <input type="number" min="1" max="27000" class="form-control" name="Vc_per_cell" id="Vc_per_cell_forLED" >
                </div>
                <div class="form-group" >
                    <label for="Vs_per_cell">Storage voltage per cell</label>
                    <input type="number" min="0" max="5000" class="form-control" name="Vs_per_cell" id="Vs_per_cell" >
                </div>
                <div class="form-group" >
                    <label for="Vd_per_cell">Discharged voltage per cell</label>
                    <input type="number" min="0" max="5000" class="form-control" name="Vd_per_cell" id="Vd_per_cell_forUnk" >
                </div>
                <div class="form-group" >
                    <label for="Vd_per_cell">Discharged voltage per cell</label>
                    <input type="number" min="0" max="270000" class="form-control" name="Vd_per_cell" id="Vd_per_cell" >
                </div>
                <div class="form-group" >
                    <label for="capacity">Capacity (mAh)</label>
                    <input type="number" min="1000" max="65000" class="form-control" name="capacity" id="capacity" >
                </div>
                <div class="form-group" >
                    <label for="Ic">Charge current (mAh)</label>
                    <input type="number" min="1" max="5000" class="form-control" name="Ic" id="Ic" >
                </div>
                <div class="form-group" >
                    <label for="minIc">Minimum charge current (mAh)</label>
                    <input type="number" min="1" max="5000" class="form-control" name="minIc" id="minIc" >
                </div>
                <div class="form-group" >
                    <label for="Id">Discharge current (mAh)</label>
                    <input type="number" min="1" max="1000" class="form-control" name="Id" id="Id" >
                </div>
                <div class="form-group" >
                    <label for="minId">Minimum discharge current (mAh)</label>
                    <input type="number" min="1" max="1000" class="form-control" name="minId" id="minId" >
                </div>
                <div class="form-group" >
                    <label for="balancer_error">Balancer error (mV)</label>
                    <input type="number" min="3" max="200" class="form-control" name="balancer_error" id="balancer_error" >
                </div>
                <div class="form-group custom-control custom-switch" >
                    <input type="checkbox" class="custom-control-input" name="delta_v_enable" id="delta_v_enable">
                    <label class="custom-control-label" for="delta_v_enable">Enable delta V</label>
                </div>
                <div class="form-group" >
                    <label for="delta_v">Delta V (mV)</label>
                    <input type="number" min="-20" max="0" class="form-control" name="delta_v" id="delta_v" >
                </div>
                <div class="form-group" >
                    <label for="delta_v_ignore_t">Ignore first (m)</label>
                    <input type="number" min="1" max="30" class="form-control" name="delta_v_ignore_t" id="delta_v_ignore_t" >
                </div>
                <div class="form-group custom-control custom-switch" >
                	<input type="checkbox" class="custom-control-input" name="enable_externT" id="enable_externT">
                    <label class="custom-control-label" for="enable_externT">Enable external temp sensor</label>
                </div>
                <div class="form-group" >
                    <label for="delta_t">Maximum external temperature difference per minute (C/m)</label>
                    <input type="number" min="0.1" max="9" class="form-control" step="0.1" name="delta_t" id="delta_t" >
                </div>
                <div class="form-group" >
                    <label for="externTCO">Minimum external temperature cutoff CHECK MIN MAX of all temps</label>
                    <input type="number" min="1" max="99" class="form-control" name="externTCO" id="externTCO" >
                </div>
                <div class="form-group" >
                    <label for="DCRestTime">DC rest time</label>
                    <input type="number" min="1" max="99" class="form-control" name="DCRestTime" id="DCRestTime" >
                </div>
                 <div class="form-group custom-control custom-switch" >
                 	<input type="checkbox" class="custom-control-input" name="enable_adaptiveDischarge" id="enable_adaptiveDischarge">
                    <label class="custom-control-label" for="enable_adaptiveDischarge">Enable adaptive discharge</label>
                </div>
                <div class="form-group" >
                    <label for="dc_cycles">DC cycles</label>
                    <input type="number" min="0" max="5" class="form-control" name="dc_cycles" id="dc_cycles" >
                </div>
                <div class="form-group" >
                    <label for="capCutoff">Capacity cutoff</label>
                    <input type="number" min="0" max="9999999" class="form-control" name="capCutoff" id="capCutoff" >
                </div>
                <div class="form-group" >
                    <label for="time">Time Limit</label>
                    <input type="number" min="0" max="1000" class="form-control" name="time" id="time" >
                </div>
                <div class="form-group">
                    <label for="subtypeIndex">Battery subtype</label>
                    <select class="form-control" id="subtypeIndex" name="subtype"></select>
                </div> 
                <div class="form-group">
                    <label for="charge_state">Charge state</label>
                    <select class="form-control" id="charge_state" name="charge_state"></select>
                </div> 
                <div class="form-group">
                    <label for="use_state">Use state</label>
                    <select class="form-control" id="use_state" name="use_state"></select>
                </div> 
                <div class="form-group" >
                    <label for="last_capacity">Last known capacity</label>
                    <input type="number" min="0" class="form-control" name="last_capacity" id="last_capacity" >
                </div>
                <div class="form-group" >
                    <label for="part_of_pack_id">Part of pack with ID</label>
                    <input type="number" min="0" class="form-control" name="part_of_pack_id" id="part_of_pack_id" >
                </div>
                <div class="form-group custom-control custom-switch" >
                    <input type="checkbox" class="custom-control-input" name="isPack" id="isPack">
                    <label class="custom-control-label" for="isPack">Is a pack</label>
                </div>
                <button type="submit" class="btn btn-primary">Save</button>
                <button type="submit" name="new" class="btn btn-primary">Save as new battery</button>
                <button id="qrgen" type="" class="btn btn-secondary">Generate QRCode</button>
            </form>
        </div>  
    </div>   
    <script src="https://ajax.aspnetcdn.com/ajax/jquery/jquery-1.9.0.min.js"></script>
    <script type="text/javascript">
        // Add the following code if you want the name of the file appear on select
        $(".custom-file-input").on("change", function () {
            var fileName = $(this).val().split("\\").pop();
            $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
        });

        $( "#qrgen" ).click(function() {
  			  	var win = window.open('qrcode.php?text='+window.location.href+"&label="+$("#name").val(), '_blank');
  				win.focus();
		});

        document.getElementById("customFile").addEventListener("change", function (event) {
            compress(event);
        });

		$( "#type" ).on( "change", function( event ) {
			loadBattery();
			changedType();
		    saveBattery();
		    checkVisible();
		});
		$( "#capacity" ).on( "change", function( event ) {
			loadBattery();
			changedCapacity();
		    saveBattery();
		    checkVisible();
		});
		$( "#Ic" ).on( "change", function( event ) {
			loadBattery();
			changedIc();
		    saveBattery();
		    checkVisible();
		});
		$( "#Id" ).on( "change", function( event ) {
			loadBattery();
			changedId();
		    saveBattery();
		    checkVisible();
		});
		$( "#avancedSwitch" ).on( "change", function( event ) {
			loadBattery();
			setAdvanced($(this).prop("checked") == true);
		    checkVisible();
		});
		$( "#enable_externT" ).on( "change", function( event ) {
			loadBattery();
		    checkVisible();
		});
		$( "#delta_v_enable" ).on( "change", function( event ) {
			loadBattery();
		    checkVisible();
		});
        afterLoad();
    </script>
</body>
</html>                                		                            