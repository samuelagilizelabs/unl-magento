<?php

class Unl_Core_Sales_OrderController extends Mage_Adminhtml_Controller_Action
{
    public function applyfilterAction()
    {
        $session = Mage::getSingleton('adminhtml/session');
        $sessionParamName = Mage::helper('unl_core')->getAdvancedGridFiltersStorageKey('order');
        if ($this->getRequest()->has('advfilter')) {
            $requestData = $this->getRequest()->getParam('advfilter');
            $requestData = Mage::helper('adminhtml')->prepareFilterString($requestData);
            $params = new Varien_Object();

            foreach ($requestData as $key => $value) {
                if (!empty($value)) {
                    $params->setData($key, $value);
                }
            }

            $session->setData($sessionParamName, $params);
        }
    }

    public function currentfiltersAction()
    {
        $this->loadLayout();

        $block = $this->getLayout()->createBlock('unl_core/adminhtml_sales_order_grid_filter_form');
        $block->toHtml();
        $filters = $block->getFilterData();
        $resp = new Varien_Object();
        foreach ($block->getForm()->getElement('base_fieldset')->getElements() as $element) {
            switch ($element->getType()) {
                case 'button':
                    break;
                case 'checkbox':
                    if ($filters->getIsRepeat()) {
                        $resp->setData($element->getHtmlId(), $element->getValue());
                    }
                    break;
                default:
                    if ($element->getValue()) {
                        $resp->setData($element->getHtmlId(), $element->getValue());
                    }
            }
        }

        if (!$resp->hasData()) {
            $resp = new stdClass();
        }

        $this->getResponse()->setBody(Zend_Json::encode($resp));
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/order');
    }
}