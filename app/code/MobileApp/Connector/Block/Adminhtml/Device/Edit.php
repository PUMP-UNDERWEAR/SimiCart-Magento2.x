<?php
namespace MobileApp\Connector\Block\Adminhtml\Device;

/**
 * Admin Connector page
 *
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    )
    {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Initialize cms page edit block
     *
     * @return void
     */
    protected function _construct()
    {

        $this->_objectId = 'device_id';
        $this->_blockGroup = 'MobileApp_Connector';
        $this->_controller = 'adminhtml_device';

        parent::_construct();

        if ($this->_isAllowedAction('MobileApp_Connector::connector_delete')) {
            $this->buttonList->update('delete', 'label', __('Delete'));
        } else {
            $this->buttonList->remove('delete');
        }

        $this->buttonList->remove('save');
        $this->buttonList->remove('reset');
    }

    /**
     * Retrieve text for header element depending on loaded page
     *
     * @return string
     */
    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('device')->getId()) {
            return __("Edit Device '%1'", $this->escapeHtml($this->_coreRegistry->registry('device')->getId()));
        } else {
            return __('New Device');
        }
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }

    /**
     * Getter of url for "Save and Continue" button
     * tab_id will be replaced by desired by JS later
     *
     * @return string
     */
    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('connector/*/save', ['_current' => true, 'back' => 'edit', 'active_tab' => '{{tab_id}}']);
    }

    /**
     * Prepare layout
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _prepareLayout()
    {
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('page_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'page_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'page_content');
                }
            };

            document.addEventListener('DOMContentLoaded', function(){

                // event change Type
                changeType();

            }, false);

            function changeType(){
                var device_type = document.getElementById('type').value;
                switch (device_type) {
                    case '1':
                        document.querySelectorAll('.field-product_id')[0].style.display = 'block';
                        document.querySelectorAll('#product_id')[0].classList.add('required-entry');

                        document.querySelectorAll('.field-category_id')[0].style.display = 'none';
                        document.querySelectorAll('#category_id')[0].classList.remove('required-entry');

                        document.querySelectorAll('.field-notice_url')[0].style.display = 'none';
                        document.querySelectorAll('#notice_url')[0].classList.remove('required-entry');
                        break;
                    case '2':
                        document.querySelectorAll('.field-product_id')[0].style.display = 'none';
                        document.querySelectorAll('#product_id')[0].classList.remove('required-entry');

                        document.querySelectorAll('.field-category_id')[0].style.display = 'block';
                        document.querySelectorAll('#category_id')[0].classList.add('required-entry');

                        document.querySelectorAll('.field-notice_url')[0].style.display = 'none';
                        document.querySelectorAll('#notice_url')[0].classList.remove('required-entry');
                        break;
                    case '3':
                        document.querySelectorAll('.field-product_id')[0].style.display = 'none';
                        document.querySelectorAll('#product_id')[0].classList.remove('required-entry');

                        document.querySelectorAll('.field-category_id')[0].style.display = 'none';
                        document.querySelectorAll('#category_id')[0].classList.remove('required-entry');

                        document.querySelectorAll('.field-notice_url')[0].style.display = 'block';
                        document.querySelectorAll('#notice_url')[0].classList.add('required-entry');
                        break;
                    default:
                        document.querySelectorAll('.field-product_id')[0].style.display = 'block';
                        document.querySelectorAll('#product_id')[0].classList.add('required-entry');

                        document.querySelectorAll('.field-category_id')[0].style.display = 'none';
                        document.querySelectorAll('#category_id')[0].classList.remove('required-entry');

                        document.querySelectorAll('.field-notice_url')[0].style.display = 'none';
                        document.querySelectorAll('#notice_url')[0].classList.remove('required-entry');
                }
            }
        ";
        return parent::_prepareLayout();
    }
}
