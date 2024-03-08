<?php

session_start();

require_once('../db.php');

require_once '../PHPLibraries/dompdf/autoload.php';

use Dompdf\Dompdf;



//dd/mm/yyy

function convertDate($date)

{

  $final_date='';

  if($date!='' && $date!='0000-00-00')

  {

    $final_date = str_replace('-', '/', $date);

    $final_date = date('d/m/Y', strtotime($final_date));

  }





    return $final_date;



}





if (isset($_GET['id'])) 

{

	
    if($_SESSION["active_module"]=='purchase_cotton')
    {
      $firm_id=$_SESSION['pur_firm_id'];
      $financiyal_year_id=$_SESSION['pur_financial_year_id'];
      $module='purchase_cotton';
    }
    if($_SESSION["active_module"]=='purchase_kapas')
    {
      $firm_id=$_SESSION['pur_firm_id'];
      $financiyal_year_id=$_SESSION['pur_financial_year_id'];
      $module='purchase_kapas';
    }

    if($_SESSION["active_module"]=='sales')
    {
      $firm_id=$_SESSION['sales_conf_firm_id'];
      $financiyal_year_id=$_SESSION['sales_financial_year_id'];
      $module='sales';
    }

    if($_SESSION["active_module"]=='kapasiya_sales')
    {
      $firm_id=$_SESSION['kap_firm_id'];
      $financiyal_year_id=$_SESSION['kap_seasonal_year_id'];
      $module='kapasiya_sales';
    }





	$dompdf = new Dompdf();

	$dompdf->setPaper('A4', 'portrait');





	

	$id = $_GET['id'];

	$sql = "SELECT * FROM `tds_tcs_declaration` where id='".$id."'";

	$result = mysqli_query($conn, $sql);

	if (mysqli_num_rows($result) > 0) {



	  $row = array();



	   foreach ($conn->query($sql) as $key => $result) 

	   {

	       $row=$result;

	   }





	  //date

	if($row['date']!='' && $row['date']!='0000-00-00')

	{

	  $date = str_replace('-', '/', $row['date']);

	  $date = date('d/m/Y', strtotime($date));

	}



  





    //external party name & address

	$ext_sql = "select * from external_party where id='".$row['ext_party_id']."'";

	$ext_result = mysqli_query($conn, $ext_sql);



	if(mysqli_num_rows($ext_result)>0)

	{

	  $ext_row = mysqli_fetch_assoc($ext_result);

	  $ext_party=$ext_row['partyname'];

	  $ext_gstin=$ext_row['gstin'];

	  $ext_party_add1=$ext_row['address'].', '.$ext_row['city'].',';

	  $ext_party_add2='Dist. '.$ext_row['district'].', '.$ext_row['state'].' - '.$ext_row['pincode'];

	}









    //firm

    $firm_name='';

    $firm_add1='';

    $firm_add2='';

    $email1='';

   	$firm_gst='';

    $contact1='';

    

    $sqlFirm = "select * from party where id='".$row['firm_id']."'";

    $resultFirm = mysqli_query($conn, $sqlFirm);



    $rowFirm = mysqli_fetch_assoc($resultFirm);

   

    if(mysqli_num_rows($resultFirm)>0)

    {

      $firm_name=strtoupper($rowFirm['party_name']);

      $firm_add1=$rowFirm['party_address'].', '.$rowFirm['city'].',';

	  $firm_add2='Dist. '.$rowFirm['district'].', '.$rowFirm['state'].' - '.$rowFirm['pincode'];



	  	$firm_gst='<b>GST IN : </b>'.$rowFirm['gst_in'];



	    $path = "../firm/files/logo/".$rowFirm['logo_img'];

		$type = pathinfo($path, PATHINFO_EXTENSION);

		$data = file_get_contents($path);

		$company_logo = 'data:image/' . $type . ';base64,' . base64_encode($data);





		//stamp

		$path2 = "../firm/files/stamp/".$rowFirm['stamp_img'];

		$type = pathinfo($path2, PATHINFO_EXTENSION);

		$data2 = file_get_contents($path2);

		$stamp_image = 'data:image/' . $type . ';base64,' . base64_encode($data2);







		//email

		$emailArr=json_decode($rowFirm['party_email'],true);

		if(isset($emailArr[0]))

		{

			$email1='<b>Email : </b>'.$emailArr[0];

		}

		





		//contact no

		$contactArr=json_decode($rowFirm['contact_number'],true);

		if(isset($contactArr[0]))

		{

			$contact1='<b>Contact No. : </b>'.$contactArr[0];

		}

		





    }









    //get year

    if($_SESSION["active_module"]=='kapasiya_sales')

    {

    	$year_table='seasonal_year';

    }

    else

    {

    	$year_table='financial_year';

    }







    $year='';

    $sql2 = "select * from $year_table where id='".$row['financiyal_year_id']."'";

    $result2 = mysqli_query($conn, $sql2);

    if(mysqli_num_rows($result2)>0)

    {

    	$rowYear=mysqli_fetch_assoc($result2);

    	 //get Start Year And End Year

        $syear = date("Y", strtotime($rowYear['startdate']));

        $eyear = date("y", strtotime($rowYear['enddate']));

        $year=$syear.'-'.$eyear;

    }





    //get audit & ITR Data

    

    $auditDataArr=array();

    $idArr=explode(',',$row['audit_report_id']);



    foreach ($idArr as $key => $id) 

    {



    	$sql2 = "select * from party_audit_report where id='".$id."'";

	    $result2 = mysqli_query($conn, $sql2);

	    if(mysqli_num_rows($result2)>0)

	    {

	    	$rowAudit=mysqli_fetch_assoc($result2); 

	    	$auditDataArr[$key]=$rowAudit;



	    	//get assessment year (finacial_year)

	    	$fyear='';

	    	$sql2 = "select * from financial_year where id='".$rowAudit['assessment_year_id']."'";

		    $result2 = mysqli_query($conn, $sql2);

		    if(mysqli_num_rows($result2)>0)

		    {

		    	$rowYear=mysqli_fetch_assoc($result2);

		    	 //get Start Year And End Year

		        $syear = date("Y", strtotime($rowYear['startdate']));

		        $eyear = date("y", strtotime($rowYear['enddate']));

		        $fyear=$syear.'-'.$eyear;

		    }



		    $auditDataArr[$key]['assessment_year']=$fyear;

		    $auditDataArr[$key]['due_date']=convertDate($rowAudit['due_date']);



	    }

    	

    }





    $assement_year_arr=array();

    $assement_year_txt='';



    if(count($auditDataArr)>0)

    {

    	foreach ($auditDataArr as $key => $item) {

    		$assement_year_arr[]=$item['assessment_year'];

    	}

    	$assement_year_txt=implode(' and ',$assement_year_arr);

    }









   







	}else {

	  $errorMsg = 'Could not Find Any Record';

	}





$html = <<<HTML

<html>

<head>



<style>



*{

	font-size: 14px;



}



.page{

	height: 100%;

	width: 100%;

	padding: 3%;

}







.date{

	text-align: right;

}



.paragraph1{

	margin-top: 60px;

	text-align: justify;

}



.paragraph2{

	margin-top: 30px;

	text-align: justify;

	margin-left: 20px;

}



li{

  margin-top: 20px;

  text-align: justify;

}



.table_section{

	margin-top: 20px;

}



.table2 td, th {

  border: 1px solid black;

  text-align: center;

}



.table2 {

  width: 100%;

  border-collapse: collapse;



}



.concusion_title{

	margin-top: 20px;

}



.footer_firm{

	margin-top: 40px;

}



.sign_title{

	margin-top: 3%;

}



.table1{

	width:100%;

}



.logo{

width: 195px;

height: 90px;

}



.head_firm_name{

	font-size: 16px;

	font-weight: bold;

}



.table1{

	width: 100%;

  border: 1px solid black;

}

.stamp_img{

	margin-top: 10px;

	width: 150px;

	height: 100px;

}













</style>

</head>



<body class="page">





HTML;



/*header section end */

$html.="

<div class='main'>





	<table class='table1'>



		<tr>

			<td width='35%'>

				<img class='logo' src=".$company_logo." />

			</td>

			<td>

				<div class='head_firm_name'>".$firm_name."</div>

				<div class='head_firm_add'>".$firm_add1."</div>

				<div class='head_firm_add'>".$firm_add2."</div>

				<div class='head_firm_contact'>".$email1."</div>

				<div class='head_firm_contact'>".$contact1."</div>

				<div class='head_firm_gst'>".$firm_gst."</div>

				

			</td>

		</tr>

	</table>



	

	<p class='date'>Date : ".$date."</p>



	<div class='ext_party_section'>

		<div>To,</div>

		<div>".$ext_party."</div>

		<div>".$ext_party_add1."</div>

		<div>".$ext_party_add2."</div>

	</div>



	<div class='paragraph1'>

		This is with reference to applicability of TDS u/s 194Q of the Act on purchase of goods made/to be made by ".$firm_name." starting from 01.07.2021

	</div>



	



	<ol type='1'>

	    <li>Total sales, gross receipts, or turnover of the company (".$firm_name.") from business during FY ".$year." was more than Rs.10 crores. Therefore, provisions of Section 194Q are applicable to the company. Accordingly, the company will deduct tax at source at the applicable rates from 1st July 2021 on amount credited/paid towards purchase of goods exceeding ".$row['good_exceeding'].".</li>

	    <li>It is requested that provisions of section 206C(1H) of the Act need not be applied by the seller (i.e. ".$ext_party.") on sale of goods to the company (".$firm_name.")</li>

	    <li>".$firm_name." has field Income Tax Return for previous years i.e. Assessment year (AY)  ".$assement_year_txt.".

		</li>

	</ol>



	<div class='table_section'>

			<table class='table2'>

					<tr>

						<th>Sl No</th>

						<th>Assessment Year</th>

						<th>Acknowledgment No.</th>

						<th>Date of Filling</th>

						<th>Due Date Of Filling</th>

					</tr>";





					if(count($auditDataArr)>0)

					{	$index=1;

						foreach ($auditDataArr as $key => $item) {



							  //date of filling

							$date_of_filling='';

							if($item['date_of_filling']!='' && $item['date_of_filling']!='0000-00-00')

							{

							  $date_of_filling = str_replace('-', '/', $item['date_of_filling']);

							  $date_of_filling = date('d/m/Y', strtotime($date_of_filling));

							}

							



							$html.="

								<tr>

								<td>".$index."</td>

								<td>".$item['assessment_year']."</td>

								<td>".$item['acknow_no']."</td>

								<td>".$date_of_filling."</td>

								<td>".$item['due_date']."</td>

								</tr>

							";



							$index++;



						}

					}









					$html.="

			</table>



			<div class='concusion_title'>Conclusion: </div>



			<p>In our case section 194Q of Income tax Act is applicable to us, TCS Should not be deduct in your Sales Invoice raise towards us.</p>





			<div class='footer_firm'>For, ".$firm_name."</div>



			<div>

				<img class='stamp_img' src=".$stamp_image." />

			</div>

			<div class='sign_title'>Authorized Signature</div>







	</div>





</div>

";

	





$html.='</body></html>';







$dompdf->loadHtml($html);

$dompdf->set_option('isHtml5ParserEnabled', true);



// Render the HTML as PDF

$dompdf->render();



// Output the generated PDF to Browser

//$dompdf->stream();











date_default_timezone_set("Asia/Kolkata");   //India time (GMT+5:30)

$timestamp=date('dmY_His');



$filename="tds_tcs_declaration_".$timestamp.'.pdf';



$dompdf->stream($filename, array("Attachment" => false));


include('../global_function.php'); 
$data=getFileStoragePath("tds_tcs_declaration",$financiyal_year_id);  //function from global_function file
$root_path=$data[0]; // file move path
$store_path=$data[1]; // db store path


$check=file_put_contents($root_path.$filename,$dompdf->output());


$filename=$store_path.$filename;



if($check)

{

  $timestamp=date("Y-m-d H:i:s");

   $sql_pdf="insert into pdf (table_indicator,record_id,file_name,username,created_at) value('4','".$_GET['id']."','".$filename."','".$_SESSION['username']."','".$timestamp."')";

   mysqli_query($conn,$sql_pdf);

}







}

?>