<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Boxplosive\Connect\Model\WebApi\Distribution;

use Boxplosive\Connect\Api\Config\RepositoryInterface as ConfigRepository;
use Boxplosive\Connect\Api\WebApi\Distribution\RepositoryInterface as DistributionRepositoryInterface;
use Boxplosive\Connect\Service\Api\Adapter;
use Magento\Customer\Api\Data\CustomerInterface;

class Repository implements DistributionRepositoryInterface
{

    /**
     * @var ConfigRepository
     */
    private $configRepository;
    /**
     * @var Adapter
     */
    private $apiAdapter;

    /**
     * Repository constructor.
     * @param ConfigRepository $configRepository
     * @param Adapter $apiAdapter
     */
    public function __construct(
        ConfigRepository $configRepository,
        Adapter $apiAdapter
    ) {
        $this->configRepository = $configRepository;
        $this->apiAdapter = $apiAdapter;
    }

    /**
     * @inheritDoc
     */
    public function getLoyaltyBalances(CustomerInterface $customer): array
    {
        $entry = $this->formatUri(self::REQUEST_LOYALTY_BALANCES, $customer);
        return $this->apiAdapter->execute($entry, Adapter::METHOD_GET, [], (int)$customer->getStoreId());
    }

    /**
     * @param string $uri
     * @param CustomerInterface $customer
     * @return string
     */
    private function formatUri(string $uri, CustomerInterface $customer): string
    {
        $tenant = $this->configRepository->getTenantId((int)$customer->getStoreId());
        $customerId = 'mag-' . $customer->getId();
        return sprintf($uri, $tenant, $customerId);
    }

    /**
     * @inheritDoc
     */
    public function getLoyaltyPrograms(CustomerInterface $customer): array
    {
        $entry = $this->formatUri(self::REQUEST_LOYALTY_PROGRAMS, $customer);
        return $this->apiAdapter->execute($entry, Adapter::METHOD_GET, [], (int)$customer->getStoreId());
    }

    /**
     * @inheritDoc
     */
    public function getSavingGoals(CustomerInterface $customer): array
    {
        $entry = $this->formatUri(self::REQUEST_SAVING_GOALS, $customer);
        return $this->apiAdapter->execute($entry, Adapter::METHOD_GET, [], (int)$customer->getStoreId());
    }

    /**
     * @inheritDoc
     */
    public function getActiveCouponsAndReservations(CustomerInterface $customer): array
    {
        $entry = $this->formatUri(self::REQUEST_ACTIVE_COUPONS, $customer);
        return $this->apiAdapter->execute($entry, Adapter::METHOD_GET, [], (int)$customer->getStoreId());
    }

    /**
     * @inheritDoc
     */
    public function getCouponsByCode(CustomerInterface $customer, array $couponCodes): array
    {
        $entry = $this->formatUri(self::REQUEST_COUPONS_BY_CODE, $customer);
        return $this->apiAdapter->execute(
            $entry,
            Adapter::METHOD_POST,
            ['CouponCodes' => $couponCodes],
            (int)$customer->getStoreId()
        );
    }

    /**
     * @inheritDoc
     */
    public function claimCoupon(CustomerInterface $customer, string $couponCode): array
    {
        $entry = $this->formatUri(self::REQUEST_CLAIM_COUPON, $customer);
        return $this->apiAdapter->execute(
            $entry,
            Adapter::METHOD_POST,
            ['CouponCode' => $couponCode],
            (int)$customer->getStoreId()
        );
    }
}
