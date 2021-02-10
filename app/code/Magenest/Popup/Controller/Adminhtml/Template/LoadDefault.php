<?php

namespace Magenest\Popup\Controller\Adminhtml\Template;

use Magenest\Popup\Model\ResourceModel\Template\Collection;
use Magenest\Popup\Model\TemplateFactory;
use Psr\Log\LoggerInterface;

class LoadDefault extends \Magento\Backend\App\Action
{
    /** @var \Magenest\Popup\Helper\Data $_helperData */
    protected $_helperData;
    /** @var  \Magenest\Popup\Model\TemplateFactory $_popupTemplateFactory */
    protected $_popupTemplateFactory;

    /** @var LoggerInterface $_logger */
    protected $_logger;

    public function __construct(
        \Magenest\Popup\Helper\Data $helperData,
        \Magenest\Popup\Model\TemplateFactory $popupTemplateFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Backend\App\Action\Context $context
    )
    {
        $this->_helperData = $helperData;
        $this->_popupTemplateFactory = $popupTemplateFactory;
        $this->_logger = $logger;
        parent::__construct($context);
    }

    public function execute()
    {
        try {
            $popup_type = $this->_helperData->getPopupTemplateDefault();
            $count = 0;

            $this->_popupTemplateFactory->create()->getCollection()->getData();
            $data = [];
            $popupTemplate = $this->_popupTemplateFactory->create()->getCollection()
                ->addFieldToFilter('status', array('eq' => 1))->getData();
            if (!empty($popupTemplate)) {
                foreach ($popupTemplate as $template) {
                    $templateClass[] = $template['class'];
                }
                foreach ($popup_type as $type) {
                    $check = in_array($type['class'], $templateClass);
                    if (!$check) {
                        $data[] = [
                            'template_name' => $type['name'],
                            'template_type' => $type['type'],
                            'html_content' => $this->_helperData->getTemplateDefault($type['path']),
                            'css_style' => '',
                            'class' => $type['class'],
                            'status' => 1
                        ];
                        $count++;
                    }
                }
            } else {
                foreach ($popup_type as $type) {
                    $data[] = [
                        'template_name' => $type['name'],
                        'template_type' => $type['type'],
                        'html_content' => $this->_helperData->getTemplateDefault($type['path']),
                        'css_style' => '',
                        'class' => $type['class'],
                        'status' => 1
                    ];
                    $count++;
                }
            }

            if (!empty($data)) {
                $popupTemplateModel = $this->_popupTemplateFactory->create();
                $popupTemplateModel->insertMultiple($data);
            }
            $this->messageManager->addSuccess(
                __('A total of %1 record(s) have been inserted.', $count)
            );
        } catch (\Exception $exception) {
            $this->_logger->critical($exception->getMessage());
            $this->messageManager->addError($exception->getMessage());
        }
        return $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT)->setPath('*/*/index');
    }
}