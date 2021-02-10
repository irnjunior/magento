<?php
namespace Magenest\Popup\Controller\Adminhtml\Popup;

class Index extends \Magenest\Popup\Controller\Adminhtml\Popup\Popup {
    /** @var  \Magento\Framework\View\Result\PageFactory $resultPageFactory */
    protected $resultPageFactory;

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
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ){
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context, $coreRegistry, $popupFactory, $popupTemplateFactory, $widgetFactory, $logger, $mathRandom, $cache, $dateTime, $translateInline);
    }

    public function execute()
    {
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->addBreadcrumb(__('Manage Popup'), __('Manage Popup'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Popup'));

        return $resultPage;
    }
    public function _isAllowed(){
        return $this->_authorization->isAllowed('Magenest_Popup::popup');
    }
}