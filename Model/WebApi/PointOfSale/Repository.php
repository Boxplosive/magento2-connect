<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Boxplosive\Connect\Model\WebApi\PointOfSale;

use Boxplosive\Connect\Api\Config\RepositoryInterface as ConfigRepository;
use Boxplosive\Connect\Api\Customer\DataInterface;
use Boxplosive\Connect\Api\Customer\RepositoryInterface as BoxplosiveCustomerRepository;
use Boxplosive\Connect\Api\WebApi\BackOffice\RepositoryInterface as BackOfficeRepository;
use Boxplosive\Connect\Api\WebApi\PointOfSale\RepositoryInterface as PointOfSaleRepositoryInterface;
use Boxplosive\Connect\Service\Api\Adapter;
use Boxplosive\Connect\Api\Discount\RepositoryInterface as BoxplosiveDiscountRepository;
use Magento\Framework\Math\Random;
use Magento\Quote\Api\Data\CartInterface;
use DateTime;
use DateTimeZone;
use Magento\Store\Model\StoreManagerInterface;

/**
 * BackOffice Repository
 */
class Repository implements PointOfSaleRepositoryInterface
{

    /**
     * @var Adapter
     */
    private $apiAdapter;
    /**
     * @var ConfigRepository
     */
    private $configRepository;
    /**
     * @var Random
     */
    private $mathRandom;
    /**
     * @var BoxplosiveCustomerRepository
     */
    private $boxplosiveCustomerRepository;
    /**
     * @var BoxplosiveDiscountRepository
     */
    private $boxplosiveDiscountRepository;
    /**
     * @var BackOfficeRepository
     */
    private $backOfficeRepository;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * Repository constructor.
     *
     * @param Adapter $apiAdapter
     * @param ConfigRepository $configRepository
     * @param Random $mathRandom
     * @param BoxplosiveCustomerRepository $boxplosiveCustomerRepository
     * @param BoxplosiveDiscountRepository $boxplosiveDiscountRepository
     */
    public function __construct(
        Adapter $apiAdapter,
        ConfigRepository $configRepository,
        Random $mathRandom,
        BoxplosiveCustomerRepository $boxplosiveCustomerRepository,
        BoxplosiveDiscountRepository $boxplosiveDiscountRepository,
        BackOfficeRepository $backOfficeRepository,
        StoreManagerInterface $storeManager
    ) {
        $this->apiAdapter = $apiAdapter;
        $this->configRepository = $configRepository;
        $this->mathRandom = $mathRandom;
        $this->boxplosiveCustomerRepository = $boxplosiveCustomerRepository;
        $this->boxplosiveDiscountRepository = $boxplosiveDiscountRepository;
        $this->backOfficeRepository = $backOfficeRepository;
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritDoc
     */
    public function getSubtotal(CartInterface $quote): array
    {
        $storeId = (int)$quote->getStoreId();
        $data = [
            'StoreId' => $quote->getStoreId(),
            'PosId' => $quote->getStoreId(),
            'CashierId' => $quote->getId(),
            'InactivityTimeout' => null,
            'StampTypePreference' => 'DigitalOnly',
            'ForgotToBringLoyaltyCard' => false,
            'Transaction' => [
                'CreatedAt' => $this->getCurrentTimestamp(),
                'Id' => $this->mathRandom->getRandomNumber(1000000000, 9999999999),
                'CustomerExternalId' => $this->getCustomerExternalId($quote),
                'ApplyDiscounts' => $this->boxplosiveDiscountRepository->canApplyDiscount($quote),
                'Rows' => $this->getRows($quote),
                'Storename' => $this->storeManager->getStore($storeId)->getName()
            ]
        ];

        $entry = $this->formatUri(self::REQUEST_URI_GET_SUBTOTAL, $storeId);
        $apiResult = $this->apiAdapter->execute($entry, Adapter::METHOD_POST, $data, $storeId);
        if ($apiResult['success']) {
            $return = [
                'success' => true,
                'transaction_id' => $apiResult['result']['TransactionId'],
                'created_at' => $apiResult['result']['TransactionCreatedAt']
            ];
            if (isset($apiResult['result']['LoyaltyCards'])) {
                $return['is_registered'] = true;
            } else {
                $return['is_registered'] = false;
                if (!$quote->getCustomer()->getId()) {
                    $processGuestCustomer = $this->backOfficeRepository->processGuestCustomer();
                    if ($processGuestCustomer['success']) {
                        return $this->getSubtotal($quote);
                    }
                }
            }

            if (isset($apiResult['result']['Clearings']) && $apiResult['result']['Clearings']) {
                $resultDiscount = [];
                foreach ($apiResult['result']['Clearings'] as $clearing) {
                    if ($clearing['Discounts']) {
                        $value = 0;
                        foreach ($clearing['Discounts'] as $discount) {
                            $value += $discount['Value'];
                        }
                        $resultDiscount[] = [
                            'title' => $clearing['Messages'][0]['Message'],
                            'value' => (int)$value / -100,
                            'discount_code' => $clearing['CampaignExternalId']
                        ];
                    }
                }
                foreach ($apiResult['result']['LoyaltyPrograms'] as $loyaltyProgram) {
                    if (isset($loyaltyProgram['PointsDeltaCollect'])) {
                        $return['points'] = $loyaltyProgram['PointsDeltaCollect'];
                    }
                }
                $return['discount'] = $resultDiscount;
            }
            return $return;
        } else {
            return [
                'success' => false
            ];
        }
    }

    /**
     * @inheritDoc
     */
    public function getCustomerInfo(CartInterface $quote): array
    {
        $storeId = (int)$quote->getStoreId();
        $data = [
            'StoreId' => $quote->getStoreId(),
            'PosId' => $quote->getStoreId(),
            'CashierId' => $quote->getId(),
            'InteractionId' => rand(1000000000, 9999999999),
            'CustomerExternalId' => $this->getCustomerExternalId($quote),
            'LockType' => 'NoLock'
        ];

        $entry = $this->formatUri(self::REQUEST_URI_CUSTOMER_INFO, $storeId);
        $apiResult = $this->apiAdapter->execute($entry, Adapter::METHOD_POST, $data, $storeId);
        if ($apiResult['success']) {
            $result = [];
            if (isset($apiResult['result']['Coupons']) && $apiResult['result']['Coupons']) {
                foreach ($apiResult['result']['Coupons'] as $coupon) {
                    $result[] = [
                        'label' => $coupon['Description'],
                        'value' => $coupon['CampaignId'],
                        'status' => strtolower($coupon['Status'])
                    ];
                }
            }
            return [
                'success' => true,
                'result' => $result
            ];
        } else {
            return [
                'success' => false
            ];
        }
    }

    /**
     * @inheritDoc
     */
    public function cancel(CartInterface $quote, array $transaction): bool
    {
        $storeId = (int)$quote->getStoreId();
        $data = [
            'StoreId' => $quote->getStoreId(),
            'PosId' => $quote->getStoreId(),
            'CashierId' => $quote->getId(),
            "TransactionId" => $transaction['transaction_id'],
            "TransactionCreatedAt" => $transaction['created_at']
        ];
        $entry = $this->formatUri(self::REQUEST_URI_CANCEL, $storeId);
        $result = $this->apiAdapter->execute($entry, Adapter::METHOD_POST, $data, $storeId);
        return $result['success'];
    }

    /**
     * @inheritDoc
     */
    public function finish(CartInterface $quote, array $transaction): bool
    {
        $storeId = (int)$quote->getStoreId();
        $data = [
            'StoreId' => $quote->getStoreId(),
            'PosId' => $quote->getStoreId(),
            'CashierId' => $quote->getId(),
            "TransactionId" => $transaction['transaction_id'],
            "TransactionCreatedAt" => $transaction['created_at']
        ];
        $entry = $this->formatUri(self::REQUEST_URI_FINISH, $storeId);
        $result = $this->apiAdapter->execute($entry, Adapter::METHOD_POST, $data, $storeId);
        return $result['success'];
    }

    /**
     * @param string $uri
     * @param int $storeId
     * @return string
     */
    private function formatUri(string $uri, int $storeId = 0): string
    {
        $tenant = $this->configRepository->getTenantId($storeId);
        return sprintf($uri, $tenant);
    }

    /**
     * @return string
     */
    private function getCurrentTimestamp(): string
    {
        return (new DateTime())
            ->setTimezone(new DateTimeZone('UTC'))
            ->format('Y-m-d\TH:i:s\Z');
    }

    /**
     * @param CartInterface $quote
     * @return array
     */
    private function getRows(CartInterface $quote): array
    {
        $rows = [];
        foreach ($quote->getAllVisibleItems() as $item) {
            $product = $item->getProduct();
            $rows[] = [
                'Id' => $product->getId(),
                'Name' => $product->getName(),
                'ProductId' => $product->getSku(),
                'ProductValue' => $item->getPriceInclTax() * 100,
                'Amount' => $item->getQty(),
                'Barcode' => '',
                'IsDiscountable' => true,
                'IsProduct' => true,
                'IsReturnProduct' => false
            ];
        }
        return $rows;
    }

    /**
     * @param CartInterface $quote
     * @return string
     */
    private function getCustomerExternalId(CartInterface $quote): string
    {
        if (!$quote->getCustomer()->getId()) {
            return DataInterface::GUEST_CUSTOMER_ID;
        }

        $customerId = $quote->getCustomer()->getId();
        try {
            $boxplosiveCustomer = $this->boxplosiveCustomerRepository->getByCustomerId((int)$customerId);
            return 'mag-' . $boxplosiveCustomer->getCustomerId();
        } catch (\Exception $e) {
            return DataInterface::GUEST_CUSTOMER_ID;
        }
    }
}
