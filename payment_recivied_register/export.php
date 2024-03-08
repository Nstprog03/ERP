<?php

session_start();



include('../db.php');



require_once "../PHPLibraries/PHPSpreadSheet/vendor/autoload.php";

 

use PhpOffice\PhpSpreadsheet\Spreadsheet;

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Style\Border;

use PhpOffice\PhpSpreadsheet\Style\Color;



	$column_data = $_SESSION['column_data'];

	$sales_rcv_data = $_SESSION['sales_rcv_export_data'];

	$sales_report_data = $_SESSION['sales_report_export_data'];







	$spreadsheet = new Spreadsheet();

	$Excel_writer = new Xlsx($spreadsheet);



	$spreadsheet->setActiveSheetIndex(0);

	$activeSheet = $spreadsheet->getActiveSheet();





	$columnArr=array();





	

	//check availble column

	$columnArr[]='Sr. No.';



	if(isset($column_data['col_firm'])){

	  $columnArr[]='Firm';

	}

	if(isset($column_data['col_party'])){

	   $columnArr[]='Billng Party';

	}

	if(isset($column_data['col_del_city'])){

	   $columnArr[]='Delivery City';

	}

	if(isset($column_data['col_credit_days'])){

	   $columnArr[]='Credit Days';

	}

	if(isset($column_data['col_bill_date'])){

	   $columnArr[]='Bill Date';

	}

	if(isset($column_data['col_bill_no'])){

	   $columnArr[]='Bill No.';

	}

	if(isset($column_data['col_final_amt'])){

	   $columnArr[]='Bill Amount';

	}



	if(isset($column_data['col_due_date'])){

	   $columnArr[]='Due Date';

	}



	if(isset($column_data['col_due_days'])){

	   $columnArr[]='Due Days';

	}

	if(isset($column_data['col_rcvd_date'])){

	  $columnArr[]='Received Date';

	}



	if(isset($column_data['col_adhoc_amt'])){

	   

	   $columnArr[]='Payment Received Date';

	   $columnArr[]='Received Amount';

	}



	if(isset($column_data['col_credit_amt'])){

	   $columnArr[]='Credit Note Amount';

	}





	if(isset($column_data['col_debit_amt'])){

	   $columnArr[]='Debit Amount';

	}

	if(isset($column_data['col_gst_amt'])){

	  $columnArr[]='GST Amount';

	}



	if(isset($column_data['col_tcs_amt'])){

	   $columnArr[]='TCS Amount';

	}

	if(isset($column_data['col_tds_amt'])){

	   $columnArr[]='TDS Amount';

	}

	if(isset($column_data['col_other_amt'])){

	   $columnArr[]='Other Amount';

	}

	

	

	if(isset($column_data['col_net_amt'])){

	  $columnArr[]='Net Amount';

	}

	if(isset($column_data['col_rcvd_amt'])){

	  $columnArr[]='Total Received Amount';

	}

	if(isset($column_data['col_outstanding'])){

	   $columnArr[]='Out Standing Amount';

	}



	//generate excel header from avilable column

	$alphas = range('A', 'Z');



	foreach ($columnArr as $key => $columnHeader) {

		

		$activeSheet->setCellValue($alphas[$key].'1',$columnHeader);

		//$activeSheet->getStyle($alphas[$key])->getAlignment()->setWrapText(true); 

		$activeSheet->getColumnDimension($alphas[$key])->setAutoSize(true);

		$activeSheet->getStyle($alphas[$key])->getAlignment()->setHorizontal('center');

		$activeSheet->getStyle($alphas[$key].'1')->getFont()->setBold( true );

	}









	//print data from sales receivable table

		$i=2;

		$sr_no=0;

		foreach ($sales_rcv_data as $data) 

		{

				$k=0;

				$sr_no+=1;



				$activeSheet->setCellValue($alphas[$k++].$i,$sr_no);





				//firm	

				if(isset($column_data['col_firm']))

				{



			        $sql_firm = "select * from party where id='".$data['firm']."'";

			        $result_firm = mysqli_query($conn, $sql_firm);

			        $row_firm=mysqli_fetch_array($result_firm);



				 	$activeSheet->setCellValue($alphas[$k++].$i,$row_firm['party_name']);

				}







				//external party

				if(isset($column_data['col_party'])){

				 	

					  $sql = "select * from external_party where id='".$data['pur_party']."'";

			          $result = mysqli_query($conn, $sql);

			          $row_ext = mysqli_fetch_assoc($result);



				 	$activeSheet->setCellValue($alphas[$k++].$i,$row_ext['partyname']);







				}







				if(isset($column_data['col_del_city'])){

				 	$activeSheet->setCellValue($alphas[$k++].$i,$data['delivery_city']);

				}

				if(isset($column_data['col_credit_days'])){

				 	$activeSheet->setCellValue($alphas[$k++].$i,$data['credit_days']);

				}



				//bill date

				if(isset($column_data['col_bill_date']))

				{



				 	$bill_date='';

					if($data['due_date']!='' && $data['bill_date']!='0000-00-00')

					{



						$bill_date=date("d/m/Y", strtotime($data['bill_date']));

					}

				 	$activeSheet->setCellValue($alphas[$k++].$i,$bill_date);



				 

				}



				if(isset($column_data['col_bill_no'])){

				 	$activeSheet->setCellValue($alphas[$k++].$i,$data['bill_no']);

				}

				if(isset($column_data['col_final_amt'])){

				 	$activeSheet->setCellValue($alphas[$k++].$i,$data['total_value']);

				}





					//due date

				if(isset($column_data['col_due_date']))

				{

				 	

				 		$due_date='';

						if($data['due_date']!='' && $data['due_date']!='0000-00-00')

						{



							$due_date=date("d/m/Y", strtotime($data['due_date']));

						}

				 	$activeSheet->setCellValue($alphas[$k++].$i,$due_date);

				}







				//due Days dynamic count

				if(isset($column_data['col_due_days']))

				{



	                   $due_days='';

	                   if($data['due_date']!='' && $data['due_date']!='0000-00-00')

	                   {

	                         //count due days       

	                            date_default_timezone_set('Asia/Kolkata');

	                            $curDate=date('Y-m-d');

	                        

	                             //due days count

	                              $parakh_date=$data['parakh_date'];

	                              $date1 = date_create($curDate);

	                              $date2 = date_create($parakh_date);

	                              $diff = date_diff($date1,$date2);

	                              $due_days=$diff->format("%a")+1;

	                   } 



	                $activeSheet->setCellValue($alphas[$k++].$i,$due_days);



				}





				//receive date

				if(isset($column_data['col_rcvd_date']))

				{

						$rcvd_date='';

						if($data['received_date']!='' && $data['received_date']!='0000-00-00')

						{



							$rcvd_date=date("d/m/Y", strtotime($data['received_date']));

						}

				 	$activeSheet->setCellValue($alphas[$k++].$i,$rcvd_date);

				}













				//dynamic cell create for ad-hoc, debit amt, gst amt, tcs amt, tds amt, other amt



				$countArr=array();



			

				$j=0;





				if(isset($column_data['col_adhoc_amt']))

				{

					$currentCol=$k++;

					$j=$i;



					$adhoc_data=json_decode($data['adhoc_data']);



					foreach ($adhoc_data as $key => $value) 

					{



						$adhoc_date='';

						if($value->date!='' && $value->date!='0000-00-00')

						{



							$adhoc_date=date("d/m/Y", strtotime($value->date));

						}

						

						$activeSheet->setCellValue($alphas[$currentCol].$j,$adhoc_date);

						$j+=1;

					}

					$countArr[]=$j;

				}





				if(isset($column_data['col_adhoc_amt']))

				{

					$currentCol=$k++;

					$j=$i;



					$adhoc_data=json_decode($data['adhoc_data']);



					foreach ($adhoc_data as $key => $value) 

					{

						

						$activeSheet->setCellValue($alphas[$currentCol].$j,$value->adhoc_amount);

						$j+=1;

					}



					$countArr[]=$j;

				}





				if(isset($column_data['col_credit_amt'])){

				 	$activeSheet->setCellValue($alphas[$k++].$i,$data['credit_amt']);

				}





				



				if(isset($column_data['col_debit_amt']))

				{

					$currentCol=$k++;

					$j=$i;



					$debit_data=json_decode($data['debit_data']);



					foreach ($debit_data as $key => $value) 

					{

						

						$activeSheet->setCellValue($alphas[$currentCol].$j,$value->debit_amount);

						$j+=1;

					}

					$countArr[]=$j;

				}





				if(isset($column_data['col_gst_amt']))

				{

					$currentCol=$k++;

					$j=$i;



					$gst_data=json_decode($data['gst_data']);



					foreach ($gst_data as $key => $value) 

					{

						

						$activeSheet->setCellValue($alphas[$currentCol].$j,$value->gst_amount);

						$j+=1;

					}

					$countArr[]=$j;

				}





				if(isset($column_data['col_tcs_amt']))

				{

					$currentCol=$k++;

					$j=$i;



					$tcs_data=json_decode($data['tcs_data']);



					foreach ($tcs_data as $key => $value) 

					{

						

						$activeSheet->setCellValue($alphas[$currentCol].$j,$value->tcs_amount);

						$j+=1;

					}

					$countArr[]=$j;

				}



				if(isset($column_data['col_tds_amt']))

				{

					$currentCol=$k++;

					$j=$i;



					$tds_data=json_decode($data['tds_data']);



					foreach ($tds_data as $key => $value) 

					{

						

						$activeSheet->setCellValue($alphas[$currentCol].$j,$value->tds_amount);

						$j+=1;

					}

					$countArr[]=$j;

				}



				if(isset($column_data['col_other_amt']))

				{

					$currentCol=$k++;

					$j=$i;



					$other_data=json_decode($data['other_data']);



					foreach ($other_data as $key => $value) 

					{

						

						$activeSheet->setCellValue($alphas[$currentCol].$j,$value->other_amount);

						$j+=1;

					}

					$countArr[]=$j;

				}





				if(isset($column_data['col_net_amt'])){

				 	$activeSheet->setCellValue($alphas[$k++].$i,$data['net_amt']);

				}

				if(isset($column_data['col_rcvd_amt'])){

				 	$activeSheet->setCellValue($alphas[$k++].$i,$data['total_received']);

				}

				if(isset($column_data['col_outstanding'])){

				 	$activeSheet->setCellValue($alphas[$k++].$i,$data['OSAmount']);

				}





				//get max row increment from array

				//$i=$j-1;

		



				$i=max($countArr)-1;



				$i++;

		}

				







		//sales report data

		foreach ($sales_report_data as $data) 

		{

			$k=0;

			$sr_no+=1;



			$activeSheet->setCellValue($alphas[$k++].$i,$sr_no);



				//firm	

				if(isset($column_data['col_firm']))

				{



			        $sql_firm = "select * from party where id='".$data['firm']."'";

			        $result_firm = mysqli_query($conn, $sql_firm);

			        $row_firm=mysqli_fetch_array($result_firm);



				 	$activeSheet->setCellValue($alphas[$k++].$i,$row_firm['party_name']);

				}







				//external party

				if(isset($column_data['col_party'])){

				 	

					  $sql = "select * from external_party where id='".$data['party_name']."'";

			          $result = mysqli_query($conn, $sql);

			          $row_ext = mysqli_fetch_assoc($result);



				 	$activeSheet->setCellValue($alphas[$k++].$i,$row_ext['partyname']);







				}







				if(isset($column_data['col_del_city'])){

				 	$activeSheet->setCellValue($alphas[$k++].$i,$data['delivery_city']);

				}

				if(isset($column_data['col_credit_days'])){

				 	$activeSheet->setCellValue($alphas[$k++].$i,$data['credit_days']);

				}



				//bill date

				if(isset($column_data['col_bill_date']))

				{



				 	$bill_date='';

					if($data['invoice_date']!='' && $data['invoice_date']!='0000-00-00')

					{



						$bill_date=date("d/m/Y", strtotime($data['invoice_date']));

					}

				 	$activeSheet->setCellValue($alphas[$k++].$i,$bill_date);



				 

				}



				if(isset($column_data['col_bill_no'])){

				 	$activeSheet->setCellValue($alphas[$k++].$i,$data['invice_no']);

				}

				if(isset($column_data['col_final_amt'])){

				 	$activeSheet->setCellValue($alphas[$k++].$i,$data['total_value']);

				}

				if(isset($column_data['col_outstanding'])){

				 	$activeSheet->setCellValue('V'.$i,$data['total_value']);

				}



				$i++;



		}





		









	$filename = "payment_received_register_".date('d_m_Y') . ".xlsx";



    header('Content-Type: application/vnd.ms-excel');

    header('Content-Disposition: attachment;filename='. $filename);

    header('Cache-Control: max-age=0');

    $Excel_writer->save('php://output');







//echo "<pre>";

//print_r($column_data)





?>