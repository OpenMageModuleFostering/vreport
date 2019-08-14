<?php

class Vtrio_CustomReports_Model_Collection extends Mage_Core_Model_Abstract
{

    protected function _construct()
    {
        parent::_construct();
    }

	public function getReport($catId,$subCatId,$from,$to,$report_period,$store_id,$order_statuses,$start,$limit){

		$reportArr	=	array();		

		if($from && $to){

			$fromExp	=	explode('/',$from);
			$toExp	=	explode('/',$to);

			if(!empty($order_statuses)){
					$status = " and sfo.status in (";
					for($i = 0;$i<count($order_statuses);$i++){

						if($i==count($order_statuses)-1){
							$status .= "'".$order_statuses[$i]."')";
						}else{
							$status .= "'".$order_statuses[$i]."',";
						}
					}
			}else{
				$status = "";
			}
			
			$fromDate =	$fromExp[2]."-".$fromExp[0]."-".$fromExp[1]."  00:00:00";
			$toDate =	$toExp[2]."-".$toExp[0]."-".$toExp[1]."  23:59:00";		

			$store_id	=		(trim($store_id))?" and sfoi.store_id='$store_id'":'';
			$catalog_category_table= Mage::getSingleton('core/resource')->getTableName('catalog_category_product'); 
			if($catId){
				$category = " left join $catalog_category_table as ccp on ccp.product_id=sfoi.product_id ";
				if($subCatId){
					$categoryWhere	=	(trim($subCatId))?" and ccp.category_id='$subCatId'":'';
				}else{
					$categoryWhere	=	(trim($catId))?" and ccp.category_id='$catId'":'';
				}
			}else{
				$category	=	"";
				$categoryWhere	=	"";
			}
			
			
		   $sale_flat_table= Mage::getSingleton('core/resource')->getTableName('sales_flat_order_item'); 
			$sales_flat_order_table =  Mage::getSingleton('core/resource')->getTableName('sales_flat_order'); 
			
			$sql = "SELECT sfoi.created_at, sfoi.name, sfoi.sku, sfoi.price, sfoi.qty_ordered FROM $sale_flat_table as sfoi
									left join $sales_flat_order_table as sfo on sfo.entity_id=sfoi.order_id
										$category
										WHERE sfoi.created_at between '$fromDate' and '$toDate' $store_id $categoryWhere $status and sfoi.price >0 and sfoi.base_price >0
										order by sfoi.created_at LIMIT $start, $limit";			
										
			$connection = Mage::getSingleton('core/resource')->getConnection('core_read');
			$reportArr	=	$connection->fetchAll($sql);			
		}
		return $reportArr;
    }
	
	public function gridColSpan($gridVal,$period){
		
	    $colSpanArr	=	array();
	    $dateValOld = "";
	    $i=0;
		 $totalCount = 0;

		 if($period == 'month'){
			 foreach($gridVal as $val){
				$dateVal1	=	explode("-",$val['created_at']);
				$dateVal	=	$dateVal1[1]."/".$dateVal1[0];
				if($dateVal != $dateValOld && $i>1){
				 	array_push($colSpanArr,$i);
					$totalCount = $totalCount+$i;
				 	$i=0;				
				}	
				$dateValOld = $dateVal;
				$i++;
			 }
			 if(!empty($colSpanArr)){
				array_push($colSpanArr,count($gridVal)-$totalCount);
			 }else if(!empty($gridVal) && empty($colSpanArr)){
				array_push($colSpanArr,count($gridVal));
			 }
		 }else if($period == 'year'){
			 foreach($gridVal as $val){
				$dateVal	=	explode("-",$val['created_at']);
				if($dateVal[0] != $dateValOld && $i>1){
				 	array_push($colSpanArr,$i);
					$totalCount = $totalCount+$i;
				 	$i=0;				
				}	
				$dateValOld = $dateVal[0];
				$i++;
			 }
			 if(!empty($colSpanArr)){
				array_push($colSpanArr,count($gridVal)-$totalCount);
			 }else if(!empty($gridVal) && empty($colSpanArr)){
				array_push($colSpanArr,count($gridVal));
			 }
		 }else{
			 foreach($gridVal as $val){
				$dateVal	=	explode(" ",$val['created_at']);
				if($dateVal[0] != $dateValOld && $i>1){
				 	array_push($colSpanArr,$i);
					$totalCount = $totalCount+$i;
				 	$i=0;				
				}	
				$dateValOld = $dateVal[0];
				$i++;
			 }
			 if(!empty($colSpanArr)){
				array_push($colSpanArr,count($gridVal)-$totalCount);
			 }else if(!empty($gridVal) && empty($colSpanArr)){
				array_push($colSpanArr,count($gridVal));
			 }
		 }

	    return $colSpanArr;
	}

