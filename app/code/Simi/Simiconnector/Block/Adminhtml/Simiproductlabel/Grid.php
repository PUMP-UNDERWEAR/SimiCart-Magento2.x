<?php
namespace Simi\Simiconnector\Block\Adminhtml\Simiproductlabel;

/**
 * Adminhtml Connector grid
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Simi\Simiconnector\Model\Simiproductlabel
     */
    protected $_productlabelFactory;

    /**
     * @var \Simi\Simiconnector\Model\ResourceModel\Simiproductlabel\CollectionFactory
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
     * @param \Simi\Simiconnector\Model\Simiconnector $connectorPage
     * @param \Simi\Simiconnector\Model\ResourceModel\Simiconnector\CollectionFactory $collectionFactory
     * @param \Magento\Core\Model\PageLayout\Config\Builder $pageLayoutBuilder
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Simi\Simiconnector\Model\SimiproductlabelFactory $simiproductlabelFactory,
        \Simi\Simiconnector\Model\ResourceModel\Simiproductlabel\CollectionFactory $collectionFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Simi\Simiconnector\Helper\Website $websiteHelper,

        array $data = []
    )
    {
        $this->_collectionFactory = $collectionFactory;
        $this->moduleManager = $moduleManager;
        $this->_resource = $resourceConnection;
        $this->_productlabelFactory = $simiproductlabelFactory;
        $this->_websiteHelper = $websiteHelper;

        parent::__construct($context, $backendHelper, $data);
    }


    /**
     * Prepare collection
     *
     * @return \Magento\Backend\Block\Widget\Grid
     */
    protected function _prepareCollection()
    {
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
        $this->addColumn('label_id', [
            'header' => __('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'label_id',
        ]);

        $this->addColumn('name', [
            'header' => __('Label Name'),
            'align' => 'left',
            'index' => 'name'
        ]);

        $this->addColumn('status', [
            'type' => 'options',
            'header' => __('Status'),
            'index' => 'status',
            'options' => $this->_productlabelFactory->create()->toOptionStatusHash(),
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
        return $this->getUrl('*/*/edit', [
            'label_id' => $row->getId()
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
    
    public function _prepareMassaction()
    {
        $this->setMassactionIdField('label_id');
        $this->getMassactionBlock()->addItem(
            'delete',
            [
                'label' => __('Delete'),
                'url' => $this->getUrl('*/*/massDelete'),
                'confirm' => __('Are you sure?')
            ]
        );
        return $this;
    }

}
