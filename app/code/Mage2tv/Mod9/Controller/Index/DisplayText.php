<?php

namespace Mage2tv\Mod9\Controller\Index;
use Magento\Framework\App\Action\Action;
use Magento\Framework\View\Result\PageFactory;


class DisplayText extends Action {
    public $resultPageFactory;
    protected $scopeConfig;

    public function __construct(\Magento\Framework\App\Action\Context $context, PageFactory $pageFactory) {
        parent::__construct($context);
        $this->resultPageFactory = $pageFactory;
        
    }
    public function execute()
    {
        return $this->resultPageFactory->create();
       
    }

}