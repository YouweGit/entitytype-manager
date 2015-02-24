<?php
/**
 * @category Goodahead
 * @package  Goodahead_Etm
 *
 * @author Sergey Gozhedrianov <s.gozhedrianov@youwe.nl>
 */
class Goodahead_Etm_Block_Adminhtml_Entity_Edit_Tab_Link
    extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Set grid params
     */
    public function __construct()
    {
        parent::__construct();
        $typeId = (int)$this->getRequest()->getParam('linked_type_id');
        $this->setId('link_entities_grid_' . $typeId);
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
        if ($this->_getEntity()->getId()) {
            $this->setDefaultFilter(array('linked' => 1));
        }
    }

    /**
     * Get children of specified item
     * Rewritten to omit any output of children items
     *
     * @param Varien_Object $item
     * @return array
     */
    public function getMultipleRows($item)
    {
        return array();
    }

    /**
     * Add filter
     *
     * @param Varien_Object $column
     * @return Goodahead_Etm_Block_Adminhtml_Entity_Edit_Tab_Link
     */
    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag
        if ($column->getId() == 'linked') {
            $entityIds = $this->_getLinkedEntities();
            if (empty($entityIds)) {
                $entityIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->getSelect()->where('e.entity_id in (?)', $entityIds);
            } else {
                if($entityIds) {
                    $this->getCollection()->getSelect()->where('e.entity_id in (?)', $entityIds);
                }
            }

        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * Get entity from session
     *
     * @throws Goodahead_Etm_Exception
     * @return Goodahead_Etm_Model_Entity
     */
    protected function _getEntity()
    {
        $entity = Mage::registry('etm_entity');
        if ($entity) {
            return $entity;
        }

        throw new Goodahead_Etm_Exception(Mage::helper('goodahead_etm')->__('Entity not found'));
    }

    /**
     * Prepare collection
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $linkedTypeId = (int)$this->getRequest()->getParam('linked_type_id');

        $currentEntity = $this->_getEntity();

        $typeModel = Mage::getModel('eav/entity_type')->load($linkedTypeId);
        if (!$typeModel->getId()) {
            return $this;
        }

        if (!$currentEntity->getId()) {
            //new entity push type data
            $currentEntity->setEntityTypeId($typeModel->getId());
        }
        $collection = Mage::getModel($typeModel->getEntityModel())->getCollection()->addAttributeToSelect('name');


        Mage::getResourceModel('goodahead_etm/link')->addLinkDataToCollection($currentEntity, $collection);

        if ($typeModel->getEntityTypeCode() == Mage_Catalog_Model_Category::ENTITY) {
            Mage::helper('goodahead_etm')->applyCategoryFilters($collection);
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Add columns to grid
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('linked', array(
            'header_css_class'  => 'a-center',
            'type'              => 'checkbox',
            'name'              => 'linked',
            'values'            => $this->_getLinkedEntities(),
            'align'             => 'center',
            'index'             => 'entity_id'
        ));


        $this->addColumn('linked_entity', array(
            'header'    => Mage::helper('goodahead_etm')->__('ID'),
            'sortable'  => true,
            'width'     => 60,
            'index'     => 'entity_id',
            'name'      => 'linked_entity'
        ));

        $this->addColumn('linked_name', array(
            'header'    => Mage::helper('goodahead_etm')->__('Name'),
            'index'     => 'name',
            'name'      =>  'linked_name'
        ));

        $this->addColumn('sort_order', array(
            'header'            => Mage::helper('goodahead_etm')->__('Position'),
            'name'              => 'sort_order',
            'type'              => 'number',
            'validate_class'    => 'validate-number',
            'index'             => 'sort_order',
            'width'             => 60,
            'editable'          => true
        ));

        return parent::_prepareColumns();
    }


    /**
     * Rerieve grid URL
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getData('grid_url')
            ? $this->getData('grid_url')
            : $this->getUrl('*/*/linkGrid', array('_current' => true));
    }

    /**
     * Retrieve selected related products
     *
     * @return array
     */
    protected function _getLinkedEntities()
    {
        $entities = $this->getLinkedEntities();
        if (!is_array($entities)) {
            $entities = array_keys($this->getSelectedEntities());
        }

        return $entities;
    }

    /**
     * Retrieve related products
     *
     * @return array
     */
    public function getSelectedEntities()
    {
        $entities = array();
        $typeId = (int)$this->getRequest()->getParam('linked_type_id');
        foreach (Mage::getModel('goodahead_etm/link')->getLinkedEntitiesByType($this->_getEntity(), $typeId) as $entity) {
            $entities[$entity['linked']] = array('sort_order' => $entity['sort_order']);
        }

        return $entities;
    }
}