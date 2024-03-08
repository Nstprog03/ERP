<?php
session_start();
include('../db.php');
//require('../PHPLibraries/FPDF_library/fpdf.php');
require('../PHPLibraries/FPDF_library/html2pdf.php');
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
    exit;
}
if(!isset($_SESSION['pur_firm_id']) && !isset($_SESSION['pur_financial_year_id']))
{
  header('Location: ../purchase_index.php');
}
//dd/mm/yyy
function convertDate2($date)
{
  $final_date='';
  if($date!='' && $date!='0000-00-00')
  {
    $final_date = str_replace('-', '/', $date);
    $final_date = date('d/m/Y', strtotime($final_date));
  }
    return $final_date;
}
if(isset($_GET['id']))
{
	$id = $_GET['id'];
  	$sql = "select p.*,
  	f.logo_img,
  	f.stamp_img,
  	f.gst_in as fgst,
  	f.party_address as faddress,
  	f.city as fcity,
  	f.district as fdistrict,
  	f.state as fstate,
  	f.pincode as fpincode,
  	f.party_email as femail,
  	f.contact_number as fcontact
  	from pur_conf p, party f where p.firm=f.id AND p.id=".$id;
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) 
    {
      $row = mysqli_fetch_assoc($result);
    }
    if($row['logo_img']!='')
    {
    	$logo_image="../firm/files/logo/".$row['logo_img'];
        $img_extension=substr($row['logo_img'], strpos($row['logo_img'], ".") + 1);
    }
    else
    {
    	$logo_image="../image/no-image.jpg";
        $img_extension='jpg';
    }
    if($row['stamp_img']!='')
    {
    	$stamp_image="../firm/files/stamp/".$row['stamp_img'];
        $stamp_img_extension=substr($row['stamp_img'], strpos($row['stamp_img'], ".") + 1);
    }
    else
    {
    	$stamp_image="../image/no-image.jpg";
        $stamp_img_extension='jpg';
    }

	$pdf=new FPDF('p','mm','A4');
	$pdf=new PDF_HTML();
	$pdf->SetTitle('Purchase Confirmation PDF');
	$pdf->AddPage();
	$pdf->setFont('Arial','B',14);
	//header section
	//set head logo
	$head_logo=$pdf->Image($logo_image,10,10,50,20,$img_extension);
	$pdf->cell(55,10,$head_logo,0,0,'C');
	// header title
	$pdf->cell(5);
	$pdf->setFont('Arial','B',8);
	$addresss=$pdf->Multicell(0,3,"Manufacturer & Exporter : \nContamination Control Cotton Bales and Cotton Seeds");
	$pdf->setFont('Arial');
	$pdf->Ln(2);
	// GST header
	$pdf->setFont('Arial','B',9);
	$pdf->Text(163, 13,"GST IN : ".$row['fgst']);
	// address header
	$pdf->setFont('Arial');
	$pdf->setFontSize(8.5);
	$pdf->Text(70.5, 20,$row['faddress'].', '.$row['fcity'].', Dist.'.$row['fdistrict'].', '.$row['fstate'].' - '.$row['fpincode']);
	//dynamiac email
	$emailArr=array();
	$countEmail=0;
	if($row['femail']!='')
	{
		$emailArr=json_decode($row['femail']);
		$countEmail=count($emailArr);
	}
	$pdf->setFont('Arial');
	$pdf->setFontSize(8.5);
	$email_position=26;
	if($countEmail>0)
	{
		foreach ($emailArr as $key => $email) 
		{
			if($key<=1)
			{
				$pdf->Text(70.5, $email_position,$email);
	   			$email_position+=4;
			}
		}
	}
	//dynamic contact no
	$contactArr=array();
	$countContact=0;
	if($row['fcontact']!='')
	{
		$contactArr=json_decode($row['fcontact']);
		$countContact=count($contactArr);
	}
	$pdf->setFont('Arial');
	$pdf->setFontSize(8.5);
	$contact_position=$email_position+1.5;
	if($countContact>0)
	{
		foreach ($contactArr as $key => $contactNo) 
		{
			if($key<=1)
			{
				$pdf->Text(70.5, $contact_position,$contactNo);
	   			$contact_position+=4;
			}
		}
	}
	//$pdf->Text(56.5, 35,"1234567890");
	// main title
	$pdf->SetXY(202,30);
	$pdf->cell(-40,-150);
	$titleText="PURCHASE\nCONFIRMATION";
	$pdf->setFont('Arial','B',15);
	$pdf->Multicell(100,5,$titleText);
	//add line
	$pdf->SetLineWidth(1);
	$pdf->Line(5, 42, 205, 42);
	// content first
	$pdf->setFont('Arial','B',10);
	$pdf->Ln(4);
	$pdf->cell(-150);
	$pdf->Cell(0, 5, "Dear Sir,", 0, 0, 'C');
	$pdf->setFont('Arial','B',10);
	$pdf->Ln(4);
	$pdf->cell(5);
	$pdf->Cell(0, 5, "We heareby Confirmed to by FP Cotton Bales From The Following Party on this Terms And Condition.", 0, 0, 'C');
	$ext_sql = "select * from external_party where id='".$row['party']."'";
    $ext_result = mysqli_query($conn, $ext_sql);
    if (mysqli_num_rows($ext_result) > 0) 
    {
      $ext_row = mysqli_fetch_assoc($ext_result);
    }
    $pdf->cell(50);
	$pdf->setFont('Arial','B',10);
	$pdf->Text(23, 60,"Seller Party");
	$pdf->SetLineWidth(0);
	$pdf->Line(23, 62, 92, 62);
    $pdf->setFont('Arial','B',10);
	$pdf->Text(23, 67,$ext_row['partyname']);
	

	//$pdf->Line(23, 62, 90, 62);
    $pdf->setFont('Arial','B',10);
	$pdf->Text(45, 60,"(GSTIN-".$ext_row['gstin'].")");
	
	
	$pdf->setFont('Arial');
	$pdf->setFontSize(9.5);
	$pdf->Text(23, 72,$ext_row['address'].', '.$ext_row['city']);
	$pdf->setFont('Arial');
	$pdf->setFontSize(9.5);
	$pdf->Text(23, 77,'Dist.'.$ext_row['district'].', '.$ext_row['state'].' - '.$ext_row['pincode']);
	//right section - conf no. & date
	$pdf->setFont('Arial','B',12);
	$pdf->Text(125, 60,"P.O. No. : ".$row['pur_conf']);
	$pdf->setFont('Arial','B',12);
	$pdf->Text(125, 65,"Date : ".convertDate2($row['pur_report_date']));
	$pdf->SetLineWidth(0);
	$pdf->Line(124, 67, 180, 67);
	$pdf->setFont('Arial');
	$pdf->Text(125, 72,"Delivery Date : ".$row['delivery_date']);
	$sql1 = "select * from party where id=".$row['firm'];
    $result1 = mysqli_query($conn, $sql1);
    if (mysqli_num_rows($result1) > 0) 
    {
      $row1 = mysqli_fetch_assoc($result1);
    }
	// Billing
    $pdf->cell(50);
	$pdf->setFont('Arial','B',10);
	$pdf->Text(23, 85,"Billing / Buyer Party");
	$pdf->SetLineWidth(0);
	$pdf->Line(23, 87, 90, 87);
    $pdf->setFont('Arial','B',10);
	$pdf->Text(23, 91,$row1['party_name']);
	$pdf->setFont('Arial');
	$pdf->setFontSize(9.5);
	$pdf->Text(23, 96,$row1['party_address'].', '.$row1['city']);
	$pdf->setFont('Arial');
	$pdf->setFontSize(9.5);
	$pdf->Text(23, 101,'Dist.'.$row1['district'].', '.$row1['state'].' - '.$row1['pincode']);
	// Ship To
    $pdf->cell(50);
	$pdf->setFont('Arial','B',10);
	$pdf->Text(125, 85,"Ship To");
	$pdf->SetLineWidth(0);
	$pdf->Line(124, 87, 180, 87);
    $pdf->setFont('Arial','B',10);
	$pdf->Text(125, 91,$row1['party_name']);
	$pdf->setFont('Arial');
	$pdf->setFontSize(9.5);
	$pdf->Text(125, 96,$row1['party_address'].', '.$row1['city']);
	$pdf->setFont('Arial');
	$pdf->setFontSize(9.5);
	$pdf->Text(125, 101,'Dist.'.$row1['district'].', '.$row1['state'].' - '.$row1['pincode']);
	// Description 
	$pdf->cell(50);
	$pdf->setFont('Arial','B',10);
	$pdf->Text(23, 116,"Description :");
	$pdf->SetLineWidth(0);
	$pdf->Line(45, 115, 199, 115);
	//get product name
	$prod_name='';
	if(isset($row['product_name']))
	{
		$sql_pro="SELECT * FROM `products` where id='".$row['product_name']."'";
		$result_pro=mysqli_query($conn,$sql_pro);
		$row_pro=mysqli_fetch_assoc($result_pro);
		$prod_name=$row_pro['prod_name'];
	}
	$pdf->setFont('Arial');
	$pdf->Text(52, 113,"Product");
	$pdf->setFont('Arial');
	$pdf->Text(52, 120,$prod_name);
	$pdf->setFont('Arial');
	$pdf->Text(85, 113,"Qty. In Bales");
	$pdf->cell(50);
	$pdf->setFont('Arial');
	$pdf->Text(85, 120,$row['bales']);
	$pdf->setFont('Arial');
	$pdf->Text(115, 113,"Rate/Candy");
	$pdf->cell(50);
	$pdf->setFont('Arial');
	$pdf->Text(115, 120,$row['candy_rate']);
	$dispatch='';
	if($row['dispatch']=='')
	{
		$dispatch='N/A';
	}
	else
	{
		$dispatch=$row['dispatch'];
	}
	$pdf->setFont('Arial');
	$pdf->Text(140, 113,"Dispatch");
	$pdf->cell(50);
	$pdf->setFont('Arial');
	$pdf->Text(140, 120,$dispatch);
	//get product name
	$broker_name='';
	if(isset($row['product_name']))
	{
		$sql_broker="SELECT * FROM `broker` where id='".$row['broker']."'";
		$result_broker=mysqli_query($conn,$sql_broker);
		$row_broker=mysqli_fetch_assoc($result_broker);
		$broker_name=$row_broker['name'];
	}
	$pdf->setFont('Arial');
	$pdf->Text(160, 113,"Broker");
	$pdf->cell(50);
	$pdf->setFont('Arial');
	$pdf->Text(160, 120,$broker_name);
	// Paremeter 
	$pdf->cell(50);
	$pdf->setFont('Arial','B',10);
	$pdf->Text(23, 132,"Parameter :");
	$pdf->SetLineWidth(0);
	$pdf->Line(43, 131, 199, 131);
	$pdf->setFont('Arial');
	$pdf->Text(52, 129,"Lenth(mm)");
	$pdf->setFont('Arial');
	$pdf->Text(52, 136,$row['pro_length']);
	$pdf->setFont('Arial');
	$pdf->Text(85, 129,"Mic");
	$pdf->cell(50);
	$pdf->setFont('Arial');
	$pdf->Text(85, 136,$row['pro_mic']);
	$pdf->setFont('Arial');
	$pdf->Text(115, 129,"Trash(%)");
	$pdf->cell(50);
	$pdf->setFont('Arial');
	$pdf->Text(115, 136,$row['pro_trash']);
	$pdf->setFont('Arial');
	$pdf->Text(145, 129,"Moisture(%)");
	$pdf->cell(50);
	$pdf->setFont('Arial');
	$pdf->Text(145, 136,$row['pro_mois']);
	$pdf->setFont('Arial');
	$pdf->Text(175, 129,"RD");
	$pdf->cell(50);
	$pdf->setFont('Arial');
	$pdf->Text(175, 136,$row['pro_rd']);
	// Paremeter 
	$sql7 = "select * from transport where id=".$row['trans_name'];
    $result7 = mysqli_query($conn, $sql7);
    if (mysqli_num_rows($result7) > 0) 
    {
      $row7 = mysqli_fetch_assoc($result7);
    }
	$pdf->cell(50);
	$pdf->setFont('Arial','B',10);
	$pdf->Text(23, 147,"Transport :");
	$pdf->SetLineWidth(0);
	$pdf->Line(42, 146, 199, 146);
	$pdf->setFont('Arial');
	$pdf->Text(52, 144,"Name");
	$pdf->setFont('Arial');
	$pdf->Text(52, 150,$row7['trans_name']);
	$pdf->setFont('Arial');
	$pdf->Text(80, 144,"Vehicle No");
	$pdf->cell(50);
	$pdf->setFont('Arial');
	$pdf->setFontSize(9);
	$veh_no=array();
	if($row['vehicle_no']!='')
	{
		$veh_no=json_decode($row['vehicle_no']);
	}
	$position = 140;
	$column=80;
	$column2=80;
	$position2=$position+15;
	$column3=80;
	$position3=$position2+5;
	$position += 10;
