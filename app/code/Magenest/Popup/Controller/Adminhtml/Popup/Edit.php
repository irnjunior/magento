<?php
namespace Magenest\Popup\Controller\Adminhtml\Popup;

class Edit extends \Magenest\Popup\Controller\Adminhtml\Popup\Popup {

    /** @var  \Magento\Framework\View\Result\PageFactory $resultPageFactory */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $json;

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
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Serialize\Serializer\Json $json
    ){
        $this->json = $json;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context, $coreRegistry, $popupFactory, $popupTemplateFactory, $widgetFactory, $logger, $mathRandom, $cache, $dateTime, $translateInline);
    }

    public function execute()
    {
        $widgetInstance = $this->_initWidgetInstance();
        $popupModel = $this->_popupFactory->create();
        try{
            $popup_id = $this->_request->getParam('id');
            if($popup_id){
                $popupModel->load($popup_id);
                $widgetInstanceData = $this->json->unserialize($popupModel['widget_instance']);
                $pageGroupData = [];
                if(!empty($widgetInstanceData)){
                    foreach ($widgetInstanceData as $data){
                        array_push($pageGroupData, array_merge($data[$data['page_group']], array('page_group' => $data['page_group'])));
                    }
                }
                $widgetInstance->setPageGroups($pageGroupData);
                if(!$popupModel->getPopupId()){
                    $this->messageManager->addError(__('This Popup doesn\'t exist'));
                    $resultRedirect = $this->resultRedirectFactory->create();
                    return $resultRedirect->setPath('*/*/index');
                }
            }
        }catch (\Exception $exception){
            $this->messageManager->addError($exception->getMessage());
            $this->_logger->critical($exception->getMessage());
        }
        $this->_coreRegistry->unregister('current_widget_instance');
        $this->_coreRegistry->register('current_widget_instance', $widgetInstance);
        $this->_coreRegistry->register('popup',$popupModel);
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend($popupModel->getPopupId() ? __($popupModel->getPopupName()) : __('New Popup'));
        return $resultPage;
    }
}