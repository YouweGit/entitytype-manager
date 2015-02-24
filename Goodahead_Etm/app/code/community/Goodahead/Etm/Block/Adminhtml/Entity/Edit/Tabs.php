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
        $this->addTab('general', 'goodahead_etm/adminhtml_entity_edit_tab_main');

        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }

        $currentType = Mage::registry('etm_entity_type');
        if (!is_object($currentType)) {
            throw new Exception('Entity type is not loaded');
        }
        $linkedTypes = $currentType->getLinkedTypes();
        if (!is_array($linkedTypes)) {
            return $this;
        }

        foreach ($linkedTypes as $type) {
            /** @var Goodahead_Etm_Model_Entity_Type $typeModel */
            $typeModel = Mage::getModel('eav/entity_type')->load($type);

            if (!$typeModel->getId()) {
                continue;
            }
            $this->addTab($typeModel->getEntityTypeCode(), array(
                'label'     => ucwords(str_replace('_', ' ', $typeModel->getEntityTypeCode())),
                'url'       => $this->getUrl('*/*/link', array('_current' => true, 'linked_type_id' => $type)),
                'class'     => 'ajax',
            ));
        }

        return parent::_prepareLayout();
    }
}