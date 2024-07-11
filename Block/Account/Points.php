<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Boxplosive\Connect\Block\Account;

use Boxplosive\Connect\Api\Config\RepositoryInterface as ConfigRepository;
use Boxplosive\Connect\Api\Customer\DataInterface as BoxplosiveCustomer;
use Boxplosive\Connect\Api\Customer\RepositoryInterface as BoxplosiveCustomerRepository;
use Boxplosive\Connect\Api\Log\RepositoryInterface as LogRepository;
use Boxplosive\Connect\Api\WebApi\Internal\RepositoryInterface as InternalRepository;
use Magento\Backend\Block\Template\Context;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Points
 */
class Points extends Template
{

    /**
     * @var null
     */
    private $boxplosiveCustomer = null;
    /**
     * @var ConfigRepository
     */
    private $configRepository;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var CustomerSession
     */
    private $customerSession;
    /**
     * @var BoxplosiveCustomerRepository
     */
    private $boxplosiveCustomerRepository;
    /**
     * @var InternalRepository
     */
    private $internalRepository;
    /**
     * @var LogRepository
     */
    private $logRepository;

    /**
     * Points constructor.
     * @param Context $context
     * @param ConfigRepository $config
     * @param StoreManagerInterface $storeManager
     * @param CustomerSession $customerSession
     * @param BoxplosiveCustomerRepository $boxplosiveCustomerRepository
     * @param InternalRepository $internalRepository
     * @param LogRepository $logRepository
     */
    public function __construct(
        Context $context,
        ConfigRepository $config,
        StoreManagerInterface $storeManager,
        CustomerSession $customerSession,
        BoxplosiveCustomerRepository $boxplosiveCustomerRepository,
        InternalRepository $internalRepository,
        LogRepository $logRepository
    ) {
        $this->configRepository = $config;
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
        $this->boxplosiveCustomerRepository = $boxplosiveCustomerRepository;
        $this->internalRepository = $internalRepository;
        $this->logRepository = $logRepository;
        parent::__construct($context);
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->configRepository->isEnabled();
    }

    /**
     * @return bool
     */
    public function isSubscribed(): bool
    {
        return (bool)$this->getBoxplosiveCustomer();
    }

    /**
     * @return BoxplosiveCustomer|null
     */
    public function getBoxplosiveCustomer(): ?BoxplosiveCustomer
    {
        if ($this->boxplosiveCustomer === null) {
            try {
                $magentoCustomerId = $this->customerSession->getCustomer()->getId();
                $this->boxplosiveCustomer = $this->boxplosiveCustomerRepository
                    ->getByCustomerId((int)$magentoCustomerId);
            } catch (\Exception $e) {
                $this->logRepository->addErrorLog('getBoxplosiveCustomer', $e->getMessage());
                return $this->boxplosiveCustomer;
            }
        }

        return $this->boxplosiveCustomer;
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getSubscribeUrl(): string
    {
        return $this->storeManager->getStore()->getBaseUrl() . 'boxplosive/customer/subscribe';
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getUnsubscribeUrl(): string
    {
        return $this->storeManager->getStore()->getBaseUrl() . 'boxplosive/customer/unsubscribe';
    }

    /**
     * @return number
     */
    public function getCustomerId()
    {
        return $this->customerSession->getCustomer()->getId();
    }

    /**
     * @return string
     */
    public function getOptInText(): string
    {
        return $this->configRepository->getOptInText();
    }

    /**
     * @return string
     */
    public function getOptOutText(): string
    {
        return $this->configRepository->getOptOutText();
    }
}
