<?php
namespace Magenest\Popup\Helper;

use Magenest\Popup\Model\Popup;
use Magenest\Popup\Model\TemplateFactory;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {
    protected $_popupTemplateFactory;

    protected $_popupFactory;
    /** @var \Magento\Backend\App\Config $backendConfig */
    protected $backendConfig;

    /** @var \Magento\Framework\ObjectManagerInterface $_objectManager */
    protected $_objectManager;

    /** @var \Magento\Store\Model\StoreManagerInterface $_storeManager */
    protected $_storeManager;
    /**  @var array */
    protected $isArea = [];
    /**
     * @var \Magento\Framework\Filesystem $_fileSystem
     */
    protected $_fileSystem;
    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    protected $_directoryList;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Theme\Model\ResourceModel\Theme\CollectionFactory
     */
    protected $_themeCollection;

    public function __construct(
        \Magenest\Popup\Model\TemplateFactory $popupTemplateFactory,
        \Magenest\Popup\Model\PopupFactory $popupFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Theme\Model\ResourceModel\Theme\CollectionFactory $themCollection
    ){
        $this->_popupTemplateFactory = $popupTemplateFactory;
        $this->_popupFactory = $popupFactory;
        $this->_objectManager = $objectManager;
        $this->_fileSystem    = $filesystem;
        $this->_directoryList = $directoryList;
        $this->_scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
        $this->_themeCollection = $themCollection;
        parent::__construct($context);
    }

    public function getTemplateType(){
        return [
            \Magenest\Popup\Model\Template::YESNO_BUTTON => __('Yes/No Button'),
            \Magenest\Popup\Model\Template::CONTACT_FORM => __('Contact Form'),
            \Magenest\Popup\Model\Template::SHARE_SOCIAL => __('Social Sharing'),
            \Magenest\Popup\Model\Template::SUBCRIBE => __('Subscribe Form'),
            \Magenest\Popup\Model\Template::STATIC_POPUP => __('Static Popup'),
            \Magenest\Popup\Model\Template::HOT_DEAL => __('Hot Deal')
        ];
    }
    public function getPopupType(){
        return [
            \Magenest\Popup\Model\Popup::YESNO_BUTTON => __('Yes/No Button'),
            \Magenest\Popup\Model\Popup::CONTACT_FORM => __('Contact Form'),
            \Magenest\Popup\Model\Popup::SHARE_SOCIAL => __('Social Sharing'),
            \Magenest\Popup\Model\Popup::SUBCRIBE => __('Subscribe Form'),
            \Magenest\Popup\Model\Popup::STATIC_POPUP => __('Static Popup'),
            \Magenest\Popup\Model\Popup::HOT_DEAL => __('Hot Deal')
        ];
    }
    public function getPopupStatus(){
        return [
            \Magenest\Popup\Model\Popup::ENABLE => __('Enable'),
            \Magenest\Popup\Model\Popup::DISABLE => __('Disable')
        ];
    }
    public function getPopupTrigger(){
        return [
            \Magenest\Popup\Model\Popup::X_SECONDS_ON_PAGE => __('After customers spend X seconds on page'),
            \Magenest\Popup\Model\Popup::SCROLL_PAGE_BY_Y_PERCENT => __('After customers scroll page by X percent'),
            \Magenest\Popup\Model\Popup::VIEW_X_PAGE => __('After customers view X pages'),
            \Magenest\Popup\Model\Popup::EXIT_INTENT => __('Exit Intent')
        ];
    }
    public function getPopupPositioninpage(){
        return [
            \Magenest\Popup\Model\Popup::CENTER => __('Center'),
            \Magenest\Popup\Model\Popup::TOP_LEFT => __('Top Left'),
            \Magenest\Popup\Model\Popup::TOP_RIGHT => __('Top Right'),
            \Magenest\Popup\Model\Popup::TOP_CENTER => __('Top Center'),
            \Magenest\Popup\Model\Popup::BOTTOM_LEFT => __('Bottom Left'),
            \Magenest\Popup\Model\Popup::BOTTOM_RIGHT => __('Bottom Right'),
            \Magenest\Popup\Model\Popup::BOTTOM_CENTER => __('Bottom Center'),
            \Magenest\Popup\Model\Popup::MIDDLE_LEFT => __('Middle Left'),
            \Magenest\Popup\Model\Popup::MIDDLE_RIGHT => __('Middle Right'),
        ];
    }
    public function getPopupAnimation(){
        return [
            \Magenest\Popup\Model\Popup::NONE => __('None'),
            \Magenest\Popup\Model\Popup::ZOOM => __('Zoom In'),
            \Magenest\Popup\Model\Popup::ZOOMOUT => __('Zoom Out'),
            \Magenest\Popup\Model\Popup::MOVE_FROM_LEFT => __('Move From Left'),
            \Magenest\Popup\Model\Popup::MOVE_FROM_RIGHT => __('Move From Right'),
            \Magenest\Popup\Model\Popup::MOVE_FROM_TOP => __('Move From Top'),
            \Magenest\Popup\Model\Popup::MOVE_FROM_BOTTOM => __('Move From Bottom'),
        ];
    }
    public function getPopupPosition(){
        return [
            \Magenest\Popup\Model\Popup::ALLPAGE => __('All Pages'),
            \Magenest\Popup\Model\Popup::HOMEPAGE => __('Home Page'),
            \Magenest\Popup\Model\Popup::CMSPAGE => __('All CMS Pages'),
            \Magenest\Popup\Model\Popup::CATEGORY => __('All Category Pages'),
            \Magenest\Popup\Model\Popup::PRODUCT => __('All Product Pages')
        ];
    }

    public function getButtonPosition(){
        return [
            \Magenest\Popup\Model\Popup::BUTTON_CENTER => __('Center'),
            \Magenest\Popup\Model\Popup::BUTTON_BOTTOM_LEFT => __('Bottom Left'),
            \Magenest\Popup\Model\Popup::BUTTON_BOTTOM_RIGHT=> __('Bottom Right'),
        ];
    }

    public function getButtonDisplayPopup(){
        return [
            \Magenest\Popup\Model\Popup::BEFORE_CLICK_BUTTON => __('Before Click Button'),
            \Magenest\Popup\Model\Popup::AFTER_CLICK_BUTTON => __('After Click Button'),
        ];
    }
    public function getTemplateNameById($template_id){
        $template_name = $template_id;
        /** @var \Magenest\Popup\Model\Template $templateModel */
        $templateModel = $this->_popupTemplateFactory->create()->load($template_id);
        if($templateModel->getTemplateId()){
            $template_name = $templateModel->getTemplateName();
        }
        return $template_name;
    }
    /**
     * Is Admin Store
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->isArea(\Magento\Framework\App\Area::AREA_ADMINHTML);
    }
    /**
     * @param string $area
     * @return mixed
     */
    public function isArea($area = \Magento\Framework\App\Area::AREA_FRONTEND)
    {
        if (!isset($this->isArea[$area])) {
            /** @var \Magento\Framework\App\State $state */
            $state = $this->_objectManager->get('Magento\Framework\App\State');

            try {
                $this->isArea[$area] = ($state->getAreaCode() == $area);
            } catch (\Exception $e) {
                $this->isArea[$area] = false;
            }
        }
        return $this->isArea[$area];
    }
    public function isModuleEnable(){
        return $this->getConfig('enabled');
    }
    public function getConfig($code,$storeId = null){
        $code = ($code !== '') ? '/' . $code : '';
        return $this->getConfigValue('magenest_popup/general' . $code, $storeId);
    }
    public function getConfigValue($field, $scopeValue = null, $scopeType = \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
    {
        if (!$this->isArea() && is_null($scopeValue)) {
            /** @var \Magento\Backend\App\Config $backendConfig */
            if (!$this->backendConfig) {
                $this->backendConfig = $this->_objectManager->get('Magento\Backend\App\ConfigInterface');
            }
            return $this->backendConfig->getValue($field);
        }
        return $this->scopeConfig->getValue($field, $scopeType, $scopeValue);
    }
    /**
     * Get default template path
     * @param $templateId
     * @param string $type
     * @return string
     */
    public function getTemplatePath($templateId, $type = '.html')
    {
        /** Get directory of Data.php */
        $currentDir = __DIR__;

        /** Get root directory(path of magento's project folder) */
        $rootPath = $this->_directoryList->getRoot();

        $currentDirArr = explode('\\', $currentDir);
        if (count($currentDirArr) == 1) {
            $currentDirArr = explode('/', $currentDir);
        }

        $rootPathArr = explode('/', $rootPath);
        if (count($rootPathArr) == 1) {
            $rootPathArr = explode('\\', $rootPath);
        }

        $basePath = '';
        for ($i = count($rootPathArr); $i < count($currentDirArr) - 1; $i++) {
            $basePath .= $currentDirArr[$i] . '/';
        }

        $templatePath = $basePath . 'view/adminhtml/templates/popup/template/';

        return $templatePath . $templateId . $type;
    }
    /**
     * @param $relativePath
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function readFile($relativePath)
    {
        $rootDirectory = $this->_fileSystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::ROOT);

        return $rootDirectory->readFile($relativePath);
    }
    public function getTemplateDefault($templatePath){
        return $this->readFile($this->getTemplatePath($templatePath));
    }

    public function getAllThemeId(){
        return $this->_themeCollection->create()->getData();
    }

    public function getPopupTemplateDefault(){
        $popup_type = [
            [
                'path' => 'yesno_button/popup_1',
                'type' => '1',
                'class' => 'popup-default-3',
                'name' => 'Default Template 0'
            ],
            [
                'path' => 'contact_form/popup_1',
                'type' => '2',
                'class' => 'popup-default-1',
                'name' => 'Default Template 1'
            ],
            [
                'path' => 'contact_form/popup_2',
                'type' => '2',
                'class' => 'popup-default-4',
                'name' => 'Default Template 2'
            ],
            [
                'path' => 'share_social/popup_1',
                'type' => '3',
                'class' => 'popup-default-5',
                'name' => 'Default Template 3'
            ],
            [
                'path' => 'subcribe_form/popup_2',
                'type' => '4',
                'class' => 'popup-default-6',
                'name' => 'Default Template 4'
            ],
            [
                'path' => 'subcribe_form/popup_1',
                'type' => '4',
                'class' => 'popup-default-2',
                'name' => 'Default Template 5'
            ],
            [
                'path' => 'static_form/popup_1',
                'type' => '5',
                'class' => 'popup-default-7',
                'name' => 'Default Template 6'
            ],
            [
                'path' => 'static_form/popup_2',
                'type' => '5',
                'class' => 'popup-default-8',
                'name' => 'Default Template 7'
            ],
            [
                'path' => 'subcribe_form/popup_3',
                'type' => '4',
                'class' => 'popup-default-9',
                'name' => 'Default Template 8'
            ],
            [
                'path' => 'subcribe_form/popup_4',
                'type' => '4',
                'class' => 'popup-default-10',
                'name' => 'Default Template 9'
            ],
            [
                'path' => 'yesno_button/popup_3',
                'type' => '1',
                'class' => 'popup-default-11',
                'name' => 'Default Template 10'
            ],
            [
                'path' => 'static_form/popup_3',
                'type' => '5',
                'class' => 'popup-default-12',
                'name' => 'Default Template 11'
            ],
            [
                'path' => 'static_form/popup_4',
                'type' => '5',
                'class' => 'popup-default-13',
                'name' => 'Default Template 12'
            ],
            [
                'path' => 'subcribe_form/popup_5',
                'type' => '4',
                'class' => 'popup-default-14',
                'name' => 'Default Template 13'
            ],
            [
                'path' => 'contact_form/popup_3',
                'type' => '2',
                'class' => 'popup-default-15',
                'name' => 'Default Template 14'
            ],
            [
                'path' => 'subcribe_form/popup_6',
                'type' => '4',
                'class' => 'popup-default-16',
                'name' => 'Default Template 15'
            ],
            [
                'path' => 'share_social/popup_2',
                'type' => '3',
                'class' => 'popup-default-17',
                'name' => 'Default Template 16'
            ],
            [
                'path' => 'subcribe_form/popup_7',
                'type' => '4',
                'class' => 'popup-default-18',
                'name' => 'Default Template 17'
            ],
            [
                'path' => 'subcribe_form/popup_8',
                'type' => '4',
                'class' => 'popup-default-19',
                'name' => 'Default Template 18'
            ],
            [
                'path' => 'subcribe_form/popup_9',
                'type' => '4',
                'class' => 'popup-default-20',
                'name' => 'Default Template 19'
            ],
            [
                'path' => 'static_form/popup_8',
                'type' => '5',
                'class' => 'popup-default-21',
                'name' => 'Default Template 20'
            ],
            [
                'path' => 'static_form/popup_7',
                'type' => '5',
                'class' => 'popup-default-22',
                'name' => 'Default Template 21'
            ],
            [
                'path' => 'subcribe_form/popup_10',
                'type' => '4',
                'class' => 'popup-default-23',
                'name' => 'Default Template 22'
            ],
            [
                'path' => 'static_form/popup_6',
                'type' => '5',
                'class' => 'popup-default-24',
                'name' => 'Default Template 23'
            ],
            [
                'path' => 'static_form/popup_9',
                'type' => '5',
                'class' => 'popup-default-25',
                'name' => 'Default Template 24'
            ]
            ,
            [
                'path' => 'subcribe_form/popup_13',
                'type' => '4',
                'class' => 'popup-default-27',
                'name' => 'Default Template 25'
            ],
            [
                'path' => 'static_form/popup_12',
                'type' => '5',
                'class' => 'popup-default-28',
                'name' => 'Default Template 26'
            ],
            [
                'path' => 'share_social/popup_3',
                'type' => '3',
                'class' => 'popup-default-29',
                'name' => 'Default Template 27'
            ],
            [
                'path' => 'share_social/popup_4',
                'type' => '3',
                'class' => 'popup-default-30',
                'name' => 'Default Template 28'
            ],
            [
                'path' => 'contact_form/popup_6',
                'type' => '2',
                'class' => 'popup-default-31',
                'name' => 'Default Template 29'
            ],
            [
                'path' => 'subcribe_form/popup_14',
                'type' => '4',
                'class' => 'popup-default-32',
                'name' => 'Default Template 30'
            ],
            [
                'path' => 'static_form/popup_13',
                'type' => '5',
                'class' => 'popup-default-33',
                'name' => 'Default Template 31'
            ],
            [
                'path' => 'yesno_button/popup_2',
                'type' => '1',
                'class' => 'popup-default-34',
                'name' => 'Default Template 32'
            ],
            [
                'path' => 'yesno_button/popup_4',
                'type' => '1',
                'class' => 'popup-default-35',
                'name' => 'Default Template 33'
            ],
            [
                'path' => 'subcribe_form/popup_12',
                'type' => '4',
                'class' => 'popup-default-36',
                'name' => 'Default Template 34'
            ],
            [
                'path' => 'contact_form/popup_4',
                'type' => '2',
                'class' => 'popup-default-26',
                'name' => 'Default Template 35'
            ],
            [
                'path' => 'contact_form/popup_5',
                'type' => '2',
                'class' => 'popup-default-37',
                'name' => 'Default Template 36'
            ],
            [
                'path' => 'static_form/popup_10',
                'type' => '5',
                'class' => 'popup-default-38',
                'name' => 'Default Template 37'
            ],
            [
                'path' => 'subcribe_form/popup_11',
                'type' => '4',
                'class' => 'popup-default-39',
                'name' => 'Default Template 38'
            ],
            [
                'path' => 'hot_deal/popup_1',
                'type' => '6',
                'class' => 'popup-default-40',
                'name' => 'Default Template 39'
            ],
            [
                'path' => 'hot_deal/popup_2',
                'type' => '6',
                'class' => 'popup-default-41',
                'name' => 'Default Template 40'
            ]
        ];
        return $popup_type;
    }
}