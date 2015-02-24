<?php
/**
 * Link Collection resource model
 *
 * @category Goodahead
 * @package  Goodahead_Etm
 * @author Sergey Gozhedrianov <s.gozhedrianov@youwe.nl>
 */
class Goodahead_Etm_Model_Resource_Link_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Define resource model
     *
     */
    protected function _construct()
    {
        $this->_init('goodahead_etm/link');
    }

    /**
     * Set entity filtering
     *
     * @param Goodahead_Etm_Model_Entity $entity
     * @return $this
     */
    public function setEntity(Goodahead_Etm_Model_Entity $entity)
    {
        $this->getSelect()
            ->where('main_table.entity_id = ?', $entity->getId());

        return $this;
    }

    /**
     * Set entity type filtering to collection
     *
     * @param int $type
     * @return $this
     */
    public function setLinkType($type)
    {
        if (is_numeric($type)) {
            $this->getSelect()
                ->where('main_table.entity_type_id = ?', $type);
        }
        return $this;
    }
}