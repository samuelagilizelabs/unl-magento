<?php

class Unl_Core_Block_Catalog_Navigation extends Mage_Catalog_Block_Navigation
{
    /* Overrides
     * @see Mage_Catalog_Block_Navigation::renderCategoriesMenuHtml()
     * by adding ACL checks
     */
    public function renderCategoriesMenuHtml($level = 0, $outermostItemClass = '', $childrenWrapClass = '')
    {
        $activeCategories = array();
        foreach ($this->getStoreCategories() as $child) {
            if ($child->getIsActive() && Mage::helper('unl_core')->isCustomerAllowedCategory($child)) {
                $activeCategories[] = $child;
            }
        }
        $activeCategoriesCount = count($activeCategories);
        $hasActiveCategoriesCount = ($activeCategoriesCount > 0);

        if (!$hasActiveCategoriesCount) {
            return '';
        }

        $html = '';
        $j = 0;
        foreach ($activeCategories as $category) {
            $html .= $this->_renderCategoryMenuItemHtml(
                $category,
                $level,
                ($j == $activeCategoriesCount - 1),
                ($j == 0),
                true,
                $outermostItemClass,
                $childrenWrapClass,
                true
            );
            $j++;
        }

        return $html;
    }
}