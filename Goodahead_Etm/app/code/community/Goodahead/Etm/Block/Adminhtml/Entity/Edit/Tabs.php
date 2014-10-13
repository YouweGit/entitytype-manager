<?php
/**
 * @author Sergey Gozhedrianov <s.gozhedrianov@youwe.nl>
 */
class Goodahead_Etm_Block_Adminhtml_Entity_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('entity_edit');
        $this->setDestElementId('edit_form');
        $this->setTitle($this->getHeaderText());
    }

    /**
     * Retrieve text for header element depending on loaded page
     *
     * @return string
     */
    public function getHeaderText()
    {
        /** @var Goodahead_Etm_Model_Entity $entity */
        $entity = Mage::registry('etm_entity_type');
        return $this->__('%1$s Information',
            $this->escapeHtml($entity->getEntityTypeName())
        );
    }

    protected function _prepareLayout()
    {
        $entity = Mage::registry('etm_entity');
        $this->addTab('general', 'goodahead_etm/adminhtml_entity_edit_tab_main');

        Mage::dispatchEvent('goodahead_etm_adminhtml_entity_edit_tabs', array('tabs' => $this, 'entity' => $entity));

        return parent::_prepareLayout();
    }
}