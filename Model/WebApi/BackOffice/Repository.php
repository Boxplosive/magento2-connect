<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Boxplosive\Connect\Model\WebApi\BackOffice;

use Boxplosive\Connect\Api\Config\RepositoryInterface as ConfigRepository;
use Boxplosive\Connect\Api\Customer\DataInterface;
use Boxplosive\Connect\Api\WebApi\BackOffice\RepositoryInterface as BackOfficeRepositoryInterface;
use Boxplosive\Connect\Service\Api\Adapter;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * BackOffice Repository
 */
class Repository implements BackOfficeRepositoryInterface
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
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * Repository constructor.
     * @param Adapter $apiAdapter
     * @param ConfigRepository $configRepository
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Adapter $apiAdapter,
        ConfigRepository $configRepository,
        StoreManagerInterface $storeManager
    ) {
        $this->apiAdapter = $apiAdapter;
        $this->configRepository = $configRepository;
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritDoc
     */
    public function processCustomer(CustomerInterface $customer, bool $remove = false): array
    {
        $storeId = (int)$customer->getStoreId();

        $data = [
            'customerID' => 'mag-' . $customer->getId(),
            'firstName' => $customer->getFirstname(),
            'lastName' => $customer->getLastname(),
            'email' => $customer->getEmail(),
            'remove' => $remove,
            'invitationCode' => '',
            'storename' => $this->storeManager->getStore($storeId)->getName()
        ];

        $entry = $this->formatUri(self::REQUEST_URI_PROCESS_CUSTOMER, $storeId);
        return $this->apiAdapter->execute($entry, Adapter::METHOD_POST, $data, $storeId);
    }

    public function processGuestCustomer(): array
    {
        $storeId = (int)$this->storeManager->getDefaultStoreView()->getId();

        $data = [
            'customerID' => DataInterface::GUEST_CUSTOMER_ID,
            'firstName' => 'Guest',
            'lastName' => 'Guest',
            'email' => 'guest@example.com',
            'remove' => false,
            'invitationCode' => '',
            'storename' => $this->storeManager->getStore($storeId)->getName()
        ];
        $entry = $this->formatUri(self::REQUEST_URI_PROCESS_CUSTOMER, $storeId);
        return $this->apiAdapter->execute($entry, Adapter::METHOD_POST, $data, $storeId);
    }

    /**
     * @param string $uri
     * @param int $storeId
     * @return string
     */
    private function formatUri(string $uri, int $storeId): string
    {
        $tenant = $this->configRepository->getTenantId($storeId);
        return sprintf($uri, $tenant);
    }
}
