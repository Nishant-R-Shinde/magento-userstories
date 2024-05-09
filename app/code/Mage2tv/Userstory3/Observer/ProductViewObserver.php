<?php
namespace Mage2tv\Userstory3\Observer;

use Magento\Framework\Event\ObserverInterface;

class ProductViewObserver implements ObserverInterface
{
    private $logger;
    public function __construct(\Psr\Log\LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();
        if($product) {
            $productName = $product->getName();
            $this->logger->info("Product Name : " . $productName);
        }
    }
}
