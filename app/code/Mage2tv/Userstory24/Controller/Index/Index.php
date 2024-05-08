<?php
declare(strict_types=1);
namespace Mage2tv\Userstory24\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    /***
     *@var \Magento\Framework\View\Result\PageFactory
     */
    private PageFactory $pageFactory;
    public function __construct(\Magento\Framework\App\Action\Context $context, PageFactory $pageFactory)
    {
        parent::__construct($context);
        $this->pageFactory = $pageFactory;
    }
    public function execute()
    {
        $page = $this->pageFactory->create();
        $page->getConfig()->getTitle()->set("Console logged the store information");
        return $page;
    }
}
