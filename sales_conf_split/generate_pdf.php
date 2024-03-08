<?php
session_start();
include('../db.php');
//require('../PHPLibraries/FPDF_library/fpdf.php');
require('../PHPLibraries/FPDF_library/html2pdf.php');

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
    exit;
}
if(!isset($_SESSION['sales_conf_firm_id']) && !isset($_SESSION['sales_financial_year_id']))
{
  header('Location: ../sales_conf_index.php');
}
if(isset($_GET['id']))
{
	$id=$_GET['id'];
	$sql = "select 
	s.*, p.*,
	ep.partyname as ep_partyname, 
	ep.address as ep_address,
	ep.gstin as ep_gst,
	ep.address as ep_address,
	ep.city as ep_city,
	ep.district as ep_district,
	ep.pincode as ep_pincode
	from 
	sales_conf_split s, party p, external_party ep
	where 
	s.firm=p.id AND 
	s.split_party_name=ep.id AND
	s.id='".$id."'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) 
    {
      $row = mysqli_fetch_assoc($result);
    }
    //print_r($row);
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
	$pdf->SetTitle('Sales Confirmation PDF');
	$pdf->AddPage();
	$pdf->setFont('Arial','B',14);
//------------header section---------------------
	//set head logo
	$head_logo=$pdf->Image($logo_image,10,10,50,20,$img_extension);
	$pdf->cell(55,10,$head_logo,0,0,'C');
	// set addresss
	$pdf->setFont('Arial','B',8);
	$addresss=$pdf->Multicell(0,3,"Manufacturer & Exporter : \nContamination Control Cotton Bales and Cotton Seeds");
	$pdf->setFont('Arial');
	$pdf->Ln(2);
	$pdf->cell(55);
	$partySQL = "select * from party where id='".$row['firm']."'";
    $partyresult = mysqli_query($conn, $partySQL);
    $partyrow = mysqli_fetch_assoc($partyresult);
	// GST header
	$pdf->setFont('Arial','B',9);
	$pdf->Text(163, 13,"GST IN : ".$partyrow['gst_in']);
	// address header
	$pdf->setFont('Arial');
	$pdf->setFontSize(8.5);
	$pdf->Text(66, 20,$partyrow['party_address'].', '.$partyrow['city'].', Dist.'.$partyrow['district'].', '.$partyrow['state'].' - '.$partyrow['pincode']);
	//dynamiac email
	$emailArr=array();
	$countEmail=0;
	if($partyrow['party_email']!='')
	{
		$emailArr=json_decode($partyrow['party_email']);
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
				$pdf->Text(66, $email_position,$email);
	   			$email_position+=4;
			}
		}
	}
	//dynamic contact no
	$contactArr=array();
	$countContact=0;
	if($partyrow['contact_number']!='')
	{
		$contactArr=json_decode($partyrow['contact_number']);
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
				$pdf->Text(66, $contact_position,$contactNo);
	   			$contact_position+=4;
			}
		}
	}
	$pdf->SetXY(202,30);
	$pdf->cell(-40,-150);
	$titleText="SALES\nCONFIRMATION";
	$pdf->setFont('Arial','B',15);
	$pdf->Multicell(100,5,$titleText);
	// conf type
	$conf_type=["Original","Revised","Cancel"];
	$pdf->setFont('Arial');
	$pdf->setFontSize(7);
	$pdf->Text(192, 43,strtoupper($conf_type[$row['conf_type']]));
	//add line
	$pdf->SetLineWidth(1);
	$pdf->Line(5, 45, 205, 45);
