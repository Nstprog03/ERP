<?php
$host = "localhost";
$username = "jivanlpt_deverp";
$password = "xdO0B]aXe!m)";
$dbname = "jivanlpt_pre-erp";



$conn = mysqli_connect($host, $username, $password, $dbname);
if (!$conn) {
	die("Connection failed: " . mysqli_connect_error());
}


if(!function_exists("getFinancialYear")) { 
function getFinancialYear($connection){
    $date = date("Y-m-d");
    $sql2 = "SELECT * FROM financial_year WHERE '".$date."' BETWEEN startdate AND DATE(enddate) ";
    $result2 = mysqli_query($connection, $sql2);
    $num = mysqli_num_rows($result2);
    $organizations = array();
    if($num > 0){
        $row = mysqli_fetch_array($result2);
        $organizations[] =  $row;
        
        $nextYear = "select * from financial_year WHERE startdate > '".$row['enddate']."' ORDER By startdate desc";
        $nextYearRes = mysqli_query($connection, $nextYear);
        $nextYearNum = mysqli_num_rows($nextYearRes);
        if($nextYearNum > 0){
            while($nextYearRow = mysqli_fetch_array($nextYearRes)){
                 $organizations[] =  $nextYearRow;
            }
        }
        
        $lastYear = "select * from financial_year WHERE startdate < '".$row['startdate']."' ORDER By startdate desc";
        $lstYearRes = mysqli_query($connection, $lastYear);
        $lastYearNum = mysqli_num_rows($lstYearRes);
        if($lastYearNum > 0){
            while($lastYearRow = mysqli_fetch_array($lstYearRes)){
                 $organizations[] =  $lastYearRow;
            }
        }
        
    }else{
        $nextYear = "select * from financial_year WHERE startdate > '".date("Y-m-d")."' ORDER By startdate desc";
        $nextYearRes = mysqli_query($connection, $nextYear);
        $nextYearNum = mysqli_num_rows($nextYearRes);
        if($nextYearNum > 0){
            while($nextYearRow = mysqli_fetch_array($nextYearRes)){
                 $organizations[] =  $nextYearRow;
            }
        }
        
        $lastYear = "select * from financial_year WHERE startdate < '".date("Y-m-d")."' ORDER By startdate desc";
        $lstYearRes = mysqli_query($connection, $lastYear);
        $lastYearNum = mysqli_num_rows($lstYearRes);
        if($lastYearNum > 0){
            while($lastYearRow = mysqli_fetch_array($lstYearRes)){
                 $organizations[] =  $lastYearRow;
            }
        }
    }
    
    return $organizations;
}
}


if(!function_exists("getSeasonalYear")) { 
function getSeasonalYear($connection){
    $date = date("Y-m-d");
    $sql2 = "SELECT * FROM seasonal_year WHERE '".$date."' BETWEEN startdate AND DATE(enddate) ";
    $result2 = mysqli_query($connection, $sql2);
    $num = mysqli_num_rows($result2);
    $organizations = array();
    if($num > 0){
        $row = mysqli_fetch_array($result2);
        $organizations[] =  $row;
        
        $nextYear = "select * from seasonal_year WHERE startdate > '".$row['enddate']."' ORDER By startdate desc";
        $nextYearRes = mysqli_query($connection, $nextYear);
        $nextYearNum = mysqli_num_rows($nextYearRes);
        if($nextYearNum > 0){
            while($nextYearRow = mysqli_fetch_array($nextYearRes)){
                 $organizations[] =  $nextYearRow;
            }
        }
        
        $lastYear = "select * from seasonal_year WHERE startdate < '".$row['startdate']."' ORDER By startdate desc";
        $lstYearRes = mysqli_query($connection, $lastYear);
        $lastYearNum = mysqli_num_rows($lstYearRes);
        if($lastYearNum > 0){
            while($lastYearRow = mysqli_fetch_array($lstYearRes)){
                 $organizations[] =  $lastYearRow;
            }
        }
        
    }else{
        $nextYear = "select * from seasonal_year WHERE startdate > '".date("Y-m-d")."' ORDER By startdate desc";
        $nextYearRes = mysqli_query($connection, $nextYear);
        $nextYearNum = mysqli_num_rows($nextYearRes);
        if($nextYearNum > 0){
            while($nextYearRow = mysqli_fetch_array($nextYearRes)){
                 $organizations[] =  $nextYearRow;
            }
        }
        
        $lastYear = "select * from seasonal_year WHERE startdate < '".date("Y-m-d")."' ORDER By startdate desc";
        $lstYearRes = mysqli_query($connection, $lastYear);
        $lastYearNum = mysqli_num_rows($lstYearRes);
        if($lastYearNum > 0){
            while($lastYearRow = mysqli_fetch_array($lstYearRes)){
                 $organizations[] =  $lastYearRow;
            }
        }
    }
    
    return $organizations;
}
}


?>