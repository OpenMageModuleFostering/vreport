<?php

class Vtrio_CustomReports_Helper_Data extends Mage_Core_Helper_Abstract
{

    public function getNewstoreids()
    {
        $object = new Vtrio_CustomReports_Block_Report_Custom;
        $storeidarr = $object->Storeparam();

        return $storeidarr;
    }

	public function exportReportToCSV($reportArr,$report_period)
	{
		$headers	=	array('Period', 'Product Name', 'SKU', 'Total Price', 'Quantity Ordered', 'Billing Address', 'Shipping Address');
		$fp 	= 	fopen('php://output', 'w');
		if ($fp && $reportArr) {
			//echo "in";
			header('Content-Type: text/csv');
			header('Content-Disposition: attachment; filename="custom_report.csv"');
			header('Pragma: no-cache');
			header('Expires: 0');
			fputcsv($fp, $headers);
			foreach ($reportArr as $arr_row) {
				$values = array();
				$totalQty =	$totalQty+$arr_row['qty_ordered'];
				$totalPrice = $totalPrice+$arr_row['price'];
				$shippingAddr = $arr_row['shipping_address'];
				$billingAddr = $arr_row['billing_address'];
				if($report_period == 'month'){
					$dateVal1	=	explode("-",$arr_row['created_at']);
					$createdDate	=	$dateVal1[1]."/".$dateVal1[0];
				}else if($report_period == 'year'){
					$dateVal1	=	explode("-",$arr_row['created_at']);
					$createdDate	=	$dateVal1[0];
				}else{
					$date	=	date_create($arr_row['created_at']);
					$createdDate = date_format($date,"M d, Y");
				}
				array_push($values,$createdDate,$arr_row['name'],$arr_row['sku'],number_format($arr_row['price'], 2, '.', ''),number_format($arr_row['qty_ordered'], 2, '.', ''),$billingAddr,$shippingAddr);
				fputcsv($fp, $values);
			}
			$values = array();
			array_push($values,'','','','','');
			fputcsv($fp, $values);
			$values = array();
			array_push($values,'','Total','',number_format($totalPrice, 2, '.', ''),number_format($totalQty, 2, '.', ''));
			fputcsv($fp, $values);



			die;

		}

		else {

			echo "no result";	

		}			

	}  	 

	public function nestedSubCategory($subcatId,$space,$selectedId = null)
	{
		$children = Mage::getModel('catalog/category')->getCategories($subcatId);
		$helper 	= 	Mage::helper('customreports');
		$spaceVal = "";
		if(count($children)>0){
			for($i=1;$i<=$space;$i++){
				$spaceVal .= "&nbsp;";
			}
			foreach ($children as $subCategory) { 
				$selectedSubCategory = ($selectedId == $subCategory->getId())?'selected = true':'';
				$id	=	$subCategory->getId();
				$name	=	$subCategory->getName();
				$option .=	"<option value='$id' $selectedSubCategory>$spaceVal $name</option>";
				$option .=	$helper->nestedSubCategory($id,$space+3,$selectedId);
			}
		}else{
			return $option;	
		}
		return $option;
	}

