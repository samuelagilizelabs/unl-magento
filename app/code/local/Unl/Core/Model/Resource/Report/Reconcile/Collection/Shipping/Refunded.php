<?php

class Unl_Core_Model_Resource_Report_Reconcile_Collection_Shipping_Refunded
    extends Unl_Core_Model_Resource_Report_Bursar_Collection_Shipping_Refunded
{
    protected function _getSelectedColumns()
    {
        parent::_getSelectedColumns();
        $this->_selectedColumns += Mage::helper('unl_core/report_bursar')->getAdditionalReconcileColumns($this);

        return $this->_selectedColumns;
    }

    protected  function _initSelect()
    {
        return $this->_initSelectForShipping(true, true);
    }
}
