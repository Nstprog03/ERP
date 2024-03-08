<?php

session_start();

require_once('../db.php');

require_once '../PHPLibraries/PHPWord/autoload.php';



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





	$pw = new \PhpOffice\PhpWord\PhpWord();

	$section = $pw->addSection();

	$pw->setDefaultParagraphStyle(

	array(

	//'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::LEFT,

	'align'=>'both',

	'spacing' => 200,

	'lineHeight' => 1,

	)

	);



	







	

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

   

    $contact1='';

    

    $sqlFirm = "select * from party where id='".$row['firm_id']."'";

    $resultFirm = mysqli_query($conn, $sqlFirm);



    $rowFirm = mysqli_fetch_assoc($resultFirm);

   

    if(mysqli_num_rows($resultFirm)>0)

    {

      $firm_name=strtoupper($rowFirm['party_name']);

      $firm_add1=$rowFirm['party_address'].', '.$rowFirm['city'].',';

	  $firm_add2='Dist. '.$rowFirm['district'].', '.$rowFirm['state'].' - '.$rowFirm['pincode'];



	 /*   $path = "../firm/files/logo/".$rowFirm['logo_img'];

		$type = pathinfo($path, PATHINFO_EXTENSION);

		$data = file_get_contents($path);

		$company_logo = 'data:image/' . $type . ';base64,' . base64_encode($data);*/



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









//date 

$pw->addFontStyle('r2Style', array('bold'=>false, 'italic'=>false, 'size'=>10));

$pw->addParagraphStyle('p2Style', array('align'=>'right', 'spaceAfter'=>100));

$section->addText('Date : '.$date, 'r2Style', 'p2Style');





$html="

<html>

<head>





</head>



<body> ";





/*header section end */

$html.="

<div>



	<div class='ext_party_section'>

		<div>To,</div>

		<div>".$firm_name."</div>

		<div>".$firm_add1."</div>

		<div>".$firm_add2."</div>

	</div>



	<br/>



	<div style='text-align:justify;'>

		This is with reference to applicability of TDS u/s 194Q of the Act on purchase of goods made/to be made by ".$ext_party." starting from 01.07.2021

	</div>



	<br/>



	<ol type='1'>

	    <li style='text-align:justify;'>Total sales, gross receipts, or turnover of the company (".$ext_party.") from business during FY ".$year." was more than Rs.10 crores. Therefore, provisions of Section 194Q are applicable to the company. Accordingly, the company will deduct tax at source at the applicable rates from 1st July 2021 on amount credited/paid towards purchase of goods exceeding ".$row['good_exceeding'].".</li>

	    <li style='text-align:justify;'>It is requested that provisions of section 206C(1H) of the Act need not be applied by the seller (i.e. ".$firm_name.") on sale of goods to the company (".$ext_party.")</li>

	    <li style='text-align:justify;'>".$ext_party." has field Income Tax Return for previous years i.e. Assessment year (AY)  ".$assement_year_txt.".

		</li>

	</ol>



	<br/>



	<div>



	</div>





</div>

";

	



$html.='</body></html>';



\PhpOffice\PhpWord\Shared\Html::addHtml($section, $html, false, false);





// 1. Basic table

$tableStyle = array(

    'borderColor' => 'black',

    'borderSize'  => 6,

    'cellMargin'  => 50,

    'width'=>100,

);

$firstRowStyle = array('bgColor' => 'white');

$pw->addTableStyle('tbl1', $tableStyle, $firstRowStyle);









$table = $section->addTable('tbl1');



$myFontStyle = array('bold' => true, 'align' => 'center');



$table->addRow();

$table->addCell(2000)->addText("Sl No",$myFontStyle = array('bold' => true, 'align' => 'center'));

$table->addCell(4000)->addText("Assessment Year",$myFontStyle = array('bold' => true, 'align' => 'center'));

$table->addCell(4000)->addText("Acknowledgment No.",$myFontStyle = array('bold' => true, 'align' => 'center'));

$table->addCell(4000)->addText("Date of Filling",$myFontStyle = array('bold' => true, 'align' => 'center'));

$table->addCell(4000)->addText("Due Date Of Filling",$myFontStyle = array('bold' => true, 'align' => 'center'));



$table->addRow();

$table->addCell(1750)->addText("1",array('align' => 'center'));

$table->addCell(1750)->addText("");

$table->addCell(1750)->addText("");

$table->addCell(1750)->addText("");

$table->addCell(1750)->addText($auditDataArr[0]['due_date']);



$table->addRow();

$table->addCell(1750)->addText("2",$myFontStyle = array('align' => 'center'));

$table->addCell(1750)->addText("");

$table->addCell(1750)->addText("");

$table->addCell(1750)->addText("");

$table->addCell(1750)->addText($auditDataArr[1]['due_date']);







//concultion



$section->addTextBreak(1);

$section->addText('Conclusion : ');



$section->addTextBreak(0.5);

$section->addText('In our case section 194Q of Income tax Act is applicable to us, TCS Should not be deduct in your Sales Invoice raise towards us.');



    



$section->addTextBreak(1.5);

$section->addText('For, '.$ext_party);



$section->addTextBreak(3);

$section->addText('Authorized Signature');



    











$timestamp1=date("dmY_His");

$filename="tds_tcs_declaration_".$timestamp1.".docx";



header('Content-Type: application/octet-stream');

header("Content-Disposition: attachment;filename=".$filename);



/*$path="word_files/";

$check=file_put_contents($pw->save($path.$filename, "Word2007"));

*/

$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($pw, 'Word2007');

$objWriter->save('php://output');





/*

if($check)

{

  $timestamp=date("Y-m-d H:i:s");

   $sql_pdf="insert into pdf (table_indicator,record_id,file_name,username,created_at) value('4','".$_GET['id']."','".$filename."','".$_SESSION['username']."','".$timestamp."')";

   mysqli_query($conn,$sql_pdf);

}*/





}

?>