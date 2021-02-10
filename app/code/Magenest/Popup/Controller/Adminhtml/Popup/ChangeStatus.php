<?php
namespace Magenest\Popup\Controller\Adminhtml\Popup;

class ChangeStatus extends \Magenest\Popup\Controller\Adminhtml\Popup\Popup {
    /** @var  \Magenest\Popup\Model\ResourceModel\Popup\CollectionFactory $_popupCollectionFactory */
    protected $_popupCollectionFactory;
    /** @var  \Magento\Ui\Component\MassAction\Filter $_filer */
    protected $_filer;

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
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Magenest\Popup\Model\ResourceModel\Popup\CollectionFactory $popupCollectionFactory
    ){
        $this->_popupCollectionFactory = $popupCollectionFactory;
        $this->_filer = $filter;
        parent::__construct($context, $coreRegistry, $popupFactory, $popupTemplateFactory, $widgetFactory, $logger, $mathRandom, $cache, $dateTime, $translateInline);
    }

    public function execute()
    {
        try{
            $collection = $this->_filer->getCollection($this->_popupCollectionFactory->create());
            $count = 0;
            /** @var \Magenest\Popup\Model\Popup $item */
            foreach ($collection->getItems() as $item){
                $status = $item->getPopupStatus();
                if($status == 1){
                    $item->setPopupStatus(0);
                }else{
                    $item->setPopupStatus(1);
                }
                $item->save();
                $count++;
            }
            /* Invalidate Full Page Cache */
            $this->cache->invalidate('full_page');
            $this->messageManager->addSuccess(
                __('A total of %1 record(s) have been changed.', $count)
            );
        }catch (\Exception $exception){
            $this->messageManager->addError($exception->getMessage());
            $this->_logger->critical($exception->getMessage());
        }
        return $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT)->setPath('*/*/index');
    }
}