<?php

/**
 * Customer Widget Form File Element Block
 *
 * @category    Goodahead
 * @package     Goodahead_Etm
 * @author      Alexander Grigor <a.grigor@youwe.nl>
 */
class Goodahead_Etm_Block_Adminhtml_Form_Element_File extends Mage_Adminhtml_Block_Customer_Form_Element_File
{

    /**
     * @return Goodahead_Etm_Model_Entity_Type
     */
    public function getEntityType()
    {
        return Mage::registry('etm_entity_type');
    }

    /**
     * Return Preview/Download URL
     *
     * @return string
     */
    protected function _getPreviewUrl()
    {
        $url = '';
        if ($this->getValue()) {
            $url = sprintf('%sgoodahead/etm/%s/%s/%s',
                Mage::getBaseUrl('media'),
                $this->getEntityType()->getEntityTypeCode(),
                $this->getEntityAttribute()->getAttributeCode(),
                $this->getValue()
            );
        }
        return $url;
    }
}