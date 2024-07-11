<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Boxplosive\Connect\Plugin\Quote;

use Boxplosive\Connect\Api\Config\RepositoryInterface as ConfigRepository;
use Boxplosive\Connect\Api\Customer\DataInterface as BoxplosiveCustomer;
use Boxplosive\Connect\Api\Customer\RepositoryInterface as BoxplosiveCustomerRepository;
use Boxplosive\Connect\Api\Log\RepositoryInterface as LogRepository;
use Boxplosive\Connect\Api\WebApi\BackOffice\RepositoryInterface as BackOfficeRepository;
use Boxplosive\Connect\Api\WebApi\PointOfSale\RepositoryInterface as PointOfSaleRepository;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\QuoteManagement;

/**
 * After Invoice Registration Plugin
 */
class AfterPlaceOrderPlugin
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
     * @var BoxplosiveCustomerRepository
     */
    private $boxplosiveCustomerRepository;
    /**
     * @var BackOfficeRepository
     */
    private $backOfficeRepository;
    /**
     * @var LogRepository
     */
    private $logRepository;

    /**
     * AfterPlaceOrderPlugin constructor.
     * @param ConfigRepository $configRepository
     * @param PointOfSaleRepository $pointOfSaleRepository
     * @param CartRepositoryInterface $quoteRepository
     * @param BoxplosiveCustomerRepository $boxplosiveCustomerRepository
     * @param BackOfficeRepository $backOfficeRepository
     */
    public function __construct(
        ConfigRepository $configRepository,
        PointOfSaleRepository $pointOfSaleRepository,
        CartRepositoryInterface $quoteRepository,
        BoxplosiveCustomerRepository $boxplosiveCustomerRepository,
        BackOfficeRepository $backOfficeRepository,
        LogRepository $logRepository
    ) {
        $this->configRepository = $configRepository;
        $this->pointOfSaleRepository = $pointOfSaleRepository;
        $this->quoteRepository = $quoteRepository;
        $this->boxplosiveCustomerRepository = $boxplosiveCustomerRepository;
        $this->backOfficeRepository = $backOfficeRepository;
        $this->logRepository = $logRepository;
    }

    /**
     * @param QuoteManagement $subject
     * @param $orderId
     * @param $quoteId
     * @return mixed
     * @throws NoSuchEntityException|LocalizedException
     */
    public function afterPlaceOrder(
        QuoteManagement $subject,
        $orderId,
        $quoteId
    ) {
        try {
            $quote = $this->quoteRepository->get((int)$quoteId);
            if ($this->configRepository->isEnabled((int)$quote->getStoreId()) && $quote->getCustomerId()) {
                if ($quote->getData('boxplosive_subscribe')) {
                    $this->subscribeCustomer($quote->getCustomer());
                }
                if ($this->configRepository->getFinishTransaction((int)$quote->getStoreId()) == 'order') {
                    try {
                        $boxplosiveCustomer = $this->boxplosiveCustomerRepository->getByCustomerId(
                            (int)$quote->getCustomerId()
                        );
                        $this->updateBoxplosiveBalance($boxplosiveCustomer, $quote);
                    } catch (\Exception $e) {
                        return $orderId;
                    }
                }
            }
        } catch (\Exception $exception) {
            $this->logRepository->addErrorLog('afterCreateAccount', $exception->getMessage());
        }

        return $orderId;
    }

    /**
     * Register customer to API and create boxplosive_customer entity in magento
     *
     * @param CustomerInterface $customer
     * @throws LocalizedException
     */
    private function subscribeCustomer(CustomerInterface $customer)
    {
        $processCustomer = $this->backOfficeRepository->processCustomer($customer);
        if ($processCustomer['success']) {
            $boxplosiveCustomer = $this->boxplosiveCustomerRepository->create();
            $boxplosiveCustomer->setCustomerId((int)$customer->getId());

            $this->boxplosiveCustomerRepository->save($boxplosiveCustomer);
        }
    }

    /**
     * @param BoxplosiveCustomer $boxplosiveCustomer
     * @param CartInterface $quote
     */
    private function updateBoxplosiveBalance(BoxplosiveCustomer $boxplosiveCustomer, CartInterface $quote)
    {
        //create and finish transaction
        $result = $this->pointOfSaleRepository->getSubtotal($quote);
        if ($result['success']) {
            $transaction = [
                'transaction_id' => $result['transaction_id'],
                'created_at' => $result['created_at']
            ];
            $this->pointOfSaleRepository->finish($quote, $transaction);
        }
    }
}
