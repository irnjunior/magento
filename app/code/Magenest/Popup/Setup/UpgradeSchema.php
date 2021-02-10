<?php

namespace Magenest\Popup\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements \Magento\Framework\Setup\UpgradeSchemaInterface
{

    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {

        $setup->startSetup();
        if (version_compare($context->getVersion(), '1.1.0') < 0) {
            // Get module table
            $magenest_popup = $setup->getTable('magenest_popup');
            if ($setup->getConnection()->isTableExists($magenest_popup) == true) {
                $columns = [
                    'popup_positioninpage' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        'nullable' => true,
                        'comment' => 'Popup Position in page',
                    ],
                    'popup_link' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => 'Add Link'
                    ],
                    'enable_floating_button' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        'nullable' => true,
                        'comment' => 'Enable Floating Button',
                    ],
                    'floating_button_content' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => 'Floating Button Content'
                    ],
                    'floating_button_position' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        'nullable' => true,
                        'comment' => 'Floating Button Position',
                    ],
                    'floating_button_text_color' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => 'Floating Button Text Color'
                    ],
                    'floating_button_background_color' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => 'Floating Button Background Color'
                    ],
                    'floating_button_display_popup' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        'nullable' => true,
                        'comment' => 'Floating Button Display Popup',
                    ],
                    'enable_mailchimp' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        'nullable' => true,
                        'comment' => 'Enable Mail'
                    ],
                    'api_key' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => false,
                        'comment' => 'Api Key',
                    ],
                    'audience_id' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => false,
                        'comment' => 'Audience Id',
                    ],
                    'widget_instance' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => false,
                        'comment' => 'Widget Instance',
                    ]
                ];

                $connection = $setup->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($magenest_popup, $name, $definition);
                }
            }

            $talbe = $setup->getConnection()->newTable(
                $setup->getTable('magenest_popup_layout')
            )->addColumn(
                'popup_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [],
                'popup id'
            )->addColumn(
                'layout_update_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [],
                'Layout update id '
            );

            $setup->getConnection()->createTable($talbe);

            $magenest_log = $setup->getTable('magenest_log');
            if ($setup->getConnection()->isTableExists($magenest_log) == true) {
                $columns = [
                    'popup_name' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => false,
                        'comment' => 'Popup Name',
                    ]
                ];
                $connection = $setup->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($magenest_log, $name, $definition);
                }
            }
            $magenest_templates = $setup->getTable('magenest_popup_templates');
            if ($setup->getConnection()->isTableExists($magenest_templates) == true) {
                $columns = [
                    'status' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        'nullable' => false,
                        'comment' => 'Status',
                    ]
                ];
                $connection = $setup->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($magenest_templates, $name, $definition);
                }
            }
            $setup->endSetup();
        }
    }
}
