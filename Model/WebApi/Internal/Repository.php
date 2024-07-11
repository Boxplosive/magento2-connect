<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Boxplosive\Connect\Model\WebApi\Internal;

use Boxplosive\Connect\Api\Discount\RepositoryInterface as BoxplosiveDiscountRepository;
use Boxplosive\Connect\Api\WebApi\Internal\RepositoryInterface as InternalRepositoryInterface;
use Boxplosive\Connect\Api\WebApi\PointOfSale\RepositoryInterface as PointOfSaleRepository;
use Boxplosive\Connect\Api\WebApi\Distribution\RepositoryInterface as DistributionRepository;
use Boxplosive\Connect\Api\Config\RepositoryInterface as ConfigRepository;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Framework\Webapi\Rest\Request;
use Boxplosive\Connect\Service\Discount\Create as CreateDiscount;
use Boxplosive\Connect\Service\Discount\Apply as ApplyDiscount;

/**
 * Class Repository
 */
class Repository implements InternalRepositoryInterface
{

    /**
     * @var CartRepositoryInterface
     */
    private $quoteRepository;
    /**
     * @var PointOfSaleRepository
     */
    private $pointOfSaleRepository;
    /**
     * @var DistributionRepository
     */
    private $distributionRepository;
    /**
     * @var ConfigRepository
     */
    private $configRepository;
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;
    /**
     * @var QuoteIdMaskFactory
     */
    private $quoteIdMaskFactory;
    /**
     * @var Request
     */
    private $request;
    /**
     * @var BoxplosiveDiscountRepository
     */
    private $boxplosiveDiscountRepository;

    /**
     * PointOfSale constructor.
     * @param CartRepositoryInterface $quoteRepository
     * @param PointOfSaleRepository $pointOfSaleRepository
     * @param ConfigRepository $configRepository
     * @param CustomerRepositoryInterface $customerRepository
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     * @param DistributionRepository $distributionRepository
     * @param Request $request
     */
    public function __construct(
        CartRepositoryInterface $quoteRepository,
        PointOfSaleRepository $pointOfSaleRepository,
        ConfigRepository $configRepository,
        CustomerRepositoryInterface $customerRepository,
        QuoteIdMaskFactory $quoteIdMaskFactory,
        DistributionRepository $distributionRepository,
        Request $request,
        BoxplosiveDiscountRepository $boxplosiveDiscountRepository
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->pointOfSaleRepository = $pointOfSaleRepository;
        $this->configRepository = $configRepository;
        $this->customerRepository = $customerRepository;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->distributionRepository = $distributionRepository;
        $this->request = $request;
        $this->boxplosiveDiscountRepository = $boxplosiveDiscountRepository;
    }

    /**
     * @inheritDoc
     */
    public function getSubtotal(string $quoteId): array
    {
        $quote = $this->quoteRepository->get((int)$quoteId);
        $result = $this->pointOfSaleRepository->getSubtotal($quote);
        if ($result['success']) {
            $transaction = [
                'transaction_id' => $result['transaction_id'],
                'created_at' => $result['created_at']
            ];
            $this->pointOfSaleRepository->cancel($quote, $transaction);
        }

        return [$result];
    }

    /**
     * @inheritDoc
     */
    public function getGuestSubtotal(string $quoteId): array
    {
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($quoteId, 'masked_id');
        $quote = $this->quoteRepository->get((int)$quoteIdMask->getQuoteId());
        $result = $this->pointOfSaleRepository->getSubtotal($quote);
        if ($result['success']) {
            $transaction = [
                'transaction_id' => $result['transaction_id'],
                'created_at' => $result['created_at']
            ];
            $this->pointOfSaleRepository->cancel($quote, $transaction);
        }

        return [$result];
    }

    /**
     * @inheritDoc
     */
    public function subscribe(string $quoteId, string $value): bool
    {
        $quote = $this->quoteRepository->get((int)$quoteId);
        $quote->setData('boxplosive_subscribe', (int)$value);
        $this->quoteRepository->save($quote);
        return true;
    }

    /**
     * @inheritDoc
     */
    public function applyDiscount(string $quoteId): array
    {
        $params = $this->request->getBodyParams();
        //unset all params except reserved
        foreach ($params as $key => $param) {
            if (!str_contains($key, 'reserved')) {
                unset($params[$key]);
            }
        }
        if (!$params) {
            return [true];
        }

        try {
            $quote = $this->quoteRepository->getActive($quoteId);
            if (!$this->boxplosiveDiscountRepository->canApplyDiscount($quote)) {
                return [false];
            }
            $customer = $quote->getCustomer();
            foreach ($params as $code) {
                $result = $this->distributionRepository->claimCoupon($customer, $code);
                if ($result['success']) {
                    $quote->collectTotals();
                }
            }
            return [true];
        } catch (\Exception $e) {
            return [false];
        }
    }

    /**
     * @inheritDoc
     */
    public function getBalance(int $customerId): array
    {
        try {
            $customer = $this->customerRepository->getById($customerId);
            $programs = $this->distributionRepository->getLoyaltyPrograms($customer);
            if ($programs['success']) {
                $programsMap = [];
                foreach ($programs['result'] as $program) {
                    $programsMap[$program['Id']] = $program['Name'];
                }
            } else {
                return [];
            }
            $balances = $this->distributionRepository->getLoyaltyBalances($customer);
            if ($balances['success']) {
                $result = [];
                foreach ($balances['result'] as $balance) {
                    $result[] = [
                        'name' => $programsMap[$balance['LoyaltyPointsProgramId']],
                        'points' => $balance['Amount']
                    ];
                }
                return $result;
            } else {
                return [];
            }
        } catch (\Exception $e) {
            return [];
        }
    }
}
