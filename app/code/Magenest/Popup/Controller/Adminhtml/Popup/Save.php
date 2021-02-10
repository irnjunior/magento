<?php

namespace Magenest\Popup\Controller\Adminhtml\Popup;

class Save extends \Magenest\Popup\Controller\Adminhtml\Popup\Popup
{
    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $json;

    /**
     * Save constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magenest\Popup\Model\PopupFactory $popupFactory
     * @param \Magenest\Popup\Model\TemplateFactory $popupTemplateFactory
     * @param \Magento\Widget\Model\Widget\InstanceFactory $widgetFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Math\Random $mathRandom
     * @param \Magento\Framework\App\Cache\TypeListInterface $cache
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param \Magento\Framework\Translate\InlineInterface $translateInline
     * @param \Magento\Framework\Serialize\Serializer\Json $json
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magenest\Popup\Model\PopupFactory $popupFactory,
        \Magenest\Popup\Model\TemplateFactory $popupTemplateFactory,
        \Magento\Widget\Model\Widget\InstanceFactory $widgetFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Math\Random $mathRandom,
        \Magento\Framework\App\Cache\TypeListInterface $cache,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Framework\Translate\InlineInterface $translateInline,
        \Magento\Framework\Serialize\Serializer\Json $json
    ){
        $this->json = $json;
        parent::__construct($context, $coreRegistry, $popupFactory, $popupTemplateFactory, $widgetFactory, $logger, $mathRandom, $cache, $dateTime, $translateInline);
    }

    public function execute()
    {
        $widgetInstance = $this->_initWidgetInstance();

        $params = $this->_request->getParams();
        $resultRedirect = $this->resultRedirectFactory->create();
        $redirectBack = $this->getRequest()->getParam('back', false);
        try {

            /** @var \Magenest\Popup\Model\Popup $popupModel */
            $popupModel = $this->_popupFactory->create();
            $popup_name = isset($params['popup_name']) ? $params['popup_name'] : '';
            $popup_type = isset($params['popup_type']) ? $params['popup_type'] : '';
            $popup_status = isset($params['popup_status']) ? $params['popup_status'] : 1;
            $start_date = isset($params['start_date']) ? $params['start_date'] : '';
            if ($start_date != '') {
                $start_date = $this->_dateTime->date('Y-m-d', $start_date);
            }
            $end_date = isset($params['end_date']) ? $params['end_date'] : '';
            if ($end_date != '') {
                $end_date = $this->_dateTime->date('Y-m-d', $end_date);
            }
            $priority = isset($params['priority']) ? $params['priority'] : '';
            $popup_template_id = isset($params['popup_template_id']) ? $params['popup_template_id'] : '';
            $popup_trigger = isset($params['popup_trigger']) ? $params['popup_trigger'] : 1;
            $number_x = isset($params['number_x']) ? $params['number_x'] : 0;
            $popup_positioninpage = isset($params['popup_positioninpage']) ? $params['popup_positioninpage'] : 1;
            $popup_animation = isset($params['popup_animation']) ? $params['popup_animation'] : 1;
            $visible_stores = isset($params['visible_stores']) ? $params['visible_stores'] : '';
            $enable_cookie_lifetime = isset($params['enable_cookie_lifetime']) ? $params['enable_cookie_lifetime'] : 0;
            $cookie_lifetime = isset($params['cookie_lifetime']) ? $params['cookie_lifetime'] : '';
            $coupon_code = isset($params['coupon_code']) ? $params['coupon_code'] : '';
            $thankyou_message = isset($params['thankyou_message']) ? $params['thankyou_message'] : '';
            $popup_link = isset($params['popup_link']) ? $params['popup_link'] : '';
            $enable_floating_button = isset($params['enable_floating_button']) ? $params['enable_floating_button'] : 0;
            $floating_button_display_popup = isset($params['floating_button_display_popup']) ? $params['floating_button_display_popup'] : 0;
            $floating_button_content = isset($params['floating_button_content']) ? $params['floating_button_content'] : '';
            $floating_button_position = isset($params['floating_button_position']) ? $params['floating_button_position'] : '';
            $floating_button_text_color = isset($params['floating_button_text_color']) ? $params['floating_button_text_color'] : '';
            $floating_button_background_color = isset($params['floating_button_background_color']) ? $params['floating_button_background_color'] : '';
            $enable_mailchimp = isset($params['enable_mailchimp']) ? $params['enable_mailchimp'] : 0;
            $api_key = isset($params['api_key']) ? $params['api_key'] : '';
            $audience_id = isset($params['audience_id']) ? $params['audience_id'] : '';
            $html_content = isset($params['html_content']) ? $params['html_content'] : '';

