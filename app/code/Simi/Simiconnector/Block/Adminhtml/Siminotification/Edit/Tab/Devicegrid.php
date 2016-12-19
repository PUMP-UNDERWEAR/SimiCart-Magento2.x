<?php
namespace Simi\Simiconnector\Block\Adminhtml\Siminotification\Edit\Tab;

/**
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Devicegrid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Simi\Simiconnector\Model\Device
     */
    protected $_deviceFactory;

    /**
     * @var \Simi\Simiconnector\Model\ResourceModel\Device\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var order model
     */
    protected $_resource;

    /**
     * @var \Simi\Simiconnector\Helper\Website
     **/
    protected $_websiteHelper;


    protected $_objectManager;
    
    public $storeview_id;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Simi\Simiconnector\Model\Simiconnector $connectorPage
     * @param \Simi\Simiconnector\Model\ResourceModel\Simiconnector\CollectionFactory $collectionFactory
     * @param \Magento\Core\Model\PageLayout\Config\Builder $pageLayoutBuilder
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Simi\Simiconnector\Model\DeviceFactory $deviceFactory,
        \Simi\Simiconnector\Model\ResourceModel\Device\CollectionFactory $collectionFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Simi\Simiconnector\Helper\Website $websiteHelper,

        array $data = []
    )
    {
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_collectionFactory = $collectionFactory;
        $this->moduleManager = $moduleManager;
        $this->_resource = $resourceConnection;
        $this->_deviceFactory = $deviceFactory;
        $this->_websiteHelper = $websiteHelper;

        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('deviceGrid');
        $this->setDefaultSort('device_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }

    /**
     * Prepare collection
     *
     * @return \Magento\Backend\Block\Widget\Grid
     */
    protected function _prepareCollection()
    { 
        if (!$this->storeview_id && ($storeviewId = $this->getRequest()->getParam('storeview_id'))) {
            $this->storeview_id = $storeviewId;
        }
        if (!$this->storeview_id)
            $this->storeview_id = $this->_objectManager->get('\Magento\Store\Model\Store')->getCollection()->getFirstItem()->getId();
        $collection = $this->_collectionFactory->create()->addFieldToFilter('storeview_id',$this->storeview_id);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare columns
     *
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'in_devices',
            [
                'type' => 'checkbox',
                'html_name' => 'devices_id',
                'required' => true,
                'values' => $this->_getSelectedDevices(),
                'align' => 'center',
                'index' => 'entity_id',
                'header_css_class' => 'col-select',
                'column_css_class' => 'col-select',
                'renderer'  => '\Simi\Simiconnector\Block\Adminhtml\Siminotification\Edit\Tab\Devicerender',
            ]
        );
        
        $this->addColumn('device_id', [
            'header' => __('ID'),
            'index' => 'device_id',
        ]);

        $this->addColumn('storeview_id', [
            'type' => 'options',
            'header' => __('Storeview'),
            'index' => 'storeview_id',
            'options' => $this->_deviceFactory->create()->toOptionStoreviewHash(),
        ]);

        $this->addColumn('plaform_id', [
            'type' => 'options',
            'header' => __('Device Type'),
            'index' => 'plaform_id',
            'options' => $this->_deviceFactory->create()->toOptionDeviceHash(),
        ]);

        $this->addColumn('city', [
            'header' => __('City'),
            'index' => 'city',
        ]);

        $this->addColumn('state', [
            'header' => __('State/Province'),
            'index' => 'state',
        ]);

        $this->addColumn('country', [
            'type' => 'options',
            'header' => __('Country'),
            'index' => 'country',
            'options' => $this->_deviceFactory->create()->toOptionCountryHash(),
        ]);

        $this->addColumn('is_demo', [
            'type' => 'options',
            'header' => __('Is Demo'),
            'index' => 'is_demo',
            'options' => $this->_deviceFactory->create()->toOptionDemoHash(),
        ]);

        $this->addColumn('created_time', [
            'type'      => 'datetime',
            'header'    => __('Created Date'),
            'index'     => 'created_time',
        ]);
        
        $this->addColumn('app_id', [
            'header' => __('App Id'),
            'index' => 'app_id',
        ]);
        
        $this->addColumn('build_version', [
            'header' => __('Build Version'),
            'index' => 'build_version',
        ]);
        
        return parent::_prepareColumns();
    }

    /**
     * Row click url
     *
     * @param \Magento\Framework\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return false;
    }

    /**
     * Get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->_getData(
            'grid_url'
        ) ? $this->_getData(
            'grid_url'
        ) : $this->getUrl('simiconnector/*/devicegrid', ['_current' => true, 'notice_id'=> $this->getRequest()->getParam('notice_id'), 'storeview_id' => $this->storeview_id]);
    }
    
    /**
     * @return array
     */
    protected
    function _getSelectedDevices()
    {
        $devices = array_keys($this->getSelectedDevices());
        return $devices;
    }
    
    
    public function setStoreview($storeviewid) {
        $this->storeview_id = $storeviewid;
        return $this;
    }

    /**
     * @return array
     */
    public
    function getSelectedDevices()
    {
        $noticeId = $this->getRequest()->getParam('notice_id');
        if (!isset($noticeId)) {
            $noticeId = 0;
        }

        $notification = $this->_objectManager->get('Simi\Simiconnector\Model\Siminotification')->load($noticeId);
        $devices = array();
        
        if($notification->getId()){
            $devices = explode(',',  str_replace(' ', '', $notification->getData('devices_pushed')));
        }

        $proIds = array();

        foreach ($devices as $device) {
            $proIds[$device] = array('id' => $device);
        }
        return $proIds;
        
    }

}