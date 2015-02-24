<?php
/**
 * Entity Link base model
 *
 * @category Goodahead
 * @package  Goodahead_Etm
 * @author Sergey Gozhedrianov <s.gozhedrianov@youwe.nl>
 */
class Goodahead_Etm_Model_Link extends Mage_Core_Model_Abstract
{

    /**
     * Init resource connection
     */
    protected function _construct()
    {
        $this->_init('goodahead_etm/link');
    }

    /**
     * Get linked entities
     *
     * @param Goodahead_Etm_Model_Entity $entity
     * @param int $typeId
     * @return array
     */
    public function getLinkedEntitiesByType($entity, $typeId)
    {
        return $this->getResource()->getLinkedEntitiesByType($entity, $typeId);
    }
}