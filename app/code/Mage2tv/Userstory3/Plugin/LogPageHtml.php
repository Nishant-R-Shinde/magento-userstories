<?php
namespace Mage2tv\Userstory3\Plugin;
use Magento\Catalog\Controller\Product\View;
use Psr\Log\LoggerInterface;
class LogPageHtml {
    private $logger;
    public function __construct(LoggerInterface $logger) {
        $this->logger = $logger;
    }
    public function afterExecute(View $subject, $result) {
        $html = $result->getLayout()->getOutput();
        $this->logger->info("The page html is : ". $html);
        return $result;
    }
}