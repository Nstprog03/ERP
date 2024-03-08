<?php
session_start();
include('../db.php');

//get Invoice data
if(isset($_POST['Getyear']) && isset($_POST['firm']))
{
    $firm = $_POST['firm'];
    $data = array();
    $yearData = array();
  
    $sql = "select * from party_audit_report where party_name = '".$firm."' AND ad_report_type='IT Return Reoport'";
    $result = mysqli_query($conn, $sql);

    if(mysqli_num_rows($result) > 0){
        foreach ($conn->query($sql) as $key => $result) 
        {
            $financial_yearSQL = "select * from financial_year where id='".$result['financial_year_id']."'";
                $financial_year_result = mysqli_query($conn, $financial_yearSQL);
        
                $financial_year_row = mysqli_fetch_assoc($financial_year_result);
        
                $start_yr='';
                $end_yr='';
                if(isset($financial_year_row))
                {
                    $start_yr =  date("Y", strtotime($financial_year_row['startdate']));
                    $end_yr =  date("Y", strtotime($financial_year_row['enddate']));
                }
                $FinalYears= $start_yr.'-'.$end_yr;
                
                $yearData[$key]['id'] = $result['id'];
                $yearData[$key]['year'] = $FinalYears;
        }

        $data['year'] = $yearData;
        $data['status'] = true;
    }else{
        $data['status'] = false;
    }

    echo json_encode($data);
}
?>