	function getOrderStatus(){
		$sales_order_status	= 	Mage::getSingleton('core/resource')->getTableName('sales_order_status'); 
		$sql 						= "SELECT status, label FROM $sales_order_status order by label asc";
		$connection 			= Mage::getSingleton('core/resource')->getConnection('core_read');
		$statusArr				=	$connection->fetchAll($sql);	
		return $statusArr;
	}	

	public function getReportCSV($catId,$subCatId,$from,$to,$report_period,$store_id,$order_statuses){

		$reportArr	=	array();		

		if($from && $to){

			$fromExp	=	explode('/',$from);
			$toExp	=	explode('/',$to);

			if(!empty($order_statuses)){
					$status = " and sfo.status in (";
					for($i = 0;$i<count($order_statuses);$i++){

						if($i==count($order_statuses)-1){
							$status .= "'".$order_statuses[$i]."')";
						}else{
							$status .= "'".$order_statuses[$i]."',";
						}
					}
			}else{
				$status = "";
			}
			
			$fromDate =	$fromExp[2]."-".$fromExp[0]."-".$fromExp[1]."  00:00:00";
			$toDate =	$toExp[2]."-".$toExp[0]."-".$toExp[1]."  23:59:00";		

			$store_id	=		(trim($store_id))?" and sfoi.store_id='$store_id'":'';
			$catalog_category_table= Mage::getSingleton('core/resource')->getTableName('catalog_category_product'); 
			if($catId){
				$category = " left join $catalog_category_table as ccp on ccp.product_id=sfoi.product_id ";
				if($subCatId){
					$categoryWhere	=	(trim($subCatId))?" and ccp.category_id='$subCatId'":'';
				}else{
					$categoryWhere	=	(trim($catId))?" and ccp.category_id='$catId'":'';
				}
			}else{
				$category	=	"";
				$categoryWhere	=	"";
			}
			
			
		    $sale_flat_table= Mage::getSingleton('core/resource')->getTableName('sales_flat_order_item'); 
			$sales_flat_order_table =  Mage::getSingleton('core/resource')->getTableName('sales_flat_order'); 
			$sales_flat_order_address =  Mage::getSingleton('core/resource')->getTableName('sales_flat_order_address'); 
			$sql = "SELECT sfoi.created_at as date, sfoi.name, sfoi.sku, sfoi.price, sfoi.qty_ordered,
									(select CONCAT(sfoa.firstname,' ',sfoa.lastname,'\n',sfoa.street,'\n',sfoa.city,', ',sfoa.region,', ',sfoa.postcode,'\n',sfoa.country_id) from $sales_flat_order_address as sfoa where sfoa.entity_id=sfo.shipping_address_id) as shipping_address,
									(select CONCAT(sfoa1.firstname,' ',sfoa1.lastname,'\n',sfoa1.street,'\n',sfoa1.city,', ',sfoa1.region,', ',sfoa1.postcode,'\n',sfoa1.country_id) from $sales_flat_order_address as sfoa1 where sfoa1.entity_id=sfo.billing_address_id) as billing_address									
									FROM $sale_flat_table as sfoi
									left join $sales_flat_order_table as sfo on sfo.entity_id=sfoi.order_id
										$category
										WHERE sfoi.created_at between '$fromDate' and '$toDate' $store_id $categoryWhere $status and sfoi.price >0 and sfoi.base_price >0
										order by sfoi.created_at";
										
			$connection = Mage::getSingleton('core/resource')->getConnection('core_read');
			$reportArr	=	$connection->fetchAll($sql);
		}
		return $reportArr;
    }	
	
	
	
