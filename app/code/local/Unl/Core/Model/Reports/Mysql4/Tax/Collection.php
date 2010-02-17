<?php

class Unl_Core_Model_Reports_Mysql4_Tax_Collection extends Mage_Reports_Model_Mysql4_Tax_Collection
{
    public function setDateRange($from, $to)
    {
        $this->_reset();

        $this->addAttributeToFilter('created_at', array('from' => $from, 'to' => $to))
            ->addExpressionAttributeToSelect('orders', 'COUNT(DISTINCT({{entity_id}}))', array('entity_id'))
            ->getSelect()
            ->join(array('tax_table' => $this->getTable('sales/order_tax')), 'e.entity_id = tax_table.order_id')
            ->from('', array('code' => "CASE WHEN tax_table.code LIKE '%-CountyFips-%' OR tax_table.code LIKE '%-CityFips-%' THEN CONCAT('US-NE-', RIGHT(tax_table.code, 14)) ELSE tax_table.code END"))
            ->joinLeft(array('pf' => 'unl_tax_places'), "CASE WHEN tax_table.code LIKE '%-CityFips-%' THEN RIGHT(tax_table.code, 5) = pf.fips_place_number ELSE NULL END", array('city' => 'name'))
            ->joinLeft(array('cf' => 'unl_tax_counties'), "CASE WHEN tax_table.code LIKE '%-CountyFips-%' THEN RIGHT(tax_table.code, 3) = cf.county_id ELSE NULL END", array('county' => 'name'))
            ->group("CASE WHEN tax_table.code LIKE '%-CountyFips-%' OR tax_table.code LIKE '%-CityFips-%' THEN RIGHT(tax_table.code, 14) ELSE tax_table.code END")
            ->order(array('process', 'priority'));

        return $this;
    }
    
    public function setStoreIds($storeIds)
    {
        //ATTN: This method does not join source_store_view to avoid sub queries
        
        $vals = array_values($storeIds);
        if (count($storeIds) >= 1 && $vals[0] != '') {
            $this->getSelect()
                 ->where('e.store_id in (?)', (array)$storeIds)
                ->from('', array('tax'=>'SUM(tax_table.base_real_amount)'))
                ->from('', array('sales_amount'=>'SUM(tax_table.base_sale_amount)'));
        } else {
            $this->addExpressionAttributeToSelect(
                'tax',
                'SUM(tax_table.base_real_amount*{{base_to_global_rate}})',
                array('base_to_global_rate'));
            $this->addExpressionAttributeToSelect(
                'sales_amount',
                'SUM(tax_table.base_sale_amount*{{base_to_global_rate}})',
                array('base_to_global_rate'));
        }

        return $this;
    }
}