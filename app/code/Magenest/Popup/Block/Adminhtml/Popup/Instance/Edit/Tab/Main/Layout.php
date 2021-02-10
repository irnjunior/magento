<?php


namespace Magenest\Popup\Block\Adminhtml\Popup\Instance\Edit\Tab\Main;


use Magento\Framework\Serialize\Serializer\Json;

class Layout extends \Magento\Widget\Block\Adminhtml\Widget\Instance\Edit\Tab\Main\Layout
{
    /**
     * @var Json
     */
    private $serializer;

    /**
     * Layout constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Catalog\Model\Product\Type $productType
     * @param array $data
     * @param Json|null $serializer
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Catalog\Model\Product\Type $productType,
        Json $serializer,
        array $data = []
    ){
        $this->serializer = $serializer;
        parent::__construct($context, $productType, $data, $serializer);
    }

    /**
     * @var string
     */
    protected $_template = 'Magenest_Popup::instance/edit/layout.phtml';

    /**
     * Generate url to get categories chooser by ajax query
     *
     * @return string
     */
    public function getCategoriesChooserUrl()
    {
        return $this->getUrl('adminhtml/widget_instance/categories', ['_current' => true]);
    }

    /**
     * Generate url to get products chooser by ajax query
     *
     * @return string
     */
    public function getProductsChooserUrl()
    {
        return $this->getUrl('adminhtml/widget_instance/products', ['_current' => true]);
    }

    /**
     * Generate url to get reference block chooser by ajax query
     *
     * @return string
     */
    public function getBlockChooserUrl()
    {
        return $this->getUrl('adminhtml/widget_instance/blocks', ['_current' => true]);
    }

    /**
     * Generate url to get template chooser by ajax query
     *
     * @return string
     */
    public function getTemplateChooserUrl()
    {
        return $this->getUrl('adminhtml/widget_instance/template', ['_current' => true]);
    }

    /**
     * Prepare and retrieve page groups data of widget instance
     *
     * @return array
     */
    public function getPageGroups()
    {
        $widgetInstance = $this->getWidgetInstance();
        $pageGroups = [];
        if ($widgetInstance->getPageGroups()) {
            foreach ($widgetInstance->getPageGroups() as $pageGroup) {
                $pageGroups[] = $this->serializer->serialize($this->getPageGroup($pageGroup));
            }
        }
        return $pageGroups;
    }

    /**
     * @param array $pageGroup
     * @return array
     */
    private function getPageGroup(array $pageGroup)
    {
        return [
            'page_id' => isset($pageGroup['page_id']) ? $pageGroup['page_id'] : '',
            'group' => isset($pageGroup['page_group']) ? $pageGroup['page_group'] : '',
            'block' => isset($pageGroup['block']) ? $pageGroup['block'] : '',
            'for_value' => isset($pageGroup['for']) ? $pageGroup['for'] : '',
            'layout_handle' => isset($pageGroup['layout_handle']) ? $pageGroup['layout_handle'] : '',
            $pageGroup['page_group'] . '_entities' => isset($pageGroup['entities']) ? $pageGroup['entities'] : '',
            'template' => isset($pageGroup['page_template']) ? $pageGroup['page_template'] : '',
        ];
    }

    public function _getDisplayOnSelectHtml()
    {
        $selectBlock = $this->getLayout()->createBlock(
            \Magento\Framework\View\Element\Html\Select::class
        )->setName(
            'widget_instance[<%- data.id %>][page_group]'
        )->setId(
            'widget_instance[<%- data.id %>][page_group]'
        )->setClass(
            'required-entry page_group_select select'
        )->setExtraParams(
            "onchange=\"WidgetInstance.displayPageGroup(this.value+\'_<%- data.id %>\')\""
        )->setOptions(
            $this->_getDisplayOnOptions()
        );
        return $selectBlock->toHtml();
    }

    public function _getDisplayOnOptions()
    {
        $options = [];
        $options[] = ['value' => '', 'label' => $this->escapeHtmlAttr(__('-- Please Select --'))];
        $options[] = [
            'label' => __('Categories'),
            'value' => [
                ['value' => 'anchor_categories', 'label' => $this->escapeHtmlAttr(__('Anchor Categories'))],
                ['value' => 'notanchor_categories', 'label' => $this->escapeHtmlAttr(__('Non-Anchor Categories'))],
            ],
        ];
        foreach ($this->_productType->getTypes() as $typeId => $type) {
            $productsOptions[] = [
                'value' => $typeId . '_products',
                'label' => $this->escapeHtmlAttr($type['label']),
            ];
        }
        array_unshift(
            $productsOptions,
            ['value' => 'all_products', 'label' => $this->escapeHtmlAttr(__('All Product Types'))]
        );
        $options[] = ['label' => $this->escapeHtmlAttr(__('Products')), 'value' => $productsOptions];
        $options[] = [
            'label' => $this->escapeHtmlAttr(__('Generic Pages')),
            'value' => [
                ['value' => 'all_pages', 'label' => $this->escapeHtmlAttr(__('All Pages'))],
                ['value' => 'pages', 'label' => $this->escapeHtmlAttr(__('Specified Page'))],
            ],
        ];
        return $options;
    }
}