	public function getReportCSV1($catId,$subCatId,$from,$to,$report_period,$store_id,$order_statuses){

		$reportArr	=	array();		

		if($from && $to){

			$fromExp	=	explode('/',$from);
			$toExp	=	explode('/',$to);

			if(!empty($order_statuses)){
					$status = " and sfo.status in (";
					for($i = 0;$i<count($order_statuses);$i++){

						if($i==count($order_statuses)-1){
							$status .= "'".$order_statuses[$i]."')";
						}else{
							$status .= "'".$order_statuses[$i]."',";
						}
					}
			}else{
				$status = "";
			}
			
			$fromDate =	$fromExp[2]."-".$fromExp[0]."-".$fromExp[1]."  00:00:00";
			$toDate =	$toExp[2]."-".$toExp[0]."-".$toExp[1]."  23:59:00";		

			$store_id	=		(trim($store_id))?" and sfoi.store_id='$store_id'":'';
			$catalog_category_table= Mage::getSingleton('core/resource')->getTableName('catalog_category_product'); 
			if($catId){
				$category = " left join $catalog_category_table as ccp on ccp.product_id=sfoi.product_id ";
				if($subCatId){
					$categoryWhere	=	(trim($subCatId))?" and ccp.category_id='$subCatId'":'';
				}else{
					$categoryWhere	=	(trim($catId))?" and ccp.category_id='$catId'":'';
				}
			}else{
				$category	=	"";
				$categoryWhere	=	"";
			}
			
			
		   $sale_flat_table= Mage::getSingleton('core/resource')->getTableName('sales_flat_order_item'); 
			$sales_flat_order_table =  Mage::getSingleton('core/resource')->getTableName('sales_flat_order'); 
			
			$sql = "SELECT sfoi.created_at as date, sfoi.name, sfoi.sku, sfoi.price, sfoi.qty_ordered FROM $sale_flat_table as sfoi
									left join $sales_flat_order_table as sfo on sfo.entity_id=sfoi.order_id
										$category
										WHERE sfoi.created_at between '$fromDate' and '$toDate' $store_id $categoryWhere $status and sfoi.price >0 and sfoi.base_price >0
										order by sfoi.created_at";			
										
			$connection = Mage::getSingleton('core/resource')->getConnection('core_read');
			$reportArr	=	$connection->fetchAll($sql);			
		}
		return $reportArr;
    }

	public function getReportCount($catId,$subCatId,$from,$to,$report_period,$store_id,$order_statuses){

		$reportArr	=	array();		

		if($from && $to){

			$fromExp	=	explode('/',$from);
			$toExp	=	explode('/',$to);

			if(!empty($order_statuses)){
					$status = " and sfo.status in (";
					for($i = 0;$i<count($order_statuses);$i++){

						if($i==count($order_statuses)-1){
							$status .= "'".$order_statuses[$i]."')";
						}else{
							$status .= "'".$order_statuses[$i]."',";
						}
					}
			}else{
				$status = "";
			}
			
			$fromDate =	$fromExp[2]."-".$fromExp[0]."-".$fromExp[1]."  00:00:00";
			$toDate =	$toExp[2]."-".$toExp[0]."-".$toExp[1]."  23:59:00";		

			$store_id	=		(trim($store_id))?" and sfoi.store_id='$store_id'":'';
			$catalog_category_table= Mage::getSingleton('core/resource')->getTableName('catalog_category_product'); 
			if($catId){
				$category = " left join $catalog_category_table as ccp on ccp.product_id=sfoi.product_id ";
				if($subCatId){
					$categoryWhere	=	(trim($subCatId))?" and ccp.category_id='$subCatId'":'';
				}else{
					$categoryWhere	=	(trim($catId))?" and ccp.category_id='$catId'":'';
				}
			}else{
				$category	=	"";
				$categoryWhere	=	"";
			}
			
			
		   $sale_flat_table= Mage::getSingleton('core/resource')->getTableName('sales_flat_order_item'); 
			$sales_flat_order_table =  Mage::getSingleton('core/resource')->getTableName('sales_flat_order'); 
			
			$sql = "SELECT count(*) as totalCount FROM $sale_flat_table as sfoi
									left join $sales_flat_order_table as sfo on sfo.entity_id=sfoi.order_id
										$category
										WHERE sfoi.created_at between '$fromDate' and '$toDate' $store_id $categoryWhere $status and sfoi.price >0 and sfoi.base_price >0
										order by sfoi.created_at";			
										
			$connection = Mage::getSingleton('core/resource')->getConnection('core_read');
			$reportArr	=	$connection->fetchAll($sql);			
		}
		return $reportArr;
    }

}
