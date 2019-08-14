<?php


class Vtrio_CustomReports_Block_Report_Custom_Grid extends Mage_Adminhtml_Block_Report_Grid
{

    protected $_subReportSize = 0;

    /**
     * Initialize Grid settings
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setFilterVisibility(true);
        $this->setPagerVisibility(true);
        $this->setId('customReport');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setDefaultLimit(1);
        $this->setTemplate('vtrio/customreports/gridproduct.phtml');
    }

    protected function _prepareColumns()
    {
    	  $this->addColumn('name', array(
            'header' => Mage::helper('reports')->__('Product Name'),
            'index' => 'sales_items',
            'align' => 'right',
				'width' => '300px',
            'total' => 'sum',
            'type' => 'number'
        ));

        $this->addColumn('ordered_qty', array(
            'header' => Mage::helper('reports')->__('SKU'),
            'width' => '100px',
            'align' => 'right',
            'index' => 'sales_total',
            'total' => 'sum',
            'type' => 'number'
        ));
        $this->addColumn('invoiced_qty', array(
            'header' => Mage::helper('reports')->__('Total Price'),
            'width' => '80px',
            'align' => 'right',
            'index' => 'invoiced',
            'total' => 'sum',
            'type' => 'number'
        ));
        $this->addColumn('refunded_qty', array(
            'header' => Mage::helper('reports')->__('Quantity Ordered'),
            'width' => '80px',
            'align' => 'right',
            'index' => 'refunded',
            'total' => 'sum',
            'type' => 'number'
        ));

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

}
