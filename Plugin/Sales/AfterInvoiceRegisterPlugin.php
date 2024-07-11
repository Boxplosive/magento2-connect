<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Boxplosive\Connect\Plugin\Sales;

use Boxplosive\Connect\Api\Log\RepositoryInterface as LogRepository;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Sales\Model\Order\Invoice;
use Boxplosive\Connect\Api\Config\RepositoryInterface as ConfigRepository;
use Boxplosive\Connect\Api\WebApi\PointOfSale\RepositoryInterface as PointOfSaleRepository;

/**
 * After Invoice Registration Plugin
 */
class AfterInvoiceRegisterPlugin
{

    /**
     * @var ConfigRepository
     */
    private $configRepository;
    /**
     * @var PointOfSaleRepository
     */
    private $pointOfSaleRepository;
    /**
     * @var CartRepositoryInterface
     */
    private $quoteRepository;
    /**
     * @var LogRepository
     */
    private $logRepository;

    /**
     * AfterInvoiceRegisterPlugin constructor.
     * @param ConfigRepository $configRepository
     * @param PointOfSaleRepository $pointOfSaleRepository
     * @param CartRepositoryInterface $quoteRepository
     * @param LogRepository $logRepository
     */
    public function __construct(
        ConfigRepository $configRepository,
        PointOfSaleRepository $pointOfSaleRepository,
        CartRepositoryInterface $quoteRepository,
        LogRepository $logRepository
    ) {
        $this->configRepository = $configRepository;
        $this->pointOfSaleRepository = $pointOfSaleRepository;
        $this->quoteRepository = $quoteRepository;
        $this->logRepository = $logRepository;
    }

    /**
     * @param Invoice $invoice
     * @param Invoice $resultInvoice
     * @return Invoice
     */
    public function afterRegister(
        Invoice $invoice,
        Invoice $resultInvoice
    ) {
        if ($this->configRepository->isEnabled((int)$invoice->getStoreId()) &&
            ($this->configRepository->getFinishTransaction((int)$invoice->getStoreId())) == 'invoice') {
            try {
                $quoteId = $invoice->getOrder()->getQuoteId();
                $quote = $this->quoteRepository->get((int)$quoteId);
                $result = $this->pointOfSaleRepository->getSubtotal($quote);
                if ($result['success']) {
                    $transaction = [
                        'transaction_id' => $result['transaction_id'],
                        'created_at' => $result['created_at']
                    ];
                    $this->pointOfSaleRepository->finish($quote, $transaction);
                }
            } catch (\Exception $exception) {
                $this->logRepository->addErrorLog('afterCreateAccount', $exception->getMessage());
            }
        }
        return $resultInvoice;
    }
}
