<?php
/**
 * This file is part of Goodahead_Etm extension
 *
 * This extension allows to create and manage custom EAV entity types
 * and EAV entities
 *
 * Copyright (C) 2014 Goodahead Ltd. (http://www.goodahead.com)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * and GNU General Public License along with this program.
 * If not, see <http://www.gnu.org/licenses/>.
 *
 * @category   Goodahead
 * @package    Goodahead_Etm
 * @copyright  Copyright (c) 2014 Goodahead Ltd. (http://www.goodahead.com)
 * @license    http://www.gnu.org/licenses/lgpl-3.0-standalone.html GNU Lesser General Public License
 */

/** @var $installer Goodahead_Etm_Model_Resource_Entity_Setup */
$installer = $this;
$installer->startSetup();

/** @var Varien_Db_Adapter_Pdo_Mysql $connection */
$connection = $installer->getConnection();
if (!$connection->tableColumnExists($installer->getTable('goodahead_etm/eav_entity_type'), 'linked_types') ) {
    $installer->getConnection()->addColumn($installer->getTable('goodahead_etm/eav_entity_type'), 'linked_types', 'VARCHAR(255) DEFAULT NULL');
}

if ($connection->isTableExists($installer->getTable('goodahead_etm/entity_link'))) {
    $connection->dropTable($installer->getTable('goodahead_etm/entity_link'));
}

$table = $installer->getConnection()->newTable($installer->getTable('goodahead_etm/entity_link'));
$table
    ->addColumn('link_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'auto_increment' => true
    ), 'Link ID')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
    ), 'Entity ID')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
    ), 'Linked Entity ID')
    ->addColumn('linked_entity_type_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
    ), 'Entity ID')
    ->addColumn('linked_entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
    ), 'Linked Entity ID')
    ->addColumn('sort_order', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => true,
        'default'   => 0
    ),'Sort order')
    ->addIndex(
        $installer->getIdxName($installer->getTable('goodahead_etm/entity_link'), array('entity_id', 'entity_type_id')),
        array('entity_id', 'entity_type_id')
    )
    ->addIndex(
        $installer->getIdxName($installer->getTable('goodahead_etm/entity_link'), array('linked_entity_id', 'linked_entity_type_id')),
        array('linked_entity_id', 'linked_entity_type_id')
    )
    ->addForeignKey(
        $installer->getFkName('goodahead_etm/entity_link', 'entity_type_id', 'eav/entity_type', 'entity_type_id'),
        'entity_type_id',
        $installer->getTable('eav/entity_type'),
        'entity_type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName('goodahead_etm/entity_link', 'linked_entity_type_id', 'eav/entity_type', 'entity_type_id'),
        'linked_entity_type_id',
        $installer->getTable('eav/entity_type'),
        'entity_type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
    );

$installer->createTable($table);

$installer->endSetup();
