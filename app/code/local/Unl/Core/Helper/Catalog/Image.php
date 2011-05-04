<?php

class Unl_Core_Helper_Catalog_Image extends Mage_Catalog_Helper_Image
{
    protected $_secureUrl = null;

    protected function _reset()
    {
        parent::_reset();
        $this->_secureUrl = null;
        return $this;
    }

    public function useSecureUrl($flag)
    {
        $this->_secureUrl = is_null($flag) ? $flag : (bool)$flag;
        return $this;
    }

    public function __toString()
    {
        try {
            if( $this->getImageFile() ) {
                $this->_getModel()->setBaseFile( $this->getImageFile() );
            } else {
                $this->_getModel()->setBaseFile( $this->getProduct()->getData($this->_getModel()->getDestinationSubdir()) );
            }

            if( $this->_getModel()->isCached() ) {
                return $this->_getModel()->getUrl($this->_secureUrl);
            } else {
                if( $this->_scheduleRotate ) {
                    $this->_getModel()->rotate( $this->getAngle() );
                }

                if ($this->_scheduleResize) {
                    $this->_getModel()->resize();
                }

                if( $this->getWatermark() ) {
                    $this->_getModel()->setWatermark($this->getWatermark());
                }

                $url = $this->_getModel()->saveFile()->getUrl($this->_secureUrl);
            }
        } catch( Exception $e ) {
            $url = Mage::getDesign()->getSkinUrl($this->getPlaceholder());
        }
        return $url;
    }
}
