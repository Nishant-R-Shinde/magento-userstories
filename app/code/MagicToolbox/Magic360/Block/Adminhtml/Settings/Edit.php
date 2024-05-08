<?php

namespace MagicToolbox\Magic360\Block\Adminhtml\Settings;

/**
 * Backend form container block
 *
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'object_id';
        $this->_controller = 'adminhtml_settings';
        $this->_blockGroup = 'MagicToolbox_Magic360';
        $this->_headerText = 'Magic 360 Config';

        parent::_construct();

        $this->_formScripts[] = '
            var mtErrMessage = \'Error: It seems that your Static Files Cache is outdated. Please, update it so that module\\\'s scripts can be loaded.\';
            var mtRequireConfigMap = null;
            try {
                mtRequireConfigMap = requirejs.s.contexts._.config.map[\'*\'];
            } catch (e) {
                mtRequireConfigMap = null;
            }
            if (mtRequireConfigMap && typeof(mtRequireConfigMap[\'magic360\']) == \'undefined\') {
                throw mtErrMessage;
            }
            require([\'magic360\'], function(magic360){
                if (typeof(magic360) == "undefined") {
                    throw mtErrMessage;
                }
                magic360.initSettings();
            });
        ';

        $this->removeButton('back');
        $this->removeButton('reset');
        $this->updateButton('save', 'label', __('Save Settings'));
    }
}
