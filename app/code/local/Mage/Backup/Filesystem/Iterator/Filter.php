<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Backup
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Filter Iterator
 *
 * @category    Mage
 * @package     Mage_Backup
 * @author      Magento Core Team <core@magentocommerce.com>
 *
 * @method RecursiveDirectoryIterator getInnerIterator()
 * @method SplFileInfo current()
 */
class Mage_Backup_Filesystem_Iterator_Filter extends RecursiveFilterIterator
{
    /**
     * Array that is used for filtering
     *
     * @var array
     */
    protected $_filters;

    /**
     * Constructor
     *
     * @param RecursiveDirectoryIterator $iterator
     * @param array $filters list of files to skip
     */
    public function __construct(RecursiveDirectoryIterator $iterator, array $filters)
    {
        parent::__construct($iterator);
        $this->_filters = $filters;
    }

    /**
     * Check whether the current element of the iterator is acceptable
     *
     * @return bool
     */
    public function accept()
    {
        $currentFilename = $this->current()->getFilename();

        if ($currentFilename == '.' || $currentFilename == '..') {
            return false;
        }

        foreach ($this->_filters as $filter) {
            if (false !== strpos($this->current(), $filter)) {
                return false;
            }
        }

        return true;
    }

    public function getChildren()
    {
        return new self($this->getInnerIterator()->getChildren(), $this->_filters);
    }
}
