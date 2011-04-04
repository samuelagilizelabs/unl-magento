<?php

class Unl_Inventory_Block_Inventory_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('inventory_audit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('unl_inventory')->__('Product Inventory'));
    }

    protected function _beforeToHtml()
    {
        if (Mage::registry('current_product')->getAuditInventory()) {

            $this->addTab('adjustment', array(
                'label'     => Mage::helper('unl_inventory')->__('Purchase/Adjustment'),
                'content'   => $this->getLayout()->createBlock('unl_inventory/inventory_edit_tab_inventory')->initForm()->toHtml(),
            ));
        }

        $this->addTab('audit', array(
            'label'     => Mage::helper('unl_inventory')->__('Audit Trail'),
            'class'     => 'ajax',
            'url'       => $this->getUrl('*/*/auditGrid', array('_current' => true)),
        ));

        return parent::_beforeToHtml();
    }
}
