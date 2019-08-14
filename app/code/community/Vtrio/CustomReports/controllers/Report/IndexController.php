<?php

class Vtrio_CustomReports_Report_IndexController extends Mage_Adminhtml_Controller_Action
{

   protected function _isAllowed()
    {
        $act = $this->getRequest()->getActionName();
        if (!$act)
            $act = 'default';
        switch ($act) {
            case 'default':
                break;
            default:
                return Mage::getSingleton('admin/session')->isAllowed('report/customreport');
                break;
        }
    }
 
    public function _initAction()
    {
        $act = $this->getRequest()->getActionName();
        if (!$act)
            $act = 'default';
        if ($act == 'default')
        {
            $this->loadLayout()
                    ->_addBreadcrumb(Mage::helper('customreports')->__('Reports'), Mage::helper('customreports')->__('Reports'));
        }else{
            $this->loadLayout()
                    ->_addBreadcrumb(Mage::helper('customreports')->__('Reports'), Mage::helper('customreports')->__('Reports'));
			}
        return $this;
    }


    public function customReportAction()
    {
        $this->_title($this->__('Custom Reports'));
        $this->_initAction()
                ->_setActiveMenu('report/customreport')
                ->_addBreadcrumb(Mage::helper('customreports')->__('Custom Report'), Mage::helper('customreports')->__('Custom Report'))
                ->_addContent($this->getLayout()->createBlock('customreports/report_custom'))
                ->renderLayout();
    }

    public function gridAction()
    {
        $this->loadLayout();
		  $this->renderLayout();
        $this->getResponse()->setBody(
                $this->getLayout()->createBlock('customreports/report_custom_grid')->toHtml()
        );
    }

    public function getsubcategoryAction()
    {
			$catId	=	$this->getRequest()->getParam('catId');
			$helper 	= 	Mage::helper('customreports');
			$children = Mage::getModel('catalog/category')->getCategories($catId);
			?>
			<select class=" select" title="Period" name="subcategory" id="subcategory">			
				<option value="">----------------------Please Select----------------------</option>
			<?php
			foreach ($children as $subCategory) { 
				$selectedSubCategory = ($subCatId == $subCategory->getId())?'selected = true':'';
				$id	=	$subCategory->getId();
				$name	=	$subCategory->getName();
				$option .="<option value='$id' $selectedSubCategory >$name</option>";
				$option .= $helper->nestedSubCategory($id,3);
			}
			echo $option;
			?>
			</select>			
			<?php
    }

	public function csv_exportAction(){
		
		$catId			=	$this->getRequest()->getParam('catId');
		$subCatId		=	$this->getRequest()->getParam('subCatId');
		$from				=	$this->getRequest()->getParam('from');
		$to				=	$this->getRequest()->getParam('to');
		$report_period	=	$this->getRequest()->getParam('period');
		$store_ids		=	$this->getRequest()->getParam('store_id');
		$order_statuses=	$this->getRequest()->getParam('order_statuses');

		$model 			= 	Mage::getModel('customreports/collection');
		$reportArr 		= 	$model->getReportCSV($catId,$subCatId,$from,$to,$report_period,$store_ids,$order_statuses);

		$helper 			= 	Mage::helper('customreports');
		$returnResult 	= 	$helper->exportReportToCSV($reportArr,$report_period);
	}

}
