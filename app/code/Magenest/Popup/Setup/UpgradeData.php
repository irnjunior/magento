<?php

namespace Magenest\Popup\Setup;

use Magenest\Popup\Helper\Data;
use Magenest\Popup\Model\LogFactory;
use Magenest\Popup\Model\PopupFactory;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeData implements UpgradeDataInterface
{
    protected $_helperData;
    protected $_popupTemplateFactory;
    protected $_popupTemplateCollection;
    private $_logModel;
    private $_popupModel;

    /**
     * UpgradeData constructor.
     * @param Data $helperData
     * @param \Magenest\Popup\Model\TemplateFactory $popupTemplateFactory
     * @param \Magenest\Popup\Model\ResourceModel\Template\CollectionFactory $popupTemplateCollection
     * @param LogFactory $logModel
     * @param PopupFactory $popupModel
     * @param Data $helperData
     */
    public function __construct(
        \Magenest\Popup\Helper\Data $helperData,
        \Magenest\Popup\Model\TemplateFactory $popupTemplateFactory,
        \Magenest\Popup\Model\ResourceModel\Template\CollectionFactory $popupTemplateCollection,
        LogFactory $logModel,
        PopupFactory $popupModel
    )
    {
        $this->_helperData = $helperData;
        $this->_popupTemplateFactory = $popupTemplateFactory;
        $this->_popupTemplateCollection = $popupTemplateCollection;
        $this->_logModel = $logModel;
        $this->_popupModel = $popupModel;
    }


    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.1.0') < 0) {
            $popup_type = [
                [
                    'path' => 'hot_deal/popup_1',
                    'type' => '6',
                    'class' => 'popup-default-40',
                ],
                [
                    'path' => 'hot_deal/popup_2',
                    'type' => '6',
                    'class' => 'popup-default-41',
                ]
            ];
            $data = [];

            $count = 0;
            $templateCollection = $this->_popupTemplateCollection->create()->getData();
            foreach ($templateCollection as $template) {
                $count++;
            }

            if (!empty($popup_type)) {
                foreach ($popup_type as $type) {
                    $data[] = [
                        'template_name' => "Default Template " . $count,
                        'template_type' => $type['type'],
                        'html_content' => $this->_helperData->getTemplateDefault($type['path']),
                        'css_style' => '',
                        'class' => $type['class'],
                        'status' => 1
                    ];
                    $count++;
                }
                $popupTemplateModel = $this->_popupTemplateFactory->create();
                $popupTemplateModel->insertMultiple($data);
            }
            $logModel = $this->_logModel->create()->getCollection()->getData();
            foreach ($logModel as $log) {
                $string = $log['content'];
                if ($this->isJSON($string)) {
                    $content = json_decode($string, true);
                    if (is_array($content)) {
                        $result = '';
                        $count = 0;
                        foreach ($content as $raw) {
                            if ($count == 0) {
                                $count++;
                                continue;
                            }
                            if (isset($raw['name'])) {
                                $result .= $raw['name'] . ": " . $raw['value'] . "| ";
                            }
                        }
                        $string = $result != '' ? $result : $content;
                    }
                }
                $log_id = $log['log_id'];
                $popup_id = $log['popup_id'];
                $popup_name = $this->_popupModel->create()->load($popup_id)->getPopupName();
                $created_at = $log['created_at'];
                $log = $this->_logModel->create()->load($log_id);
                $log->setPopupId($popup_id);
                $log->setPopupName($popup_name);
                $log->setContent($string);
                $log->setCreatedAt($created_at);
                $log->save();

            }
            $popup_type_default = $this->_helperData->getPopupTemplateDefault();

            // set status for template default
            $templateCollection = $this->_popupTemplateFactory->create()->getCollection()->getData();
            foreach ($templateCollection as $template) {
                if ($template['status'] == 0) {
                    foreach ($popup_type_default as $type) {
                        if ($type['class'] === $template['class']
                            && $type['name'] === $template['template_name']
                            && $type['type'] === $template['template_type']
                            && $this->_helperData->getTemplateDefault($type['path']) === $template['html_content']
                            && $template['css_style'] === '') {
                            $this->_popupTemplateFactory->create()->load($template['template_id'])
                                ->setStatus(1)->save();
                            break;
                        }
                    }
                }
            }

            // set status for tempalate_edited
            $templateEdited = $this->_popupTemplateFactory->create()->getCollection()
                ->addFieldToFilter('status', array('nin' => array(1)))->getData();
            foreach ($templateEdited as $template) {
                $this->_popupTemplateFactory->create()->load($template['template_id'])
                    ->setStatus(2)->save();
            }

            // set status for tempalate_default_deleted
            $data_template_default = [];
            $templateDefault = $this->_popupTemplateFactory->create()->getCollection()
                ->addFieldToFilter('status', array('eq' => array(1)))->getData();
            foreach ($templateDefault as $template) {
                $templateClass[] = $template['class'];
            }
            foreach ($popup_type_default as $type) {
                $check = in_array($type['class'], $templateClass);
                if (!$check) {
                    $data_template_default[] = [
                        'template_name' => $type['name'],
                        'template_type' => $type['type'],
                        'html_content' => $this->_helperData->getTemplateDefault($type['path']),
                        'css_style' => '',
                        'class' => $type['class'],
                        'status' => 1
                    ];
                }
            }
            if (!empty($data_template_default)) {
                $popupTemplateModel = $this->_popupTemplateFactory->create();
                $popupTemplateModel->insertMultiple($data_template_default);
            }

            // add class = 'magenest-popup-step' to html_content of template default
            $templateDefault = $this->_popupTemplateFactory->create()->getCollection()
                ->addFieldToFilter('status', array('eq' => array(1)))->getData();
            $type_array = [];
            $key = [];
            $value = [];
            foreach ($popup_type_default as $type) {
                $key[] =  $type['class'];
                $value[] = $type['path'];
            }
            $type_array = array_combine($key, $value);
            foreach ($templateDefault as $template) {
                if (in_array($template['class'], $key)) {
                    $html_content = $this->_helperData->getTemplateDefault($type_array[$template['class']]);
                    $this->_popupTemplateFactory->create()->load($template['template_id'])
                        ->setHtmlContent($html_content)->save();
                }
            }
        }
    }

    function isJSON($string)
    {
        return is_string($string) && is_array(json_decode($string, true)) ? true : false;
    }
}