	public function paginationCtrl($catId,$subCatId,$from,$to,$report_period,$store_ids,$order_statuses,$limit,$page){

		$model 							= 	Mage::getModel('customreports/collection');
		$reportArrCount 				= 	$model->getReportCount($catId,$subCatId,$from,$to,$report_period,$store_ids,$order_statuses);
		foreach ($reportArrCount as $reportArrCountVal) {
			$reportCount				=	$reportArrCountVal['totalCount'];
		}
	
		$limitConf						=	array('20','30','50','100');

		$targetpage						=	Mage::helper('core/url')->getCurrentUrl();	
		$stages 							= 	1;

		if($page){
			$start 						= 	($page - 1) * $limit; 
		}else{
			$start 						= 	0;	
		}	

		// Initial page num setup
		if ($page == 0){
			$page 						= 	1;
		}
		$prev 							= 	$page - 1;	
		$next 							= 	$page + 1;							
		$lastpage 						= 	ceil($reportCount/$limit);		
		$LastPagem1 					= 	$lastpage - 1;					


		$paginate 						=	"<div class='vtrioCustPagination'>";
		if($lastpage > 1){				
			// Previous
			if ($page > 1){
				$paginate				.=	"<a href='#' onclick='setPage($prev)'>previous</a>";
			}else{
				$paginate				.=	"<span class='disabled'>previous</span>";	
			}
			// Pages	
			if ($lastpage < 7 + ($stages * 2)){	// Not enough pages to breaking it up
				for ($counter = 1; $counter <= $lastpage; $counter++){
					if ($counter == $page){
						$paginate		.= "<span class='current'>$counter</span>";
					}else{
						$paginate		.= "<a href='#' onclick='setPage($counter)'>$counter</a>";
					}					
				}
			}else if($lastpage > 5 + ($stages * 2)){	// Enough pages to hide a few?
				// Beginning only hide later pages
				if($page < 1 + ($stages * 2)){
					for ($counter = 1; $counter < 4 + ($stages * 2); $counter++){
						if ($counter == $page){
							$paginate	.= "<span class='current'>$counter</span>";
						}else{
							$paginate	.= "<a href='#' onclick='setPage($counter)'>$counter</a>";
						}					
					}
					$paginate			.= "...";
					$paginate			.= "<a href='#' onclick='setPage($LastPagem)'>$LastPagem1</a>";
					$paginate			.= "<a href='#' onclick='setPage($lastpage)'>$lastpage</a>";		
				}else	if($lastpage - ($stages * 2) > $page && $page > ($stages * 2)) {// Middle hide some front and some back
				
					$paginate			.= "<a href='#' onclick='setPage(1)'>1</a>";
					$paginate			.= "<a href='#' onclick='setPage(2)'>2</a>";
					$paginate			.= "...";
					for ($counter = $page - $stages; $counter <= $page + $stages; $counter++){
						if ($counter == $page){
							$paginate	.= "<span class='current'>$counter</span>";
						}else{
							$paginate	.= "<a href='#' onclick='setPage($counter)'>$counter</a>";
						}					
					}
					$paginate		.= "...";
					$paginate		.= "<a href='#' onclick='setPage($LastPagem)'>$LastPagem1</a>";
					$paginate		.= "<a href='#' onclick='setPage($lastpage)'>$lastpage</a>";		
				}else{// End only hide early pages
					$paginate		.= "<a href='#' onclick='setPage(1)'>1</a>";
					$paginate		.= "<a href='#' onclick='setPage(2)'>2</a>";
					$paginate		.= "...";
					for ($counter = $lastpage - (2 + ($stages * 2)); $counter <= $lastpage; $counter++){
						if ($counter == $page){
							$paginate.= "<span class='current'>$counter</span>";
						}else{
							$paginate.= "<a href='#' onclick='setPage($counter)'>$counter</a>";
						}					
					}
				}
			}
				// Next
			if ($page < $counter - 1){ 
				$paginate		.= "<a href='#' onclick='setPage($next)'>next</a>";
			}else{
				$paginate		.= "<span class='disabled'>next</span>";
			}
			$viewPerPage		=	"&nbsp;&nbsp; View <select name='limit' onchange='getPaginationLimit(this)'>";
			foreach($limitConf as $limits){
				$selected		=	($limits == $limit)?'selected=true':'';
				$viewPerPage		.=	"<option value='$limits' $selected>$limits</option>";
			}
			$viewPerPage		.=	"</select>  per page ";

			$paginate			.= $viewPerPage ." | &nbsp;&nbsp;";			
		}

		if($reportCount){
			$paginate			.= " Total ".$reportCount." records found </div>";		
		}else{
			//$paginate			.= " No records found. </div>";			
			$paginate			.= "</div>";			
		}				

		$returnVal							=	array();
		$returnVal['start']				=	$start;
		$returnVal['reportCount']		=	$reportCount;
		$returnVal['paginationLink']	=	$paginate;

		return $returnVal;		
	}
}