//------------header section END---------------------
	$pdf->cell(50);
	$pdf->setFont('Arial');
	$pdf->setFontSize(12);
	$pdf->Text(12, 55,chr(127)." Vendor Details");
	$pdf->setFont('Arial','B',12);
	$pdf->Text(20, 63,$row['party_name']);
	$pdf->setFont('Arial');
	$pdf->setFontSize(10);
	$pdf->Text(20, 69,'GST No. '.$row['gst_in']);
	$pdf->SetLineWidth(0.5);
	$pdf->Line(100, 50, 100, 75);
	$pdf->setFont('Arial','B',12);
	$pdf->Text(105, 55,"Sales Order No.  : ");
	$pdf->setFont('Arial');
	$pdf->Text(145, 55,$row['conf_split_no']);
	$conf_split_date='';
	if($row['conf_split_date']!='' && $row['conf_split_date']!='0000-00-00')
	{
	  $conf_split_date=date('d/m/Y', strtotime($row['conf_split_date']));
	}
	$pdf->setFont('Arial','B',12);
	$pdf->Text(105, 62,"Date Of Bargin    : ");
	$pdf->setFont('Arial');
	$pdf->Text(145, 62,$conf_split_date);
	$dispatch_date='';
    if($row['dispatch_date']!='' && $row['dispatch_date']!='0000-00-00')
    {
      $dispatch_date=date('d/m/Y', strtotime($row['dispatch_date']));
    }
	$pdf->setFont('Arial','B',12);
	$pdf->Text(105, 69,"Date Of Dispatch : ");
	$pdf->setFont('Arial','B',9);
	$pdf->Text(105, 73,"(Expected)");
	$pdf->setFont('Arial');
	$pdf->setFontSize(12);
	$pdf->Text(145, 69,$dispatch_date);
	$pdf->SetLineWidth(0.5);
	$pdf->Line(10, 78, 203, 78);
	$pdf->setFont('Arial');
	$pdf->setFontSize(12);
	$pdf->Text(12, 85,chr(127)." Buyer Details");
	$pdf->setFontSize(10);
	$pdf->Text(14, 93,"Billing To :");
	$pdf->setFont('Arial','B',12);
	$pdf->Text(33, 93,$row['ep_partyname']);
	$pdf->setFont('Arial');
	$pdf->setFontSize(10);
	$pdf->Text(33, 99,$row['ep_address'].', '.$row['ep_city'].' - '.$row['ep_pincode']);
	$pdf->setFont('Arial');
	$pdf->setFontSize(10);
	$pdf->Text(33, 105,'GST No. : '.$row['ep_gst']);
		$sql = "select * from external_party where id='".$row['shipping_ext_party_id']."'";
		$result = mysqli_query($conn, $sql);
		$row2=mysqli_fetch_array($result);
	$pdf->setFontSize(10);
	$pdf->Text(110, 93,"Shipping To :");
	$pdf->setFont('Arial','B',12);
	$pdf->Text(132, 93,$row2['partyname']);
	$pdf->setFont('Arial');
	$pdf->setFontSize(10);
	$pdf->Text(132, 99,$row2['address'].', '.$row2['city'].'-'.$row2['pincode']);
	$pdf->setFont('Arial');
	$pdf->setFontSize(10);
	$pdf->Text(132, 105,'GST No. : '.$row2['gstin']);
	$pdf->SetLineWidth(0.5);
	$pdf->Line(10, 110, 203, 110);
	$pdf->setFont('Arial');
	$pdf->setFontSize(12);
	$pdf->Text(12, 124,chr(127)." Product and Quality Details");
  $products = "select * from products where id='".$row['product']."'";
  $products_result = mysqli_query($conn, $products);
  $products_row = mysqli_fetch_assoc($products_result);
  $products_name='';
  if(isset($products_row))
  {
    $products_name=$products_row['prod_name'];
  }
	$pdf->SetXY(12,130);
	$pdf->Cell(45,8,'Product : ',0,0,'L');
	$pdf->Cell(45,8,$products_name,0,1,'L');
	//product quality
	  $sql_pq = "select * from product_sub_items where id='".$row['prod_quality']."'";
	  $pq_result = mysqli_query($conn, $sql_pq);
	  $pq_row = mysqli_fetch_assoc($pq_result);
	  $prod_quality='';
	  if(isset($pq_row))
	  {
	    $prod_quality=$pq_row['value'];
	  }
	$pdf->SetXY(12,135);
	$pdf->Cell(45,8,'Quality : ',0,0,'L');
	$pdf->Cell(45,8,$prod_quality,0,1,'L');
	$LotArr=json_decode($row['lot_no']);
	$pdf->SetXY(12,140);
	$pdf->Cell(45,8,'LOT No. : ',0,0,'L');
	$pdf->Cell(45,8,current($LotArr).' To '.end($LotArr),0,1,'L');
	$pdf->SetXY(12,145);
	$pdf->Cell(45,8,'Press No. : ',0,0,'L');
	$pdf->Cell(45,8,$row['press_no'],0,1,'L');
	$pdf->SetXY(12,150);
	$pdf->Cell(45,8,'Quantity (In Bales) : ',0,0,'L');
	$pdf->Cell(45,8,$row['no_of_bales'],0,1,'L');
	$pdf->SetXY(12,155);
	$pdf->Cell(45,8,'Rate In Rs.(Candy) : ',0,0,'L');
	$pdf->Cell(45,8,$row['price'],0,1,'L');
	//product variety
	  $sql_pv = "select * from product_sub_items where id='".$row['variety']."'";
	  $pv_result = mysqli_query($conn, $sql_pv);
	  $pv_row = mysqli_fetch_assoc($pv_result);
	  $prod_variety='';
	  if(isset($pv_row))
	  {
	    $prod_variety=$pv_row['value'];
	  }
	$pdf->SetXY(12,160);
	$pdf->Cell(45,8,'Variety : ',0,0,'L');
	$pdf->Cell(45,8,$prod_variety,0,1,'L');
	//sub product variety
	  $sql_sv = "select * from product_sub_items where id='".$row['sub_variety']."'";
	  $sv_result = mysqli_query($conn, $sql_sv);
	  $sv_row = mysqli_fetch_assoc($sv_result);
	  $prod_sub_var='';
	  if(isset($sv_row))
	  {
	    $prod_sub_var=$sv_row['value'];
	  }
	$pdf->SetXY(12,165);
	$pdf->Cell(45,8,'Sub Variety : ',0,0,'L');
	$pdf->Cell(45,8,$prod_sub_var,0,1,'L');
	$gstCheck=0;
	if($row['tax_type']=='sgst')
	{
		$gstCheck=1;
		$pdf->SetXY(12,170);
		$pdf->Cell(45,8,'SGST : ',0,0,'L');
		$pdf->Cell(45,8,$row['sgst'].'%',0,1,'L');
		$pdf->SetXY(12,175);
		$pdf->Cell(45,8,'CGST : ',0,0,'L');
		$pdf->Cell(45,8,$row['cgst'].'%',0,1,'L');
	}
	else
	{
		$pdf->SetXY(12,170);
		$pdf->Cell(45,8,'IGST : ',0,0,'L');
		$pdf->Cell(45,8,$row['igst'].'%',0,1,'L');
	}
	if($gstCheck!=0)
	{
		$nextValue=180;
	}
	else
	{
		$nextValue=175;
	}
	$pdf->SetXY(12,$nextValue);
	$pdf->Cell(45,8,'Station : ',0,0,'L');
	$pdf->Cell(45,8,$row['station'],0,1,'L');
	$pdf->setFont('Arial');
	$pdf->setFontSize(12);
	$pdf->Text(12,195,chr(127)." Contracted Parameter");
	$pdf->SetXY(12,197);
	$pdf->Cell(45,8,'Staple Length : ',0,0,'L');
	$pdf->Cell(45,8,$row['length'].' MM',0,1,'L');
	$pdf->SetXY(12,202);
	$pdf->Cell(45,8,'Strength : ',0,0,'L');
	$pdf->Cell(45,8,$row['strength'].' MM',0,1,'L');
	$pdf->SetXY(12,207);
	$pdf->Cell(45,8,'Micronair : ',0,0,'L');
	$pdf->Cell(45,8,$row['mic'],0,1,'L');
	$pdf->SetXY(12,212);
	$pdf->Cell(45,8,'Trash : ',0,0,'L');
	$pdf->Cell(45,8,$row['trash'],0,1,'L');
	$pdf->SetXY(12,217);
	$pdf->Cell(45,8,'Mositure : ',0,0,'L');
	$pdf->Cell(45,8,$row['moi'],0,1,'L');
	$pdf->SetXY(12,222);
	$pdf->Cell(45,8,'RD : ',0,0,'L');
	$pdf->Cell(45,8,$row['rd'],0,1,'L');
	$pdf->SetLineWidth(0.5);
	$pdf->Line(105, 110, 105, 250);
	$pdf->setFont('Arial');
	$pdf->setFontSize(12);
	$pdf->Text(120, 124,chr(127)." Contract and Conditions");
	$pdf->setFont('Arial');
	$pdf->setFontSize(11);
	 $pdf->SetLeftMargin(110);
	 $pdf->SetXY(240,120);
	 $pdf->setFontSize(9);
	 $content = $row['bill_inst'];
	 $content = str_replace("&nbsp;", " ", $content);
	 $content = str_replace("&nbsp; ", " ", $content);
	 $content = html_entity_decode($content);
	 $pdf->WriteHTML($content);
	//bottom last border
	$pdf->SetLineWidth(0.5);
	$pdf->Line(10, 250, 203, 250);
	//set stamp
	$stamp_logo=$pdf->Image($stamp_image,145,260,30,21,$stamp_img_extension);
	$pdf->cell(130,10,$stamp_logo,10,10,'C');
	//sign section
	/*$pdf->SetLineWidth(0.5);
	$pdf->Line(130, 275, 190, 275);*/
	$pdf->setFont('Arial');
	$pdf->setFontSize(10);
	$pdf->Text(143, 285,"Authorized Signatory");
	$pdf->setFont('Arial','B',12);
	$pdf->Text(132, 290,$row['party_name']);
	$pdf->output();




	include('../global_function.php'); 
    $data=getFileStoragePath("sales_conf_split",$_SESSION['sales_financial_year_id']);  //function from global_function file
    $root_path=$data[0]; // file move path
    $store_path=$data[1]; // db store path

	//save pdf in folder
	date_default_timezone_set("Asia/Kolkata");
    $timestamp=date("ymd_His");
	$filename="SCS_".$row['conf_split_no'].'_'.$timestamp.'.pdf';
    $pdf->Output($root_path.$filename,'F');

    $filename=$store_path.$filename;

    //insert pdf record in db
    $timestamp2=date("Y-m-d H:i:s");
   $sql_pdf="insert into pdf (table_indicator,record_id,file_name,username,created_at) value('2','".$_GET['id']."','".$filename."','".$_SESSION['username']."','".$timestamp2."')";
   mysqli_query($conn,$sql_pdf);
}
?>