<?php 
session_start();
include('../db.php');

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
    exit;
}

  if(isset($_POST['submit'])){
        $id = $_POST['id'];
        $srno = $_POST['sr-no'];
        $party_name = $_POST['party_name'];
       
        $notruck = $_POST['no-truck'];
        $rate = $_POST['rate'];
        $credit = $_POST['credit'];
        $broker = $_POST['broker'];
        $prod_name = $_POST['prod_name'];
        $trucks = $_POST['trucks'];
        $sldate = $_POST['sl-date'];
        $weight = $_POST['weight'];
        $basicamt = $_POST['basic-amt'];
        $gst = $_POST['gst'];
        $tcsper = $_POST['tcs-per'];
        $tcsamt = $_POST['tcs-amt'];
        $tdsper = $_POST['tds-per'];
        $tdsamt = $_POST['tds-amt'];
        $finalamt = $_POST['final-amt'];
        $invoiceno = $_POST['invoice-no'];
        $truck_no = $_POST['truck_no'];
        $paymentst = $_POST['payment-st'];
        $gst_amount = $_POST['gst_amount'];


  
        if (isset($_POST['other_day'])){
          $other_day = $_POST['other_day'];
        }

        $conf_date='';
        if($_POST['conf_date']!='')
        {
            $conf_date = str_replace('/', '-',$_POST['conf_date']);
            $conf_date = date('Y-m-d', strtotime($conf_date));
        }

        

$truckDataArr=array();
foreach ($trucks as $key => $truck_id) 
{
   $sales_date='';
    if($sldate[$key]!='')
    {
      $sales_date = str_replace('/', '-', $sldate[$key]);
      $sales_date = date('Y-m-d', strtotime($sales_date));
    }

    $truckDataArr[$key]['truck_id']=$truck_id;
    $truckDataArr[$key]['sales_date']=$sales_date;
    $truckDataArr[$key]['weight']=$weight[$key];
    $truckDataArr[$key]['basic_amt']=$basicamt[$key];
    $truckDataArr[$key]['gst_per']=$gst[$key];
    $truckDataArr[$key]['gst_amount']=$gst_amount[$key];
    $truckDataArr[$key]['tcs_per']=$tcsper[$key];
    $truckDataArr[$key]['tcs_amt']=$tcsamt[$key];
    $truckDataArr[$key]['tds_per']=$tdsper[$key];
    $truckDataArr[$key]['tds_amt']=$tdsamt[$key];
    $truckDataArr[$key]['final_amt']=$finalamt[$key];
    $truckDataArr[$key]['invoice_no']=$invoiceno[$key];
    $truckDataArr[$key]['truck_no']=$truck_no[$key];
    $truckDataArr[$key]['payment_status']=$paymentst[$key];

    $truck_complete=0;
    if(isset($_POST['truck_complete'][$key]))
    {
        $truck_complete=1;
    }
    $truckDataArr[$key]['truck_complete']=$truck_complete;

}

$truckDataArr=json_encode($truckDataArr);

    
        $username= $_SESSION["username"];
        date_default_timezone_set('Asia/Kolkata');
        $timestamp=date("Y-m-d H:i:s");

        $sql = "update kapasiya
                  set 

                    serialno = '".$srno."',
                   
                    party = '".$party_name."',
                    pro_name = '".$prod_name."',
                    no_of_truck = '".$notruck."',
                    rate = '".$rate."',
                    credit = '".$credit."',
                    broker = '".$broker."',
                    username='".$username."',
                    updated_at='".$timestamp."',
                   
                    conf_date = '".$conf_date."',
                    other_day = '".$other_day."',
                    truck = '".$truckDataArr."'

          where id=".$id;
      $result = mysqli_query($conn, $sql);
      if($result){
        $successMsg = 'New record updated successfully';
        $page=1;
        if(isset($_POST['page']))
        {
          $page=$_POST['page'];
        }
        header("Location: index1.php?page=$page");
      }else{
        $errorMsg = 'Error '.mysqli_error($conn);
                echo $errorMsg;
      }
  }