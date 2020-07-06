<?php
/**
 * A Magento 2 module named Xigen/Menu
 * Copyright (C) 2019 
 * 
 * This file included in Xigen/Menu is licensed under OSL 3.0
 * 
 * http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * Please see LICENSE.txt for the full text of the OSL 3.0 license
 */

namespace Xigen\Menu\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{

    /**
     * {@inheritdoc}
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $table_xigen_menu_item = $setup->getConnection()->newTable($setup->getTable('xigen_menu_item'));

        $table_xigen_menu_item->addColumn(
            'item_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true,'nullable' => false,'primary' => true,'unsigned' => true],
            'Entity ID'
        );

        $table_xigen_menu_item->addColumn(
            'menu_id',
            Table::TYPE_INTEGER,
            null,
            [],
            'Menu ID'
        );

        $table_xigen_menu_item->addColumn(
            'parent_id',
            Table::TYPE_INTEGER,
            null,
            [],
            'Parent ID'
        );

        $table_xigen_menu_item->addColumn(
            'title',
            Table::TYPE_TEXT,
            255,
            [],
            'Title'
        );

        $table_xigen_menu_item->addColumn(
            'identifier',
            Table::TYPE_TEXT,
            255,
            [],
            'Identifier'
        );

        $table_xigen_menu_item->addColumn(
            'url',
            Table::TYPE_TEXT,
            255,
            [],
            'URL'
        );

        $table_xigen_menu_item->addColumn(
            'open_type',
            Table::TYPE_INTEGER,
            null,
            [],
            'Open Type'
        );

        $table_xigen_menu_item->addColumn(
            'url_type',
            Table::TYPE_TEXT,
            255,
            [],
            'URL Type'
        );

        $table_xigen_menu_item->addColumn(
            'cms_page_identifier',
            Table::TYPE_TEXT,
            255,
            [],
            'CMS Page Identifier'
        );

        $table_xigen_menu_item->addColumn(
            'category_id',
            Table::TYPE_INTEGER,
            null,
            [],
            'Category ID'
        );

        $table_xigen_menu_item->addColumn(
            'position',
            Table::TYPE_INTEGER,
            null,
            [],
            'Position'
        );

        $table_xigen_menu_item->addColumn(
            'is_active',
            Table::TYPE_INTEGER,
            null,
            [],
            'Is Active'
        );

        $table_xigen_menu_menu = $setup->getConnection()->newTable($setup->getTable('xigen_menu_menu'));

        $table_xigen_menu_menu->addColumn(
            'menu_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true,'nullable' => false,'primary' => true,'unsigned' => true],
            'Entity ID'
        );

        $table_xigen_menu_menu->addColumn(
            'identifier',
            Table::TYPE_TEXT,
            255,
            [],
            'Identifier'
        );

        $table_xigen_menu_menu->addColumn(
            'title',
            Table::TYPE_TEXT,
            255,
            [],
            'Title'
        );

        $table_xigen_menu_menu->addColumn(
            'css_class',
            Table::TYPE_TEXT,
            255,
            [],
            'CSS Class'
        );

        $table_xigen_menu_menu->addColumn(
            'is_active',
            Table::TYPE_INTEGER,
            null,
            [],
            'Is Active'
        );

        $table_xigen_menu_store = $setup->getConnection()->newTable($setup->getTable('xigen_menu_store'));

        $table_xigen_menu_store->addColumn(
            'menu_id',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Menu ID'
        );

        $table_xigen_menu_store->addColumn(
            'store_id',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Store ID'
        );
        
        $setup->getConnection()->createTable($table_xigen_menu_menu);

        $setup->getConnection()->createTable($table_xigen_menu_item);
        
        $setup->getConnection()->createTable($table_xigen_menu_store);
    }
}
