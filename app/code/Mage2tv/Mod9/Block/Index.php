<?php
namespace Mage2tv\Mod9\Block;
use Magento\Framework\View\Element\Template;
use Magento\Framework\App\Config\ScopeConfigInterface;
class Index extends Template {
    protected $scopeConfig;
    public function __construct(Template\Context $context, ScopeConfigInterface $scopeConfigInterface, array $data = []) {
        parent::__construct($context);
        $this->scopeConfig = $scopeConfigInterface;
    }
    public function getResult() {
        $enable = $this->scopeConfig->getValue('general/mod9/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if ($enable) {
            return $this->scopeConfig->getValue('general/mod9/text_to_display', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            
        }
        return "";
    }
}