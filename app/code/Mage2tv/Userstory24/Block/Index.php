<?php
declare(strict_types=1);
namespace Mage2tv\Userstory24\Block;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Payment\Model\Config;

class Index extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Payment\Model\Config
     */
    protected Config $payConfig;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected ScopeConfigInterface $scopeConfig;
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        Config $payConfig,
        ScopeConfigInterface $scopeConfigInterface,
        array $data = []
    ) {
        $this->payConfig = $payConfig;
        $this->scopeConfig = $scopeConfigInterface;
        parent::__construct($context, $data);
    }
    public function getSalesEmail(): string
    {
        return $this->scopeConfig->getValue(
            'trans_email/ident_sales/email',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function getPaymentMethods(): array
    {
        try {
            $methods = [];
            foreach($this->payConfig->getActiveMethods() as $methodCode => $method) {
                $methods[] = $method->getTitle();
            }
            return $methods;
        } catch(\Exception $e) {
            return [];
        }
    }
}
