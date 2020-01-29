<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit();
}
require_once "config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $error = "none";
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
    $balancerError = $_POST["balancer_error"];
    $deltaV = $_POST["delta_v"];
    $deltaVIgnoreTime = $_POST["delta_v_ignore_t"];
    $deltaT = $_POST["delta_t"];
    $externTCO = $_POST["externTCO"];
    $DCRestTime = $_POST["DCRestTime"];
    $DCcycles = $_POST["dc_cycles"];
    $capCutoff = $_POST["capCutoff"];
    $time = $_POST["time"];

    $subtype = $_POST["subtype"];
    $charge_state = $_POST["charge_state"];
    $use_state = $_POST["use_state"];
    $last_capacity = $_POST["last_capacity"];
    $part_of_pack_id = $_POST["part_of_pack_id"];
    $isPack = $_POST["isPack"];

    if(isset($_POST['delta_v_enable'])) {
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
    if(isset($_POST['isPack'])) {
        $isPack = 1;
    }
    else {
        $isPack = 0;
    }
    if(!isset($_POST['externTCO'])) {
        $externTCO = 6000;
    }
    if(!isset($_POST['deltaV'])) {
        $deltaV = 8;
    }
    if(!isset($_POST['deltaVIgnoreTime'])) {
        $deltaVIgnoreTime = 3;
    }
    if(!isset($_POST['deltaT'])) {
        $deltaT = 100;
    }
	$idx = $_GET['id'];
    if(!isset($_POST['new'])) {
        if(isset($_FILES["myfile"])) {
            if(!move_uploaded_file($_FILES["myfile"]["tmp_name"], "images/".$_GET['id'].".jpeg"))
                $error = "could not move file";
        }

        $sql = 'UPDATE batteries SET name = ?, type = ?, capacity = ?, cells = ?, Ic = ?, Id = ?, Vc_per_cell = ?, Vd_per_cell = ?, minIc = ?, minId = ?, time = ?, enable_externT = ?, externTCO = ?, enable_adaptiveDischarge = ?, DCRestTime = ?, capCutoff = ?, Vs_per_cell = ?, balancer_error = ?, delta_v_enable = ?, delta_v = ?, delta_v_ignore_t = ?, delta_t = ?, dc_cycles = ?, subtype = ?, charge_state = ?, use_state = ?, last_capacity = ?, part_of_pack_id = ?, isPack = ?
         WHERE idx = ?';
        if ($stmt = mysqli_prepare($link, $sql)) {
            $stmt->bind_param("siiiiiiiiiiiiiiiiiiiiiiiiiiiii",$name, $type, $capacity, $cells, $Ic, $Id, $Vc_per_cell, $Vd_per_cell, $minIc, $minId, $time, $enable_externT, $externTCO, $enable_adaptiveDischarge, $DCRestTime, $capCutoff, $Vs_per_cell, $balancerError, $enable_deltaV, $deltaV, $deltaVIgnoreTime, $deltaT, $DCcycles, $subtype, $charge_state, $use_state, $last_capacity , $part_of_pack_id, $isPack, $idx);
            $stmt->execute();
            if ($stmt->error) {
              $error = "Could not update record:" . $stmt->error;
            }
            $stmt->close();
        } else {
            $error = "Could not update record:" . htmlspecialchars($mysqli->error);
        }
        if($error != "none") {
            $result["error"] = $error;
            echo json_encode($result);
            exit();
        }
    }
    else {//23
        $sql = 'INSERT INTO batteries (name, type, capacity, cells, Ic, Id, Vc_per_cell, Vd_per_cell, minIc, minId, time, enable_externT, externTCO, enable_adaptiveDischarge, DCRestTime, capCutoff, Vs_per_cell, balancer_error, delta_v_enable, delta_v, delta_v_ignore_t, delta_t, dc_cycles) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';        
        if ($stmt = mysqli_prepare($link, $sql)) {
            $stmt->bind_param("siiiiiiiiiiiiiiiiiiiiii",$name, $type, $capacity, $cells, $Ic, $Id, $Vc_per_cell, $Vd_per_cell, $minIc, $minId, $time, $enable_externT, $externTCO, $enable_adaptiveDischarge, $DCRestTime, $capCutoff, $Vs_per_cell, $balancerError, $enable_deltaV, $deltaV, $deltaVIgnoreTime, $deltaT, $DCcycles);
            $stmt->execute();
            $idx = $stmt->insert_id;
            if ($stmt->error) {
              $error = "Could not create record:" . $stmt->error;
            }
            $stmt->close();
        } else {
            $error = "Could not create record:" . htmlspecialchars($mysqli->error);
        }
        if($error != "none") {
            $result["error"] = $error;
            echo json_encode($result);
            exit();
        }
    }
}
if(isset($_GET['createNew'])) {
    $error = "none";
    $newID = 0;
    $name = 'New Battery';
    $result = array();
    $sql = 'INSERT INTO batteries (name) VALUES (?)';        
    if ($stmt = mysqli_prepare($link, $sql)) {
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $newID = $stmt->insert_id;
        if ($stmt->error) {
            $error = "Could not create record:" . $stmt->error;
            $stmt->close();
            $result["error"] = $error;
            print json_encode($result);
            exit();
        }
    } 
    else {
        $error = "Could not create record:" . htmlspecialchars(mysqli_error($link));
        $stmt->close();
    }
    if($error != "none") {
        $result["error"] = $error;
        print json_encode($result);
        exit();
    }
    else {
        $result["error"] = $error;
        $result["newID"] = $newID;
        print json_encode($result);
        exit();
    }
}
if(isset($_GET['page']) && is_numeric($_GET['page'])) {
    $result = array();
    $error = "none";
    $rows = array();
    $records_per_page = 5;
    $page = $_GET['page'];
    $name = "%";
    $idd = array();
    if(isset($_GET['name']))
        $name = "%".$_GET['name']."%";
    else
        $name = "%";
    if(isset($_GET['idd'])) {
        $_idds = explode(",", $_GET['idd']);
        foreach($_idds as $_idd) {
        $_idd = trim($_idd);
        $idd[] = $_idd;
    }
    }
    $par1 = "s";
    $refArr = array();
    $refArr[] = $name;
    $sql = 'SELECT COUNT(*) FROM batteries WHERE (name LIKE ?)';
    $idxstr ="";
    if(isset($_GET['type'])) {
        $idxstr = $idxstr . ' AND type=? ';
        $par1 = $par1 . "i";
        $refArr[] = $_GET['type'];
    }
    if(isset($_GET['subtype'])) {
        $idxstr = $idxstr . ' AND subtype=? ';
        $par1 = $par1 . "i";
        $refArr[] = $_GET['subtype'];
    }
    if(isset($_GET['isPack'])) {
        $idxstr = $idxstr . ' AND isPack=? ';
        $par1 = $par1 . "i";
        $refArr[] = $_GET['isPack'];
    }
    if(isset($_GET['pack'])) {
        $idxstr = $idxstr . ' AND part_of_pack_id=? ';
        $par1 = $par1 . "i";
        $refArr[] = $_GET['pack'];
    }
    if(!empty($idd)) {
        $idxstr = $idxstr . ' AND (idx=?';
        foreach ($idd as $key => $value) {
            $par1 = $par1 . "i";
            $refArr[] = $value;;
            if($key != (count($idd) -1)) {
                $idxstr = $idxstr . ' OR idx=?';
            }
        }
        $idxstr = $idxstr . ') ';
    }
    $sql = $sql . $idxstr;
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, $par1, ...$refArr);
        if ($stmt->execute()) {
            $rows = fetch($stmt);
            $stmt->close();
            $bat_cnt = $rows[0]['COUNT(*)'];
        }
    } 
    else 
    {
        $error = "Could not read battery database:" . mysqli_error($link);
    }
    $sql = 'SELECT * FROM batteries WHERE name LIKE ? '.$idxstr.'ORDER BY idx LIMIT ? OFFSET ?';
    if ($stmt = mysqli_prepare($link, $sql)) {
        $param1 = ($page - 1) * $records_per_page;
        $refArr[] = $records_per_page;
        $refArr[] = $param1;
        $par1 = $par1 . "ii";
        mysqli_stmt_bind_param($stmt, $par1, ...$refArr);
        if ($stmt->execute()) {
            $rows = fetch($stmt);
            $stmt->close();
            $result["result"] = $rows;
        }
        else {
            $error = "Could not get records:" . $stmt->error;
        }
    }
    else {
        $error = "Could not get records:" . htmlspecialchars(mysqli_error($link));
    }
    $result['totalrecords'] = $bat_cnt;
    $result['error'] = $error;
    print json_encode($result);
}
else if(isset($_GET['page']) && $_GET['page']==="all") {
    $result = array();
    $error = "none";
    $rows = array();
    $records_per_page = 5;
    $page = $_GET['page'];
    $name = "%";
    $idd = array();
    if(isset($_GET['name']))
        $name = "%".$_GET['name']."%";
    else
        $name = "%";
    if(isset($_GET['idd'])) {
        $_idds = explode(",", $_GET['idd']);
        foreach($_idds as $_idd) {
        $_idd = trim($_idd);
        $idd[] = $_idd;
    }
    }
    $par1 = "s";
    $refArr = array();
    $refArr[] = $name;
    $idxstr ="";
    if(isset($_GET['type'])) {
        $idxstr = $idxstr . ' AND type=? ';
        $par1 = $par1 . "i";
        $refArr[] = $_GET['type'];
    }
    if(isset($_GET['subtype'])) {
        $idxstr = $idxstr . ' AND subtype=? ';
        $par1 = $par1 . "i";
        $refArr[] = $_GET['subtype'];
    }
    if(isset($_GET['isPack'])) {
        $idxstr = $idxstr . ' AND subtype=? ';
        $par1 = $par1 . "i";
        $refArr[] = $_GET['isPack'];
    }
    if(isset($_GET['pack'])) {
        $idxstr = $idxstr . ' AND part_of_pack_id=? ';
        $par1 = $par1 . "i";
        $refArr[] = $_GET['pack'];
    }
    if(!empty($idd)) {
        $idxstr = $idxstr . ' AND (idx=?';
        foreach ($idd as $key => $value) {
            $par1 = $par1 . "i";
            $refArr[] = $value;;
            if($key != (count($idd) -1)) {
                $idxstr = $idxstr . ' OR idx=?';
            }
        }
        $idxstr = $idxstr . ') ';
    }
    
    $sql = 'SELECT * FROM batteries WHERE name LIKE ? '.$idxstr.'ORDER BY idx';
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, $par1, ...$refArr);
        if ($stmt->execute()) {
            $rows = fetch($stmt);
            $stmt->close();
            $result["result"] = $rows;
        }
        else {
            $error = "Could not get records:" . $stmt->error;
        }
    }
    else {
        $error = "Could not get records:" . htmlspecialchars(mysqli_error($link));
    }
    $result['error'] = $error;
    print json_encode($result);
}
else if(isset($_GET['id']) && is_numeric($_GET['id']) && !isset($_GET['packet'])) {
    $error = "none";
    $id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int) $_GET['id'] : 1;
    if(isset($_POST['new'])) {
        $id = $idx;
    }
    $sql = 'SELECT * FROM batteries WHERE idx = ?';
    if ($stmt = mysqli_prepare($link, $sql)) {
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "i", $id);
        if ($stmt->execute()) {
            $row = fetch($stmt);
            $stmt->close();
        }  
        else {
            $error = "Could not get record:" . $stmt->error;
        }
    }
    else {
        $error = "Could not get record:" . htmlspecialchars($mysqli->error);
    }

    $resuls = array();
    $result["error"] = $error;
    $result["result"] = $row[0];
    echo json_encode($result);
}
else if(isset($_GET['special'])) {
    $result = array();
    $result["error"] = "none";
    $rows = array();
    if($_GET['special'] == "batTypes") {
    $sql = 'SELECT * FROM battery_types';
        if ($stmt = mysqli_prepare($link, $sql)) {
            if ($stmt->execute()) {
                $rows = fetch($stmt);
                $stmt->close();
                $result["result"] = $rows;
            }
        }
    }
    if($_GET['special'] == "batSubTypes") {
    $sql = 'SELECT * FROM battery_sub_types';
        if ($stmt = mysqli_prepare($link, $sql)) {
            if ($stmt->execute()) {
                $rows = fetch($stmt);
                $stmt->close();
                $result["result"] = $rows;
            }
        }
    }
    if($_GET['special'] == "chargers") {
    $sql = 'SELECT * FROM chargers';
        if ($stmt = mysqli_prepare($link, $sql)) {
            if ($stmt->execute()) {
                $rows = fetch($stmt);
                $stmt->close();
                $result["result"] = $rows;
            }
        }
    }
    if($_GET['special'] == "types") {
    $sql = 'SELECT * FROM battery_types';
        if ($stmt = mysqli_prepare($link, $sql)) {
            if ($stmt->execute()) {
                $rows = fetch($stmt);
                $stmt->close();
                $result["battery_types"] = $rows;
            }
        }
    $sql = 'SELECT * FROM charge_state_types';
        if ($stmt = mysqli_prepare($link, $sql)) {
            if ($stmt->execute()) {
                $rows = fetch($stmt);
                $stmt->close();
                $result["charge_state_types"] = $rows;
            }
        }
    $sql = 'SELECT * FROM use_states';
        if ($stmt = mysqli_prepare($link, $sql)) {
            if ($stmt->execute()) {
                $rows = fetch($stmt);
                $stmt->close();
                $result["use_states"] = $rows;
            }
        }
    $sql = 'SELECT * FROM battery_sub_types';
        if ($stmt = mysqli_prepare($link, $sql)) {
            if ($stmt->execute()) {
                $rows = fetch($stmt);
                $stmt->close();
                $result["battery_sub_types"] = $rows;
            }
        }
    }
    print json_encode($result);
}
else if(isset($_GET['packet'])) {
    $newID = "";
    $type = "";
    $capacity = $_GET['capacity'];
    $cells = $_GET['cells'];
    $name = $_GET['packet'];
    $result = array();
    $error = "none";
    $rows = array();
    $idd = array();
    if(isset($_GET['idd'])) {
        $_idds = explode(",", $_GET['idd']);
        foreach($_idds as $_idd) {
        $_idd = trim($_idd);
        $idd[] = $_idd;
    }
    }
    $par1 = "";
    $refArr = array();
    $idxstr ="";
    if(!empty($idd)) {
        $idxstr = $idxstr . ' (idx=?';
        foreach ($idd as $key => $value) {
            $par1 = $par1 . "i";
            $refArr[] = $value;;
            if($key != (count($idd) -1)) {
                $idxstr = $idxstr . ' OR idx=?';
            }
        }
        $idxstr = $idxstr . ') ';
    }
    
    $sql = 'SELECT * FROM batteries WHERE '.$idxstr.'ORDER BY idx';
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, $par1, ...$refArr);
        if ($stmt->execute()) {
            $rows = fetch($stmt);
            $type = $rows[0]["type"];
            foreach ($rows as $k=>$v) {
                if($type != $v['type']) {
                    $error = "Not all batteries are of the same type";
                    $result['error'] = $error;
                    print json_encode($result);
                    $stmt->close();
                    exit();
                }
            }
        }
        else {
            $error = "Could not get records:" . $stmt->error;
            $result['error'] = $error;
            print json_encode($result);
            $stmt->close();
            exit();
        }
    }
    else {
        $error = "Could not get records:" . htmlspecialchars(mysqli_error($link));
        $result['error'] = $error;
        print json_encode($result);
        $stmt->close();
        exit();
    }
    if($error != "none") {
        $result['error'] = $error;
        print json_encode($result);
        $stmt->close();
        exit();
    }
    else {
        $sql = 'INSERT INTO batteries (name, type,capacity,cells,isPack) VALUES (?,?,?,?,?)';        
        if ($stmt = mysqli_prepare($link, $sql)) {
            $stmt->bind_param("siiii",$name, $type,$capacity, $cells, 1);
            $stmt->execute();
            $newID = $stmt->insert_id;
            if ($stmt->error) {
                $error = "Could not create record:" . $stmt->error;
                $stmt->close();
                print json_encode($result);
                exit();
            }
        } else {
            $error = "Could not create record:" . htmlspecialchars(mysqli_error($link));
            $stmt->close();
        }
        if($error != "none") {
            $result["error"] = $error;
            echo json_encode($result);
            exit();
        }
        else  {
            foreach ($idd as $key => $value) {
                $sql = 'UPDATE batteries SET part_of_pack_id=? WHERE idx = ?';
                if ($stmt = mysqli_prepare($link, $sql)) {
                    $stmt->bind_param("ii",$newID, $value);
                    $stmt->execute();
                    if ($stmt->error) {
                        $error = "Could not update record:" . $stmt->error;
                        $stmt->close();
                        $result["error"] = $error;
                        echo json_encode($result);
                        exit();
                    }
                } else {
                    $error = "Could not update record:" . htmlspecialchars($mysqli->error);
                    $stmt->close();
                    $result["error"] = $error;
                    echo json_encode($result);
                    exit();
                }
            }
            $result["error"] = $error;
            echo json_encode($result);
            $stmt->close();
        }
    }
}
else if(isset($_GET["delete"]) && is_numeric($_GET['delete'])) {
    $error = 'none';
    $sql = 'DELETE FROM batteries WHERE idx=?';
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, 'i', $_GET['delete']);
        if ($stmt->execute()) {
            if ($stmt->error) {
                $error = "Could not update record:" . $stmt->error;
                $stmt->close();
                $result["error"] = $error;
                echo json_encode($result);
                exit();
            }
        }
    }
    else {
        $error = "Could not update record:" . htmlspecialchars($mysqli->error);
        $stmt->close();
        $result["error"] = $error;
        echo json_encode($result);
        exit();
    }
    $result["error"] = $error;
    echo json_encode($result);
    $stmt->close();
}
else if(isset($_GET["chargerlive"]) && is_numeric($_GET['chargerlive'])) {
    $error = 'none';
    $sql = 'INSERT INTO chargers (id, name, ip) VALUES(?,?,?) ON DUPLICATE KEY UPDATE    
name=?, ip=?, lastseen = NULL';
    $ls = null;
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, 'issss', $_GET["chargerlive"], $_GET["chargername"], $_GET["chargerip"]
    , $_GET["chargername"], $_GET["chargerip"]);
        if ($stmt->execute()) {
            if ($stmt->error) {
                $error = "Could not update record:" . $stmt->error;
                $stmt->close();
                $result["error"] = $error;
                echo json_encode($result);
                exit();
            }
        }
    }
    else {
        $error = "Could not update record:" . htmlspecialchars($mysqli->error);
        $stmt->close();
        $result["error"] = $error;
        echo json_encode($result);
        exit();
    }
    $result["error"] = $error;
    echo json_encode($result);
    $stmt->close();
}
function fetch($result)
{    
    $array = array();

    if($result instanceof mysqli_stmt)
    {
        $result->store_result();

        $variables = array();
        $data = array();
        $meta = $result->result_metadata();

        while($field = $meta->fetch_field())
            $variables[] = &$data[$field->name]; // pass by reference

        call_user_func_array(array($result, 'bind_result'), $variables);

        $i=0;
        while($result->fetch())
        {
            $array[$i] = array();
            foreach($data as $k=>$v)
                $array[$i][$k] = $v;
            $i++;
        }
    }
    elseif($result instanceof mysqli_result)
    {
        while($row = $result->fetch_assoc())
            $array[] = $row;
    }

    return $array;
}
?>
