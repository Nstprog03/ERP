<?php
session_start();
require_once('../db.php');
if(isset($_POST['cur_record_id']) && isset($_POST['pur_report_id']))
{

  $pur_report_id=$_POST['pur_report_id'];
  $cur_record_id=$_POST['cur_record_id'];


  $response=array();


  //get bales from purchase report
  $total_bales=0;
  $sql="select * from pur_report where id='".$pur_report_id."'";
  $result=mysqli_query($conn,$sql);
  if(mysqli_num_rows($result)>0)
  {
    $row=mysqli_fetch_assoc($result);
    $total_bales+=$row['bales'];

  }


  //get used bales from comparision report where record is not current edit 
  
  $sql="select * from comparison_report where id!='".$cur_record_id."'";
  $result=mysqli_query($conn,$sql);
  if(mysqli_num_rows($result)>0)
  {
    while ($row2=mysqli_fetch_assoc($result)) 
    {
      $dataArr=json_decode($row2['purchase_data'],true);
      foreach ($dataArr as $key => $item) 
      {
        if($item['purchase_report_id']==$pur_report_id)
        {
          $total_bales-=$item['lot_bales'];
        }
       
      }
      
    }
  }


  $response['avl_bales']=$total_bales;




  echo json_encode($response);
  exit;
}
?>