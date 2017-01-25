<?php
namespace Simi\Simiconnector\Block\Adminhtml\Productlist;

/**
 * Adminhtml Simiconnector grid
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Simi\Simiconnector\Model\Productlist
     */
    protected $_productlistFactory;

    /**
     * @var \Simi\Simiconnector\Model\ResourceModel\Productlist\CollectionFactory
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

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Simi\Simiconnector\Model\Simiconnector $simiconnectorPage
     * @param \Simi\Simiconnector\Model\ResourceModel\Simiconnector\CollectionFactory $collectionFactory
     * @param \Magento\Core\Model\PageLayout\Config\Builder $pageLayoutBuilder
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Simi\Simiconnector\Model\ProductlistFactory $productlistFactory,
        \Simi\Simiconnector\Model\ResourceModel\Productlist\CollectionFactory $collectionFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Simi\Simiconnector\Helper\Website $websiteHelper,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->moduleManager = $moduleManager;
        $this->_resource = $resourceConnection;
        $this->_productlistFactory = $productlistFactory;
        $this->_websiteHelper = $websiteHelper;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('productlistGrid');
        $this->setDefaultSort('productlist_id');
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
        $webId = $this->getWebsiteIdFromUrl();
        $collection = $this->_collectionFactory->create();

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
        $this->addColumn('productlist_id', [
            'header' => __('ID'),
            'index' => 'productlist_id',
        ]);

        $this->addColumn('list_title', [
            'header' => __('List Title'),
            'index' => 'list_title',
        ]);

        
        $this->addColumn('sort_order', [
            'header' => __('Sort Order'),
            'index' => 'sort_order',
        ]);

        $this->addColumn('list_status', [
            'type' => 'options',
            'header' => __('Status'),
            'index' => 'list_status',
            'options' => $this->_productlistFactory->create()->toOptionStatusHash(),
        ]);

        $this->addColumn(
            'action',
            [
                'header' => __('View'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => [
                    [
                        'caption' => __('Edit'),
                        'url' => [
                            'base' => '*/*/edit',
                            'params' => ['store' => $this->getRequest()->getParam('store')]
                        ],
                        'field' => 'productlist_id'
                    ]
                ],
                'sortable' => false,
                'filter' => false,
                'header_css_class' => 'col-action',
                'column_css_class' => 'col-action',
            ]
        );

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
        return $this->getUrl('*/*/edit', [
            'productlist_id' => $row->getId()
        ]);
    }

    /**
     * Get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', ['_current' => true]);
    }

    /**
     * @return mixed
     */
    public function getWebsiteIdFromUrl()
    {
        return $this->_websiteHelper->getWebsiteIdFromUrl();
    }
}
