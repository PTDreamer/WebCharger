<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}
require_once "config.php";

$COND_NiXX = 1;
$COND_Pb = 2;
$COND_LiXX = 4;
$COND_NiZn = 8;
$COND_Unknown = 16;
$COND_LED = 32;

$COND_enableT = 256;
$COND_enable_dV = 512;
$COND_enable_dT = 1024;
$COND_advanced = 32768;

$COND_LiXX_NiZn = ($COND_LiXX + $COND_NiZn);
$COND_LiXX_NiZn_Pb = ($COND_LiXX + $COND_NiZn + $COND_Pb);
$COND_LiXX_NiZn_Pb_Unkn = ($COND_LiXX + $COND_NiZn + $COND_Pb + $COND_Unknown);
$COND_NiXX_Pb = ($COND_NiXX + $COND_Pb);

$COND_BATTERY = ($COND_NiXX + $COND_Pb + $COND_LiXX + $COND_NiZn + $COND_Unknown);
$COND_BATT_UNKN = ($COND_NiXX + $COND_Pb + $COND_LiXX + $COND_NiZn);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    print_r($_POST["name"]);
    print_r($_FILES["upfile"]["name"]);
    $name = $_POST["name"];
    $type = $_POST["type"];
    $cells = $_POST["cells"];
    $Vc_per_cell = $_POST["Vc_per_cell"];
    $Vs_per_cell = $_POST["Vs_per_cell"];
    $Vd_per_cell = $_POST["Vd_per_cell"];
    $capacity = $_POST["capacity"];
    $Ic = $_POST["Ic"];
    $minIc = $_POST["minIc"];
    $Id = $_POST["Id"];
    $minId = $_POST["minId"];
    $balancerError = $_POST["balancerError"];
    $deltaV = $_POST["deltaV"];
    $deltaVIgnoreTime = $_POST["deltaVIgnoreTime"];
    $deltaT = $_POST["deltaT"];
    $externTCO = $_POST["externTCO"];
    $DCRestTime = $_POST["DCRestTime"];
    $DCcycles = $_POST["DCcycles"];
    $capCutoff = $_POST["capCutoff"];
    $time = $_POST["time"];
    if(isset($_FILES["myfile"])) {
    	move_uploaded_file($_FILES["myfile"]["tmp_name"], "images/".$_GET['id'].".jpeg");
    	die("could no move file");
    }
    if(isset($_POST['enable_deltaV'])) {
    	$enable_deltaV = 1;
    }
    else {
    	$enable_deltaV = 0;
    }
        if(isset($_POST['enable_externT'])) {
    	$enable_externT = 1;
    }
    else {
    	$enable_externT = 0;
    }
        if(isset($_POST['enable_adaptiveDischarge'])) {
    	$enable_adaptiveDischarge = 1;
    }
    else {
    	$enable_adaptiveDischarge = 0;
    }
	$idx = $_GET['id'];
    $sql = 'UPDATE batteries SET name = ?, type = ?, capacity = ?, cells = ?, Ic = ?, Id = ?, Vc_per_cell = ?, Vd_per_cell = ?, minIc = ?, minId = ?, time = ?, enable_externT = ?, externTCO = ?, enable_adaptiveDischarge = ?, DCRestTime = ?, capCutoff = ?, Vs_per_cell = ?, balancer_error = ?, delta_v_enable = ?, delta_v = ?, delta_v_ignore_t = ?, delta_t = ?, dc_cycles = ? WHERE idx = ?';
    if ($stmt = mysqli_prepare($link, $sql)) {
    	$stmt->bind_param("siiiiiiiiiiiiiiiiiiiiiii",$name, $type, $capacity, $cells, $Ic, $Id, $Vc_per_cell, $Vd_per_cell, $minIc, $minId, $time, $enable_externT, $externTCO, $enable_adaptiveDischarge, $DCRestTime, $capCutoff, $Vs_per_cell, $balancerError, $enable_deltaV, $deltaV, $deltaVIgnoreTime, $deltaT, $DCcycles, $idx);
    	$stmt->execute();
		if ($stmt->error) {
		  die("FAILURE!!! " . $stmt->error);
		}
		$stmt->close();
    } else {
    	die('prepare() failed: ' . htmlspecialchars($mysqli->error));
    }
}
$id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int) $_GET['id'] : 1;
$sql = 'SELECT * FROM batteries WHERE idx = ?';
if ($stmt = mysqli_prepare($link, $sql)) {
    // Bind variables to the prepared statement as parameters
    mysqli_stmt_bind_param($stmt, "i", $id);
    if ($stmt->execute()) {
        $stmt->bind_result($idx, $name, $type, $capacity, $cells, $Ic, $Id, $Vc_per_cell, $Vd_per_cell, $minIc, $minId, $time, $enable_externT, $externTCO, $enable_adaptiveDischarge, $DCRestTime, $capCutoff, $Vs_per_cell, $balancerError, $enable_deltaV, $deltaV, $deltaVIgnoreTime, $deltaT, $DCcycles, $date_created);
        $stmt->fetch();
        $stmt->close();
    } 
}
// Fetch the records so we can display them in our template.
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
        <script src="cheali_scripts.js"></script>
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
    </head>
    <body>
        <div class="container">
            <form method="post" enctype="multipart/form-data" name="mainForm">
                <div class="form-group">
                    <?php
                    $imgs = glob("./images/$idx.{jpg,jpeg,png,gif}", GLOB_BRACE);
                    if (empty($imgs))
                        $img = "/images/no-image-available.jpg";
                    else
                        $img = $imgs[0];
                    ?>
                    <img id="bat_image" src=<?php echo $img ?>> <?php $name ?> 
                    <div class="custom-file mb-3">
                        <input type="file" class="custom-file-input" id="customFile" name="upfile">
                        <label class="custom-file-label" for="customFile">Choose file</label>
                    </div>
                </div>
                <div class="form-group custom-control custom-switch">
					  <input type="checkbox" class="custom-control-input" id="avancedSwitch" name="avancedSwitch">
					  <label class="custom-control-label" for="avancedSwitch">Show advanced</label>
				</div>
                <div class="form-group">
                    <label for="name">Name</label>
                    <textarea class="form-control"  name="name" id="name" required rows="1" value=><?php echo $name; ?></textarea>
                </div>
                <div class="form-group">
                    <label for="type">Battery type</label>
                    <select class="form-control" id="type" name="type">
                        <?php
                        $sql = 'SELECT * FROM battery_types';
                        if ($stmt = mysqli_prepare($link, $sql)) {
                            if ($stmt->execute()) {
                                $stmt->bind_result($bat_type, $bat_str);
                                while ($stmt->fetch()) {
                                    ?>
                                    <option value=<?php echo $bat_type;?> <?php if ($type == $bat_type) echo "selected" ?>> <?php echo $bat_str; ?></option>
                        <?php   }
                            }
                        };?>
                    </select>
                </div>                
                <div class="form-group">
                    <label for="cells">Number of Cells</label>
                    <select class="form-control" id="cells" name="cells">
                        <option value="1 " <?php if ($cells == 1) echo "selected" ?>>1</option>
                        <option value="2 " <?php if ($cells == 2) echo "selected" ?>>2</option>
                        <option value="3 " <?php if ($cells == 3) echo "selected" ?>>3</option>
                        <option value="4 " <?php if ($cells == 4) echo "selected" ?>>4</option>
                        <option value="5 " <?php if ($cells == 5) echo "selected" ?>>5</option>
                        <option value="6 " <?php if ($cells == 6) echo "selected" ?>>6</option>
                    </select>
                </div>
                <div class="form-group" condition=<?php echo ($COND_advanced + $COND_LiXX_NiZn_Pb) ?>>
                    <label for="Vc_per_cell">Charge voltage per cell</label>
                    <input type="number"  min="0" max="5000" class="form-control" name="Vc_per_cell" id="Vc_per_cell_forLixx" value=<?php echo $Vc_per_cell ?>>
                </div>
                <div class="form-group" condition=<?php echo ($COND_Unknown) ?>>
                    <label for="Vc_per_cell">Charge voltage per cell</label>
                    <input type="number"  min="0" max="27000" class="form-control" name="Vc_per_cell" id="Vc_per_cell_forUnk" value=<?php echo $Vc_per_cell ?>>
                </div>
                <div class="form-group" condition=<?php echo ($COND_advanced + $COND_NiXX) ?>>
                    <label for="Vc_per_cell">Voltage Cut Off</label>
                    <input type="number" min="1200" max="2000" class="form-control" name="Vc_per_cell" id="Vc_per_cell_forNixx" value=<?php echo $Vc_per_cell ?>>
                </div>
                <div class="form-group" condition=<?php echo ($COND_LED) ?>>
                    <label for="Vc_per_cell">Voltage Cut Off</label>
                    <input type="number" min="1" max="27000" class="form-control" name="Vc_per_cell" id="Vc_per_cell_forLED" value=<?php echo $Vc_per_cell ?>>
                </div>
                <div class="form-group" condition=<?php echo ($COND_advanced + $COND_LiXX) ?>>
                    <label for="Vs_per_cell">Storage voltage per cell</label>
                    <input type="number" min="0" max="5000" class="form-control" name="Vs_per_cell" id="Vs_per_cell" value=<?php echo $Vs_per_cell ?>>
                </div>
                <div class="form-group" condition=<?php echo ($COND_BATT_UNKN + $COND_advanced) ?>>
                    <label for="Vd_per_cell">Discharged voltage per cell</label>
                    <input type="number" min="0" max="5000" class="form-control" name="Vd_per_cell" id="Vd_per_cell_forUnk" value=<?php echo $Vd_per_cell ?>>
                </div>
                <div class="form-group" condition=<?php echo ($COND_Unknown) ?>>
                    <label for="Vd_per_cell">Discharged voltage per cell</label>
                    <input type="number" min="0" max="270000" class="form-control" name="Vd_per_cell" id="Vd_per_cell" value=<?php echo $Vd_per_cell ?>>
                </div>
                <div class="form-group" condition=<?php echo ($COND_BATTERY) ?>>
                    <label for="capacity">Capacity (mAh)</label>
                    <input type="number" min="1000" max="65000" class="form-control" name="capacity" id="capacity" value=<?php echo $capacity ?>>
                </div>
                <div class="form-group" condition=<?php echo ($COND_BATTERY + $COND_LED) ?>>
                    <label for="Ic">Charge current (mAh)</label>
                    <input type="number" min="1" max="5000" class="form-control" name="Ic" id="Ic" value=<?php echo $Ic ?>>
                </div>
                <div class="form-group" condition=<?php echo ($COND_advanced + $COND_LiXX_NiZn_Pb_Unkn) ?>>
                    <label for="minIc">Minimum charge current (mAh)</label>
                    <input type="number" min="1" max="5000" class="form-control" name="minIc" id="minIc" value=<?php echo $minIc ?>>
                </div>
                <div class="form-group" condition=<?php echo ($COND_BATTERY) ?>>
                    <label for="Id">Discharge current (mAh)</label>
                    <input type="number" min="1" max="1000" class="form-control" name="Id" id="Id" value=<?php echo $Id ?>>
                </div>
                <div class="form-group" condition=<?php echo ($COND_advanced + $COND_BATTERY) ?>>
                    <label for="minId">Minimum discharge current (mAh)</label>
                    <input type="number" min="1" max="1000" class="form-control" name="minId" id="minId" value=<?php echo $minId ?>>
                </div>
                <div class="form-group" condition=<?php echo ($COND_advanced + $COND_LiXX_NiZn) ?>>
                    <label for="balancerError">Balancer error (mV)</label>
                    <input type="number" min="3" max="200" class="form-control" name="balancerError" id="balancerError" value=<?php echo $balancerError ?>>
                </div>
                <div class="form-group custom-control custom-switch" condition=<?php echo ($COND_NiXX) ?>>
                    <input type="checkbox" class="custom-control-input" name="enable_deltaV" id="enable_deltaV" <?php if($enable_deltaV) echo " checked" ?>>
                    <label class="custom-control-label" for="enable_deltaV">Enable delta V</label>
                </div>
                <div class="form-group" condition=<?php echo ($COND_enable_dV) ?>>
                    <label for="deltaV">Delta V (mV)</label>
                    <input type="number" min="-20" max="0" class="form-control" name="deltaV" id="deltaV" value=<?php echo $deltaV ?>>
                </div>
                <div class="form-group" condition=<?php echo ($COND_enable_dV) ?>>
                    <label for="deltaVIgnoreTime">Ignore first (m)</label>
                    <input type="number" min="1" max="30" class="form-control" name="deltaVIgnoreTime" id="deltaVIgnoreTime" value=<?php echo $deltaVIgnoreTime ?>>
                </div>
                <div class="form-group custom-control custom-switch" condition=<?php echo ($COND_BATTERY) ?>>
                	<input type="checkbox" class="custom-control-input" name="enable_externT" id="enable_externT" <?php if($enable_externT) echo " checked" ?>>
                    <label class="custom-control-label" for="enable_externT">Enable external temp sensor</label>
                </div>
                <div class="form-group" condition=<?php echo ($COND_enable_dT) ?>>
                    <label for="deltaT">Maximum external temperature difference per minute (C/m)</label>
                    <input type="number" min="0.1" max="9" class="form-control" step="0.1" name="deltaT" id="deltaT" value=<?php echo $deltaT / 100 ?>>
                </div>
                <div class="form-group" condition=<?php echo ($COND_enableT) ?>>
                    <label for="externTCO">Minimum external temperature cutoff CHECK MIN MAX of all temps</label>
                    <input type="number" min="1" max="99" class="form-control" name="externTCO" id="externTCO" value=<?php echo $externTCO / 100 ?>>
                </div>
                <div class="form-group" condition=<?php echo ($COND_advanced + $COND_BATTERY) ?>>
                    <label for="DCRestTime">DC rest time</label>
                    <input type="number" min="1" max="99" class="form-control" name="DCRestTime" id="DCRestTime" value=<?php echo $DCRestTime?>>
                </div>
                 <div class="form-group custom-control custom-switch" condition=<?php echo ($COND_advanced + $COND_BATTERY) ?>>
                 	<input type="checkbox" class="custom-control-input" name="enable_adaptiveDischarge" id="enable_adaptiveDischarge" <?php if($enable_adaptiveDischarge) echo " checked" ?>>
                    <label class="custom-control-label" for="enable_adaptiveDischarge">Enable adaptive discharge</label>
                </div>
                <div class="form-group" condition=<?php echo ($COND_advanced + $COND_BATTERY) ?>>
                    <label for="DCcycles">DC cycles</label>
                    <input type="number" min="0" max="5" class="form-control" name="DCcycles" id="DCcycles" value=<?php echo $DCcycles?>>
                </div>
                <div class="form-group" condition=<?php echo ($COND_BATTERY) ?>>
                    <label for="capCutoff">Capacity cutoff</label>
                    <input type="number" min="1" max="250" class="form-control" name="capCutoff" id="capCutoff" value=<?php echo $capCutoff?>>
                </div>
                <div class="form-group" condition=<?php echo ($COND_BATTERY) ?>>
                    <label for="time">Time Limit</label>
                    <input type="number" min="0" max="1000" class="form-control" name="time" id="time" value=<?php echo $time?>>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
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

        $("#picture button").on("click", function (event) {

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
		$( "#enable_deltaV" ).on( "change", function( event ) {
			loadBattery();
		    checkVisible();
		});
        afterLoad();
    </script>
</body>
</html>                                		                            