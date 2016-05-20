<?php

namespace MobileApp\Connector\Controller\Adminhtml\Notice;

class Productgrid extends \Magento\Catalog\Controller\Adminhtml\Product
{

    protected $resultLayoutFactory;


    /**
     * @var Product\Builder
     */
    protected $productBuilder;

    /**
     * @param Action\Context $context
     * @param Product\Builder $productBuilder
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Catalog\Controller\Adminhtml\Product\Builder $productBuilder,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
    ) {
        parent::__construct($context, $productBuilder);
        $this->resultLayoutFactory = $resultLayoutFactory;
    }



    public function execute()
    {
        $this->productBuilder->build($this->getRequest());
        $resultLayout = $this->resultLayoutFactory->create();
        $resultLayout->getLayout()->getBlock('connector.notice.edit.tab.productgrid')
            ->setProducts($this->getRequest()->getPost('products', null));
        return $resultLayout;
    }
}