// 	$veh_no = array("GJ3V9686","GJ3V9686","GJ3V9686","GJ3V9686","GJ3V9686","GJ3V9686","GJ3V9686","GJ3V9686","GJ3V9686","GJ3V9686","GJ3V9686","GJ3V9686");
	if ($row['no_of_vehicle']!='' && $row['no_of_vehicle']>0) 
	{
		foreach ($veh_no as $key => $vno) 
		{
			if($key<=3)
			{
			    $pdf->Text($column, $position,$vno.',');
			    $column+=20;
			}
			else if($key>=4 && $key<=7)
			{
			    $pdf->Text($column2, $position2,$vno.',');
			    $column2+=20;
			    $position=$position2;
			}
			else if($key>=8 && $key<=11)
			{
				$pdf->Text($column3, $position3,$vno.',');
			    $column3+=20;
			    $position=$position3;
			}
		}
	}
	else{
		$pdf->Text(100, $position,"");
	}
	$pdf->setFont('Arial');
	$pdf->setFontSize(10);
	$pdf->Text(170, 144,"Station");
	$pdf->cell(50);
	$pdf->setFont('Arial');
	$pdf->Text(170, 150,$row['station']);
	// Insurance 
	$sql3 = "select * from insurance where firm_id=".$row['firm'];
    $result3 = mysqli_query($conn, $sql3);
    if (mysqli_num_rows($result3) > 0) 
    {
      $row3 = mysqli_fetch_assoc($result3);
    }
	$pdf->cell(50);
	$pdf->setFont('Arial','B',10);
	$pdf->Text(23, $position+=12,"Insurance :");
	$pdf->setFont('Arial');
	$pdf->Text(52, $position,$row['ins_cmpny']);
	$pdf->setFont('Arial');
	$pdf->setFont('Arial');
	$pdf->Text(110, $position,"No :");
	$pdf->setFont('Arial','B',10);
	$pdf->Text(120, $position,$row['ins_policy_no']);
	$pdf->setFont('Arial');
	// Terms & Condition
	$pdf->cell(50);
	$pdf->setFont('Arial','B',10);
	$pdf->Text(23, $position+=12,"Terms & Condition :");
	$pdf->SetLineWidth(0);
	$pdf->Line(57, $position-1, 199, $position-1);
	$pdf->setFont('Arial');
	$pdf->setFontSize(9);
	$pdf->SetLeftMargin(22);
	$pdf->SetXY(100,$position-4);
	$content = $row['term_condtion'];
	$content = str_replace("&nbsp;", " ", $content);
	$content = html_entity_decode($content);
	$pdf->WriteHTML($content);
	//set stamp
	$stamp_logo=$pdf->Image($stamp_image,145,260,30,21,$stamp_img_extension);
	$pdf->cell(130,10,$stamp_logo,10,10,'C');
	//sign section
//$pdf->SetLineWidth(0.5);
//$pdf->Line(130, 275, 190, 275);
	$pdf->setFont('Arial');
	$pdf->setFontSize(10);
	$pdf->Text(143, 285,"Authorized Signatory");
	$pdf->setFont('Arial','B',12);
	$pdf->Text(132, 290,$row1['party_name']);
	$pdf->output();







	//save pdf in folder
	date_default_timezone_set("Asia/Kolkata");
    $timestamp=date("ymd_His");
	$filename="PC_".$row['pur_conf'].'_'.$timestamp.'.pdf';


	include('../global_function.php'); 
    $data=getFileStoragePath("pur_conf",$_SESSION['pur_financial_year_id']);  //function from global_function file
    $root_path=$data[0]; // file move path
    $store_path=$data[1]; // db store path


    $pdf->Output($root_path.$filename,'F');

	$filename=$store_path.$filename;
   
    //insert pdf record in db
    $timestamp2=date("Y-m-d H:i:s");
   $sql_pdf="insert into pdf (table_indicator,record_id,file_name,username,created_at) value('3','".$_GET['id']."','".$filename."','".$_SESSION['username']."','".$timestamp2."')";
   mysqli_query($conn,$sql_pdf);
}
?>