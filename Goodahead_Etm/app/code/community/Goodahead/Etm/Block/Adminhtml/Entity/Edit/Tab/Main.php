<?php
/**
 * @author Sergey Gozhedrianov <s.gozhedrianov@youwe.nl>
 */
class Goodahead_Etm_Block_Adminhtml_Entity_Edit_Tab_Main
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        Varien_Data_Form::setFieldsetElementRenderer(
            $this->getLayout()->createBlock('goodahead_etm/adminhtml_form_renderer_fieldset_element')
        );
        return $this;
    }

    public function getEntity()
    {
        return Mage::registry('etm_entity');
    }

    public function getEntityType()
    {
        return Mage::registry('etm_entity_type');
    }

    protected function _initDefaultValues()
    {
        if (!$this->getEntity()->getId()) {
            foreach (
                Mage::helper('goodahead_etm')
                    ->getVisibleAttributes($this->getEntityType()) as $attribute
            ) {
                $default = $attribute->getDefaultValue();
                if ($default != '') {
                    $this->getEntity()->setData($attribute->getAttributeCode(), $default);
                }
            }
        }
        return $this;
    }

    /**
     * Preparing form elements for editing Entity
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $this->_initDefaultValues();
        $entityType = $this->getEntityType();
        $entity = $this->getEntity();

        $form->setDataObject($entity);

        $fieldSet = $form->addFieldset('entity_data', array(
            'legend' => Mage::helper('goodahead_etm')->__("Entity Attributes")
        ));

        $attributes = Mage::helper('goodahead_etm')->getVisibleAttributes($entityType);
        foreach ($attributes as $attribute) {
            /* @var $attribute Mage_Eav_Model_Entity_Attribute */
            $attribute->unsIsVisible();
            if ($attribute->isSystem()) {
                $attribute->setIsVisible(0);
            }
        }

        $this->_setFieldset($attributes, $fieldSet);

        if ($entity->getId()) {
            $form->addField('entity_id', 'hidden', array(
                'name' => 'entity_id',
            ));
        }
        $form->setValues($entity->getData());
        if ($entityType->getId()) {
            $form->addField('entity_type_id', 'hidden', array(
                'name'  => 'entity_type_id',
                'value' => $entityType->getId(),
            ));
        }
        $form->addField('store_id', 'hidden', array(
            'name'  => 'store_id',
            'value' => $this->getRequest()->getParam('store'),
        ));

        $this->setForm($form);

        return parent::_prepareForm();
    }

    // TODO: add media images and media gallery support
    protected function _getAdditionalElementTypes()
    {
        return array(
            'file'      => Mage::getConfig()->getBlockClassName('adminhtml/customer_form_element_file'),
//            'image'     => Mage::getConfig()->getBlockClassName('adminhtml/customer_form_element_image'),
            'image'     => Mage::getConfig()->getBlockClassName('goodahead_etm/adminhtml_form_renderer_fieldset_element_image'),
            'boolean'   => Mage::getConfig()->getBlockClassName('adminhtml/customer_form_element_boolean'),
            'price'     => Mage::getConfig()->getBlockClassName('goodahead_etm/adminhtml_form_renderer_fieldset_element_price'),
        );
    }

    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return 'General';
    }

    /**
     * Return Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }


    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }
}