            if (is_array($visible_stores)) {
                $visible_stores = implode(',', $visible_stores);
            }

            $css_style = isset($params['css_style']) ? $params['css_style'] : '';
            if ($popup_name == '' || $popup_type == '') {
                $this->messageManager->addError('Some fields are required!');
            }

            if (isset($params['popup_id']) && $params['popup_id']) {
                $popupModel->load($params['popup_id']);
                $template_id = $popupModel->getPopupTemplateId();
                if (
                    ($html_content == '' && $popup_template_id != '')
                    ||
                    ($popup_template_id != '' && $template_id != '' && $template_id != $popup_template_id)
                ) {
                    /** @var \Magenest\Popup\Model\Template $templateModel */
                    $templateModel = $this->_popupTemplateFactory->create()->load($popup_template_id);
                    $html_content = $templateModel->getHtmlContent();
                }
                if (
                    ($css_style == '' && $popup_template_id != '')
                    ||
                    ($popup_template_id != '' && $template_id != '' && $template_id != $popup_template_id)
                ) {
                    /** @var \Magenest\Popup\Model\Template $templateModel */
                    $templateModel = $this->_popupTemplateFactory->create()->load($popup_template_id);
                    $css_style = $templateModel->getCssStyle();
                }
                if ($popup_type != $popupModel->getPopupType()) {
                    $template = $this->getPopupTemplateByPopupType($popup_type);
                    $popup_template_id = $template->getTemplateId();
                    $html_content = $template->getHtmlContent();
                    $css_style = $template->getCssStyle();
                }
            }
            $popupModel->setPopupName($popup_name);
            $popupModel->setPopupType($popup_type);
            $popupModel->setPopupStatus($popup_status);
            $popupModel->setStartDate($start_date);
            $popupModel->setEndDate($end_date);
            $popupModel->setPriority($priority);
            $popupModel->setPopupTemplateId($popup_template_id);
            $popupModel->setPopupTrigger($popup_trigger);
            $popupModel->setNumberX($number_x);
            $popupModel->setPopupPositioninpage($popup_positioninpage);
            $popupModel->setPopupAnimation($popup_animation);
            $popupModel->setVisibleStores($visible_stores);
            $popupModel->setEnableCookieLifetime($enable_cookie_lifetime);
            $popupModel->setCookieLifetime($cookie_lifetime);
            $popupModel->setCouponCode($coupon_code);
            $popupModel->setThankyouMessage($thankyou_message);
            $popupModel->setHtmlContent($html_content);
            $popupModel->setCssStyle($css_style);
            $popupModel->setPopupLink($popup_link);
            $popupModel->setEnableFloatingButton($enable_floating_button);
            $popupModel->setFloatingButtonDisplayPopup($floating_button_display_popup);
            $popupModel->setFloatingButtonContent($floating_button_content);
            $popupModel->setFloatingButtonPosition($floating_button_position);
            $popupModel->setFloatingButtonTextColor($floating_button_text_color);
            $popupModel->setFloatingButtonBackgroundColor($floating_button_background_color);
            $popupModel->setEnableMailchimp($enable_mailchimp);
            $popupModel->setApiKey($api_key);
            $popupModel->setAudienceId($audience_id);

            $widgetInstanceData = $this->json->serialize($this->getRequest()->getPost('widget_instance'));
            $popupModel->setWidgetInstance($widgetInstanceData);
            if($popup_trigger == 2 && $number_x > 100) {
                $this->messageManager->addError('Please enter a number less than or equal to 100 in Number X field.');
            } else {
                $popupModel->save();

                if ($this->validDateFromTo($start_date, $end_date)) {
                    $popupModel->setPopupStatus(0);
                    $popupModel->save();
                    throw new \Exception($this->validDateFromTo($start_date, $end_date));
                }
                $this->messageManager->addSuccess(__('The Popup has been saved.'));
                /* Invalidate Full Page Cache */
                $this->cache->invalidate('full_page');
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->_logger->critical($e->getMessage());
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $exception) {
            $this->_logger->critical($exception->getMessage());
            $this->messageManager->addError($exception->getMessage());
        }
        if ($redirectBack === 'edit') {
            $resultRedirect->setPath(
                '*/*/edit',
                ['id' => $popupModel->getPopupId(), 'back' => null, '_current' => true]
            );
        } else {
            $resultRedirect->setPath('*/*/index');
        }
        return $resultRedirect;
    }

    public function getPopupTemplateByPopupType($popup_type)
    {
        $collection = $this->_popupTemplateFactory->create()
            ->getCollection()
            ->addFieldToFilter('template_type', $popup_type)
            ->getFirstItem();
        return $collection == null ? $this->_popupTemplateFactory->create() : $collection;
    }
}