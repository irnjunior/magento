<?php

namespace Magenest\Popup\Block\Adminhtml\Popup;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /** @var  \Magento\Framework\Registry $_coreRegistry */
    protected $_coreRegistry;
    protected $_popupCollection;
    protected $_frontendUrl;

    public function __construct(
        \Magenest\Popup\Model\ResourceModel\Popup\CollectionFactory $popupCollection,
        \Magento\Framework\Url $frontendUrl,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Block\Widget\Context $context,
        array $data = []
    )
    {
        $this->_popupCollection = $popupCollection;
        $this->_frontendUrl = $frontendUrl;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        $popup = $this->_coreRegistry->registry('popup');
        $this->_objectId = 'id';
        $this->_blockGroup = 'Magenest_Popup';
        $this->_controller = 'adminhtml_popup';
        parent::_construct();

        /** @var \Magenest\Popup\Model\Popup $popupModel */
        $popupModel = $this->_coreRegistry->registry('popup');
        if ($popupModel->getPopupId()) {
            $this->buttonList->update('save', 'label', __('Save'));
            $this->buttonList->add(
                'template_preview',
                [
                    'label' => __('Preview Template')
                ]
            );
        } else {
            $this->buttonList->remove('save', 'label', __('Save'));
            $this->buttonList->remove('template_preview', 'label', __('Preview Template'));
        }

        $backUrl = $this->getUrl('*/*/index');
        $this->buttonList->update('back', 'onclick', "setLocation('{$backUrl}')");
        $this->buttonList->add(
            'save-and-continue',
            [
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init' => [
                        'button' => [
                            'event' => 'saveAndContinueEdit',
                            'target' => '#edit_form'
                        ]
                    ]
                ]
            ],
            -100
        );
    }

    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl(
            '*/*/save',
            [
                '_current' => true,
                'back' => 'edit',
                'active_tab' => '{{tab_id}}'
            ]
        );
    }
}
