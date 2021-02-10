<?php

namespace Magenest\Popup\Controller\Adminhtml\Template;

class Save extends \Magenest\Popup\Controller\Adminhtml\Template\Template
{

    public function execute()
    {
        $params = $this->getRequest()->getParams();
        try {
            /** @var \Magenest\Popup\Model\Template $popupTemplate */
            $popupTemplate = $this->_popupTemplateFactory->create();
            $template_name = $params['template_name'];
            $html_content = $params['html_content'];
            $css_style = $params['css_style'];
            if (isset($params['template_id']) && $params['template_id']) {
                $popupTemplate->load($params['template_id']);
                $template_name_before_edit = $popupTemplate->getTemplateName();
                $template_type = $popupTemplate->getTemplateType();
                $html_content_before_edit = $popupTemplate->getHtmlContent();
                $css_style_before_edit = $popupTemplate->getCssStyle();
                if ($template_name !== $template_name_before_edit ||
                    $html_content !== $html_content_before_edit ||
                    $css_style !== $css_style_before_edit) {
                    $status = $popupTemplate->getStatus();
                    if ($status == 1 || $status == 2)
                        $popupTemplate->setStatus(2);
                    else {
                        $popupTemplate->setStatus(0);
                    }
                }
            } else {
                $template_type = $params['template_type'];
            }
            $popupTemplate->setTemplateName($template_name);
            $popupTemplate->setTemplateType($template_type);
            $popupTemplate->setHtmlContent($html_content);
            $popupTemplate->setCssStyle($css_style);
            $this->_eventManager->dispatch('save_template', ['template' => $popupTemplate]);
            $popupTemplate->save();
            $this->_redirect('*/*/index');
            $this->messageManager->addSuccess(__('The Popup Template template has been saved.'));
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->_logger->critical($e->getMessage());
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $exception) {
            $this->_logger->critical($exception->getMessage());
            $this->messageManager->addError($exception->getMessage());
        }
    }
}