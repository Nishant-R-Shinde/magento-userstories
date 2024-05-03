<?php
namespace Mage2tv\Mod9\Controller\Adminhtml\Index;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
class DisplayOption extends \Magento\Backend\App\Action {
    protected $pageFactory;
    public function __construct(\Magento\Backend\App\Action\Context $context, PageFactory $pageFactory) {
        parent::__construct($context);
        $this->pageFactory = $pageFactory;
        
    }
    public function execute() {
        return $this->pageFactory->create();
    }
}