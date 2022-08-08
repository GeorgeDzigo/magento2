<?php

namespace Pixelpro\Helloworld\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements \Magento\Framework\Setup\UpgradeSchemaInterface
{
    protected $tableName = 'pixelpro_helloworld_post';
    /**
     * @inheritDoc
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if(version_compare($context->getVersion(), '1.1.0', '<')) {
            if(!$setup->tableExists($this->tableName)) {
                $table = $setup->getConnection()->newTable(
                    $setup->getTable($this->tableName)
                )
                    ->addColumn(
                        'post_id',
                        Table::TYPE_INTEGER,
                        null,
                        [
                            'identity' => true,
                            'nullable' => false,
                            'primary'  => true,
                            'unsigned' => true,
                        ],
                        'Post ID'
                    )
                    ->addColumn(
                        'title',
                        Table::TYPE_TEXT,
                        255,
                        ['nullable => false'],
                        'Title'
                    )
                    ->addColumn(
                        'content',
                        Table::TYPE_TEXT,
                        '64k',
                        [],
                        'Content'
                    )->setComment('Post Table');
                
                $setup->getConnection()->createTable($table);
                $setup->getConnection()->addIndex(
                    $setup->getTable($this->tableName),
                    $setup->getIdxName(
                        $setup->getTable($this->tableName),
                        ['title','content'],
                        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
                    ),
                    ['title','content'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
                );
            }
        }
        $setup->endSetup();
    }
}