<?php
namespace Mage2tv\Mod9\Block\Adminhtml;

class ColorPicker extends \Magento\Config\Block\System\Config\Form\Field
{
    public function __construct( \Magento\Backend\Block\Template\Context $context, array $data = []) {
        parent::__construct($context, $data);
    }
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $html = parent::_getElementHtml($element);
        $backColr = $element->getData('value');
        $html .= '<script type="text/javascript">
            require(["jquery"], function($) {
                $(document).ready(function() {
                    let regex = /^[a-zA-Z0-9]{6}$/;
                    if(regex.test("'.$backColr.'" )) {
                        $("#html-body").css("background-color", "#' . $backColr . '");
                    }else {
                        $("#' . $element->getHtmlId() . '").parent().find(".note").prepend("<div style=\"color: red;\">Enter the correct RGB color value</div>");
                    }
                })
            })
        </script>';
        return $html;
    }
}