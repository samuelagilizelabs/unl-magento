<?php

class Unl_BundlePlus_Model_Sales_Order_Pdf_Items_Shipment
    extends Unl_BundlePlus_Model_Sales_Order_Pdf_Items_Abstract
{
    const DEFAULT_OFFSET_QTY      = 35;
    const DEFAULT_OFFSET_PRODUCT  = 65;
    const DEFAULT_OFFSET_SKU      = 440;

    const DEFAULT_OFFSET_PAD      = 10;

    const DEFAULT_TRIM_PRODUCT    = 60;
    const DEFAULT_TRIM_SKU        = 25;

    const FONT_SIZE_GIFTMESSAGE   = 8;
    const LINE_HEIGHT_GIFTMESSAGE = 10;

    /**
     * Draw item line
     *
     */
    public function draw()
    {
        $item   = $this->getItem();
        $pdf    = $this->getPdf();
        $page   = $this->getPage();

        $this->_setFontRegular();

        $shipItems = $this->getChilds($item);
        $items = $this->getChilds($item->getOrderItem());

        $_prevOptionId = '';
        $drawItems = array();

        foreach ($items as $_item) {
            $line   = array();

            $attributes = $this->getSelectionAttributes($_item);
            if (is_array($attributes)) {
                $optionId   = $attributes['option_id'];
            }
            else {
                $optionId = 0;
            }

            if (!isset($drawItems[$optionId])) {
                $drawItems[$optionId] = array(
                    'lines'  => array(),
                    'height' => self::DEFAULT_LINE_HEIGHT
                );
            }

            if ($_item->getParentItem()) {
                if ($_prevOptionId != $attributes['option_id']) {
                    $line[0] = array(
                        'font'  => 'italic',
                        'text'  => Mage::helper('core/string')->str_split($attributes['option_label'], self::DEFAULT_TRIM_PRODUCT, true, true),
                        'feed'  => self::DEFAULT_OFFSET_PRODUCT
                    );

                    $drawItems[$optionId] = array(
                        'lines'  => array($line),
                        'height' => self::DEFAULT_LINE_HEIGHT
                    );

                    $line = array();

                    $_prevOptionId = $attributes['option_id'];
                }
            }

            if (($this->isShipmentSeparately() && $_item->getParentItem()) || (!$this->isShipmentSeparately() && !$_item->getParentItem())) {
                if (isset($shipItems[$_item->getId()])) {
                    $qty = $shipItems[$_item->getId()]->getQty()*1;
                } else if ($_item->getIsVirtual()) {
                    $qty = Mage::helper('bundle')->__('N/A');
                } else {
                    $qty = 0;
                }
            } else {
                $qty = '';
            }

            $line[] = array(
                'text'  => $qty,
                'feed'  => self::DEFAULT_OFFSET_QTY
            );

            // draw Name
            if ($_item->getParentItem()) {
                $feed = self::DEFAULT_OFFSET_PRODUCT + self::DEFAULT_OFFSET_PAD;
                $name = $this->getValueHtml($_item);
            } else {
                $feed = self::DEFAULT_OFFSET_PRODUCT;
                $name = $_item->getName();
            }
            $text = array();
            foreach (Mage::helper('core/string')->str_split($name, self::DEFAULT_TRIM_PRODUCT, true, true) as $part) {
                $text[] = $part;
            }
            $line[] = array(
                'text'  => $text,
                'feed'  => $feed
            );

            // draw SKUs
            $text = array();
            foreach (Mage::helper('core/string')->str_split($_item->getSku(), self::DEFAULT_TRIM_SKU) as $part) {
                $text[] = $part;
            }
            $line[] = array(
                'text'  => $text,
                'feed'  => self::DEFAULT_OFFSET_SKU
            );

            $drawItems[$optionId]['lines'][] = $line;
        }

        $page = $pdf->drawLineBlocks($page, $drawItems, array('table_header' => true));
        $this->setPage($page);

        $this->drawOptions();
        $this->drawGiftMessage();
    }
}
