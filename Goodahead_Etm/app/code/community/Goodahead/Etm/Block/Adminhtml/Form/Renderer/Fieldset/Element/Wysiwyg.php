<?php
/**
 * Magento admin frontend renderer
 *
 * @category Goodahead
 * @package  Goodahead_Etm
 * @author Sergey Gozhedrianov <s.gozhedrianov@youwe.nl>
 */

class Goodahead_Etm_Block_Adminhtml_Form_Renderer_Fieldset_Element_Wysiwyg
    extends Varien_Data_Form_Element_Editor
{
    /**
     * Check whether Wysiwyg is enabled or not
     *
     * @return bool
     */
    public function isEnabled()
    {
        if (Mage::helper('catalog')->isModuleEnabled('Mage_Cms')) {
            return (Mage::getSingleton('cms/wysiwyg_config')->isEnabled());
        }

        return false;
    }

    /**
     * Class construct
     *
     * @param array $attributes
     */
    public function __construct($attributes = array())
    {
        parent::__construct($attributes);

        $this->setConfig(Mage::getSingleton('cms/wysiwyg_config')->getConfig());

    }

    /**
     * Check whether Wysiwyg is loaded on demand or not
     *
     * @return bool
     */
    public function isHidden()
    {
        return (bool)$this->getConfig('hidden');
    }
}
