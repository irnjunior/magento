<?php

namespace Magenest\Popup\Block\Adminhtml\Popup\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Element\Dependence;

class Setting extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{

    /** @var  \Magenest\Popup\Model\PopupFactory $_popupFactory */
    protected $_popupFactory;

    /** @var  \Magenest\Popup\Helper\Data $_helperData */
    protected $_helperData;

    /** @var \Magento\Store\Model\System\Store $_systemStore */
    protected $_systemStore;

    public function __construct(
        \Magenest\Popup\Model\PopupFactory $popupFactory,
        \Magenest\Popup\Helper\Data $helperData,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    )
    {
        $this->_popupFactory = $popupFactory;
        $this->_helperData = $helperData;
        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('edit_form');
        $this->setTitle(__('Setting'));
    }

    public function _prepareForm()
    {
        /** @var \Magenest\Popup\Model\Popup $popupModel */
        $popupModel = $this->_coreRegistry->registry('popup');
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('popup_');

        $fieldset = $form->addFieldset(
            'setting_fieldset',
            [
                'legend' => __('Popup Configuration'),
                'class' => 'fieldset-wide'
            ]
        );

        $fieldset->addType('popup_template_type', '\Magenest\Popup\Block\Adminhtml\Popup\Custom\PopupSelectTemplate');

        if ($popupModel->getPopupId()) {
            $fieldset->addField(
                'popup_id',
                'hidden',
                [
                    'name' => 'popup_id',
                    'label' => __('Id'),
                    'title' => __('Id')
                ]
            );
        }
        $fieldset->addField(
            'popup_template_id',
            'popup_template_type',
            [
                'name' => 'popup_template_id',
                'label' => __('Popup Template'),
                'required' => true
            ]
        );

        // floating button
        $fieldset->addField(
            'enable_floating_button',
            'select',
            [
                'name' => 'enable_floating_button',
                'label' => __('Enable Floating Button'),
                'required' => true,
                'values' => [0 => __('No'), 1 => __('Yes')]
            ]
        );

        $fieldset->addField(
            'floating_button_display_popup',
            'select',
            [
                'name' => 'floating_button_display_popup',
                'label' => __('Display Popup'),
                'required' => true,
                'values' => $this->_helperData->getButtonDisplayPopup()
            ]
        );

        $fieldset->addField(
            'floating_button_content',
            'text',
            [
                'name' => 'floating_button_content',
                'label' => __('Floating Button Content'),
                'require' => true,
            ]
        );

        $fieldset->addField(
            'floating_button_position',
            'select',
            [
                'name' => 'floating_button_position',
                'label' => __('Floating Button Position'),
                'require' => true,
                'values' => $this->_helperData->getButtonPosition()
            ]
        );

        // Text Button Color
        $textColor = $fieldset->addField(
            'floating_button_text_color',
            'text',
            [
                'name' => 'floating_button_text_color',
                'label' => __('Floating Button Text Color'),
                'require' => true,
            ]
        );
        $textColorValue = $textColor->getData('value');
        $html = $this->getColor($textColor, $textColorValue);
        $textColor->setAfterElementHtml($html);

        // Background Button Color
        $backgroundButtonColor = $fieldset->addField(
            'floating_button_background_color',
            'text',
            [
                'name' => 'floating_button_background_color',
                'label' => __('Floating Button Background Color'),
                'require' => true,
            ]
        );
        $backgroundColorValue = $backgroundButtonColor->getData('value');
        $html = $this->getColor($backgroundButtonColor, $backgroundColorValue);
        $backgroundButtonColor->setAfterElementHtml($html);
        $fieldset->addField(
            'popup_trigger',
            'select',
            [
                'name' => 'popup_trigger',
                'label' => __('Popup Trigger'),
                'required' => true,
                'values' => $this->_helperData->getPopupTrigger()
            ]
        );
        $fieldset->addField(
            'number_x',
            'text',
            [
                'name' => 'number_x',
                'label' => __('Number X'),
                'title' => __('Number X'),
                'class' => 'validate-digits validate-greater-than-zero'
            ]
        );

        $enable_mailchimp = $fieldset->addField(
            'enable_mailchimp',
            'select',
            [
                'name' => 'enable_mailchimp',
                'label' => __('Enable Mailchimp'),
                'required' => true,
                'values' => [0 => __('No'), 1 => __('Yes')]
            ]
        );

        $api_key = $fieldset->addField(
            'api_key',
            'text',
            [
                'name' => 'api_key',
                'label' => 'Mailchimp Api Key',
                'required' => true
            ]
        );

        $audience_id = $fieldset->addField(
            'audience_id',
            'text',
            [
                'name' => 'audience_id',
                'label' => 'Mailchimp Audience Id',
                'required' => true
            ]
        );


        $fieldset->addField(
            'popup_positioninpage',
            'select',
            [
                'name' => 'popup_positioninpage',
                'label' => __('Popup Position in page'),
                'required' => true,
                'values' => $this->_helperData->getPopupPositioninpage()
            ]
        );

        $fieldset->addField(
            'popup_animation',
            'select',
            [
                'name' => 'popup_animation',
                'label' => __('Popup Animation'),
                'required' => true,
                'values' => $this->_helperData->getPopupAnimation()
            ]
        );
        if ($this->_storeManager->isSingleStoreMode()) {
            $storeId = $this->_storeManager->getStore(true)->getStoreId();
            $fieldset->addField('visible_stores', 'hidden', ['name' => 'visible_stores[]', 'value' => $storeId]);
        } else {
            $field = $fieldset->addField(
                'visible_stores',
                'multiselect',
                [
                    'name' => 'visible_stores[]',
                    'label' => __('Stores'),
                    'title' => __('Stores'),
                    'values' => $this->_systemStore->getStoreValuesForForm(false, true),
                    'required' => true,
                ]
            );
            $renderer = $this->getLayout()->createBlock(
                'Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element'
            );
            $field->setRenderer($renderer);
        }
        $fieldset->addField(
            'enable_cookie_lifetime',
            'select',
            [
                'name' => 'enable_cookie_lifetime',
                'label' => __('Enable Cookie Lifetime'),
                'required' => true,
                'values' => [0 => __('No'), 1 => __('Yes')],
                'note' => __('Duration (in seconds) before popup appears again for the same customer')
            ]
        );
        $fieldset->addField(
            'cookie_lifetime',
            'text',
            [
                'name' => 'cookie_lifetime',
                'label' => __('Cookie Lifetime'),
                'class' => 'validate-digits validate-greater-than-zero',
                'note' => __("Cookie lifetime (in seconds)"),
            ]
        );


        // coupon code
        $fieldset->addField(
            'coupon_code',
            'text',
            [
                'name' => 'coupon_code',
                'label' => __('Coupon Code'),
                'title' => __('Coupon Code'),
            ]
        );
        $fieldset->addField(
            'thankyou_message',
            'textarea',
            [
                'name' => 'thankyou_message',
                'label' => __('Thank You Message'),
                'title' => __('Thank You Message')
            ]
        );
        $fieldset->addField(
            'popup_link',
            'text',
            [
                'name' => 'popup_link',
                'label' => __('Add Link'),
                'title' => __('Add Link')
            ]
        );


        if (!empty($popupModel->getVisibleStores()) && $popupModel->getVisibleStores()) {
            $popupModel->setVisibleStores(explode(',', $popupModel->getVisibleStores()));
        }

        /* @var $layoutBlock \Magenest\Popup\Block\Adminhtml\Popup\Instance\Edit\Tab\Main\Layout */
        $layoutBlock = $this->getLayout()->createBlock(
            \Magenest\Popup\Block\Adminhtml\Popup\Instance\Edit\Tab\Main\Layout::class
        )->setWidgetInstance(
            $this->_coreRegistry->registry('current_widget_instance')
        );

        $fieldset = $form->addFieldset('layout_updates_fieldset', ['legend' => __('Layout Updates')]);
        $fieldset->addField('layout_updates', 'note', []);
        $form->getElement('layout_updates_fieldset')->setRenderer($layoutBlock);

        $form->setValues($popupModel->getData());
        $this->setForm($form);

        $this->setChild(
            'form_after',
            $this->getLayout()->createBlock(Dependence::class)
                ->addFieldMap($enable_mailchimp->getHtmlId(), $enable_mailchimp->getName())
                ->addFieldMap($api_key->getHtmlId(), $api_key->getName())
                ->addFieldMap($audience_id->getHtmlId(), $audience_id->getName())
                ->addFieldDependence($api_key->getName(), $enable_mailchimp->getName(), 1)
                ->addFieldDependence($audience_id->getName(), $enable_mailchimp->getName(), 1)
        );

        return parent::_prepareForm();
    }

    public function getTabLabel()
    {
        return __('Popup Configuration');
    }

    public function getTabTitle()
    {
        return __('Popup Configuration');
    }

    public function canShowTab()
    {
        /** @var \Magenest\Popup\Model\Popup $popupModel */
        $popupModel = $this->_coreRegistry->registry('popup');
        if ($popupModel->getPopupId()) {
            return true;
        } else {
            return false;
        }
    }

    public function isHidden()
    {
        /** @var \Magenest\Popup\Model\Popup $popupModel */
        $popupModel = $this->_coreRegistry->registry('popup');
        if ($popupModel->getPopupId()) {
            return false;
        } else {
            return true;
        }
    }

    public function getColor($color, $value)
    {
        $html = sprintf(
            '<script type="text/javascript">
                require(["jquery", "jquery/colorpicker/js/colorpicker"], function ($) {
                    $(function() {
                        var $el = $("#%s");
                        $el.css("backgroundColor", "#%s");
                        $el.ColorPicker({
                            color: "%s",
                            onChange: function (hsb, hex, rgb) {
                                $el.css("backgroundColor", "#" + hex).val(hex);
                            }
                        });
                    });
                });
            </script>', $color->getHtmlId(), $value, $value
        );
        return $html;
    }
}