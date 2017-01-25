<?php
namespace Simi\Simiconnector\Block\Adminhtml\Simivideo;

/**
 * Admin Simiconnector page
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
        $this->_objectId = 'video_id';
        $this->_blockGroup = 'Simi_Simiconnector';
        $this->_controller = 'adminhtml_simivideo';

        parent::_construct();

        if ($this->_isAllowedAction('Simi_Simiconnector::save')) {
            $this->buttonList->update('save', 'label', __('Save'));
            $this->buttonList->add(
                'saveandcontinue',
                [
                    'label' => __('Save and Continue Edit'),
                    'class' => 'save',
                    'data_attribute' => [
                        'mage-init' => [
                            'button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form'],
                        ],
                    ]
                ],
                -100
            );
        } else {
            $this->buttonList->remove('save');
        }

        if ($this->_isAllowedAction('Simi_Simiconnector::simiconnector_delete')) {
            $this->buttonList->update('delete', 'label', __('Delete'));
        } else {
            $this->buttonList->remove('delete');
        }
    }

    /**
     * Retrieve text for header element depending on loaded page
     *
     * @return string
     */
    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('simivideo')->getId()) {
            return __("Edit Video '%1'", $this->escapeHtml($this->_coreRegistry->registry('simivideo')->getId()));
        } else {
            return __('New Video');
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
        return true;
        //return $this->_authorization->isAllowed($resourceId);
    }

    /**
     * Getter of url for "Save and Continue" button
     * tab_id will be replaced by desired by JS later
     *
     * @return string
     */
    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('simiconnector/*/save', ['_current' => true, 'back' => 'edit', 'active_tab' => '{{tab_id}}']);
    }

    /**
     * Prepare layout
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _prepareLayout()
    {
        $arrow_down_img = $this->getViewFileUrl('Simi_Simiconnector::images/arrow_down.png');
        $arrow_up_img = $this->getViewFileUrl('Simi_Simiconnector::images/arrow_up.png');

        $productJsUpdateFunction = '
                    function selectProduct(e) {
                        var vl = e.value;
                        if(e.checked == true){
                            if($("product_ids").value == "")
                                $("product_ids").value = e.value;
                            else {
                                removeValueFromField(vl);
                                $("product_ids").value = $("product_ids").value + ", "+e.value;
                            }
                        }else{
                            removeValueFromField(vl);
                        }
                    }
                    
                    function removeValueFromField(vl){
                        if($("product_ids").value.search(vl) == 0){
                                if ($("product_ids").value.search(vl+", ") != -1)
                                    $("product_ids").value = $("product_ids").value.replace(vl+", ","");
                                else 
                                    $("product_ids").value = $("product_ids").value.replace(vl,"");
                            }else{
                                $("product_ids").value = $("product_ids").value.replace(", "+ vl,"");
                            }
                    }
                    

                    function checkboxAllChecked(el){
                        var product_grid_trs = document.querySelectorAll(".admin__control-checkbox");
                        for (var i=1; i< product_grid_trs.length; i++) {
                            var e = product_grid_trs[i];
                            if (e.id != "checkall_simivideo")
                                e.checked = el.checked;
                        }
                    }
                    
                    function toogleCheckAllProduct(){
                        var product_grid_trs = document.querySelectorAll(".admin__control-checkbox");
                        var el = product_grid_trs[0];
                        if(el.checked == true){
                            for (var i=1; i< product_grid_trs.length; i++) {
                                var e = product_grid_trs[i];
                                selectProduct(e);
                            }
                        }else{
                            for (var i=1; i< product_grid_trs.length; i++) {
                                var e = product_grid_trs[i];
                                selectProduct(e);
                            }
                        }
                    }
                    
        ';
        
        $this->_formScripts[] = $productJsUpdateFunction."
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

                // default: hide product grid
                document.getElementById('product_grid').style.display = 'none';
                
            }, false);
            


            function toogleProduct(){
                var product_grid = document.getElementById('product_grid');
                var product_choose_img = document.getElementById('show_product_grid');

                if(product_grid.style.display == 'none'){
                    product_grid.style.display = 'block';
                    product_choose_img.src = '$arrow_up_img';
                } else {
                    product_grid.style.display = 'none';
                    product_choose_img.src = '$arrow_down_img';
                }
            }

            function changeType(){

                document.getElementById('list_products').style.width = '400px';
                document.getElementById('show_product_grid').style.width = '30px';
                var banner_type = document.getElementById('list_type').value;
                switch (banner_type) {
                    case '1':
                        document.querySelectorAll('.field-list_products')[0].style.display = 'block';
                        document.querySelectorAll('#list_products')[0].classList.add('required-entry');
                        break;
                    default:
                        document.querySelectorAll('.field-list_products')[0].style.display = 'none';
                        document.querySelectorAll('#list_products')[0].classList.remove('required-entry');
                }
            }
        ";
        return parent::_prepareLayout();
    }
}
