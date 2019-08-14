<?php

class Vtrio_CustomReports_Block_Report_Custom extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    /**
     * Initialize container block settings
     *
     */
    public function __construct()
    {
        $this->_blockGroup = 'customreports';  // This is your module’s name.
        $this->_controller = 'report_custom';  //This is not the controller class name. It is actually your Block class name.
        $this->_headerText = Mage::helper('customreports')->__('Custom Report');
        parent::__construct();
        $this->_removeButton('add');
        $this->addButton('filter_form_submit', array(
            'label'     => Mage::helper('reports')->__('Show Report'),
            'onclick'   => 'filterFormSubmit()'
        ));
    }   

}
