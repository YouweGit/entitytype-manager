<?php
/**
 * Link Resource model
 *
 * @category Goodahead
 * @package  Goodahead_Etm
 * @author Sergey Gozhedrianov <s.gozhedrianov@youwe.nl>
 */
class Goodahead_Etm_Model_Resource_Link extends Mage_Core_Model_Resource_Db_Abstract
{

    /**
     * Init table information
     */
    protected function _construct()
    {
        $this->_init('goodahead_etm/entity_link', 'link_id');
    }

    /**
     * Get linked entities by type id
     *
     * @param Goodahead_Etm_Model_Entity $entity
     * @param int $typeId
     * @return array
     */
    public function getLinkedEntitiesByType($entity, $typeId)
    {
        /** @var Varien_Db_Select $select */
        $select = $entity->getCollection()->getSelect();
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->joinInner(
            array('link' => $this->getMainTable()),
            'e.entity_type_id = link.entity_type_id AND e.entity_id = link.entity_id',
            array('linked' => 'linked_entity_id', 'sort_order')
        )->where('link.linked_entity_type_id = ?', $typeId)
            ->where('link.entity_id = ?', $entity->getId());

        $result = $this->getReadConnection()->fetchAll($select);

        return $result;
    }

    /**
     * Save linking data
     *
     * @param Goodahead_Etm_Model_Entity_Type $entity
     * @param array $linkData
     */
    public function saveEntityLinks($entity, $linkData)
    {

        foreach ($linkData as $linkTypeId => $linkDetails) {
            $select = $this->_getWriteAdapter()->select()
                ->from(
                    array('main' => $this->getTable('youwe_etm/entity_link')),
                    '')->where('main.entity_type_id = ?', $entity->getEntityTypeId())
                ->where('main.entity_id = ?', $entity->getId())
                ->where('main.linked_entity_type_id = ?', $linkTypeId);

            $query = $this->_getWriteAdapter()->deleteFromSelect($select, 'main');

            $this->_getWriteAdapter()->query($query);

            $select = $this->_getWriteAdapter()->select()
                ->from(
                    array('main' => $this->getTable('youwe_etm/entity_link')),
                    '')->where('main.entity_type_id = ?', $linkTypeId)
                ->where('main.linked_entity_id = ?', $entity->getId())
                ->where('main.linked_entity_type_id = ?', $entity->getEntityTypeId());

            $query = $this->_getWriteAdapter()->deleteFromSelect($select, 'main');

            $this->_getWriteAdapter()->query($query);

            if (empty($linkDetails)) {
                continue;
            }

            $this->_getWriteAdapter()->insertMultiple($this->getTable('youwe_etm/entity_link'), $linkDetails);
        }
    }
}