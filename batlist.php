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
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script src="scripts/batlist.js"></script>
    <script src="scripts/common.js"></script>
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
        .table-title-first .btn {
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
        .container .btn-outline-secondary {
            background: #e9ecef;
        }
        .table-title-first .btn:hover, .table-title-first .btn:focus {
            color: #566787;
            background: #f2f2f2;
        }
        .table-title-first .btn i {
            float: left;
            font-size: 21px;
            margin-right: 5px;
        }
        .table-title-first .btn span {
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
        table.table td.pack {
            background-color:#32a852;
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
            width: 80px;
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
    <script type="text/javascript">
        $(document).ready(function () {
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
    
    
<div id="menu" ></div>
</head>
<body class="">

    <div class="container maincontainer">
        <div class="table-wrapper">
            <div class="table-title">
                <div class="row table-title-first">
                    <div class="col-sm-5">
                        <h2>Battery&nbsp;<b>List</b></h2>
                    </div>
                    <div class="col-sm-7">
                        <a href="#" id="addnew" class="btn btn-primary"><i class="material-icons">&#xE147;</i> <span>Add New Battery</span></a>
                        <a class="btn btn-primary" data-toggle="collapse" href="#filters" role="button" aria-expanded="false" aria-controls="filters"><i class="material-icons">&#xE24D;</i> <span>Filter</span></a> 
                    </div>
                </div>
                <div class="container collapse" id="filters">
                    <hr />
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">With id (comma delimited):</span>
                                </div>
                                <input type="text" id="idd" class="form-control" aria-label="With textarea"></input>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" id="qrbtn" type="button">QRCode</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">With name containing</span>
                                </div>
                                <input type="text"  id="batnamefind" class="form-control" aria-label="With textarea"></input>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Of type:</span>
                                </div>
                                <select class="form-control" id="typefilter">
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Of subtype:</span>
                                </div>
                                <select class="form-control" id="subtypefilter">
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Part of pack ID:</span>
                                </div>
                                <input type="number" id="partOfPack" class="form-control"></input>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <span class="button-checkbox">
                                <button type="button" class="btn  mt-2" data-color="success">Pack Only</button>
                                <input id="isPack" type="checkbox" class="d-none" />
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Image</th>
                        <th>Name</th> 
                        <th>N. of Cells</th>
                        <th>Capacity</th>
                        <th>Type</th>
                        <th>Sub Type</th>
                        <th>Date Created</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="battable">
                </tbody>
            </table>
            <div class = "row">
                <button id="createpack" type="button" class="btn btn-primary">Create pack with selected</button>
            </div>
            <div class="clearfix">
                <div class="hint-text"></div>
                <ul class="pagination">
                    <li id="previousBtn" class="page-item">
                        <a href="">Previous</a>
                    </li>
                    <li id="nextBtn" class="page-item">
                        <a href="">Next</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>         
</body>
</html> 
