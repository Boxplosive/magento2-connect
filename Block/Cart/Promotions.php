<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Boxplosive\Connect\Block\Cart;

use Boxplosive\Connect\Api\Config\RepositoryInterface as ConfigRepository;
use Boxplosive\Connect\Api\Customer\RepositoryInterface as BoxplosiveCustomerRepository;
use Boxplosive\Connect\Service\Discount\GetPromotions;
use Magento\Backend\Block\Template\Context;
use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Promotions
 */
class Promotions extends Template
{

    /**
     * @var ConfigRepository
     */
    private $configRepository;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var Session
     */
    private $session;
    /**
     * @var BoxplosiveCustomerRepository
     */
    private $boxplosiveCustomerRepository;
    /**
     * @var GetPromotions
     */
    private $getPromotions;

    /**
     * Promotions constructor.
     * @param Context $context
     * @param ConfigRepository $config
     * @param StoreManagerInterface $storeManager
     * @param Session $session
     * @param BoxplosiveCustomerRepository $boxplosiveCustomerRepository
     */
    public function __construct(
        Context $context,
        ConfigRepository $config,
        StoreManagerInterface $storeManager,
        Session $session,
        BoxplosiveCustomerRepository $boxplosiveCustomerRepository,
        GetPromotions $getPromotions
    ) {
        $this->configRepository = $config;
        $this->storeManager = $storeManager;
        $this->session = $session;
        $this->boxplosiveCustomerRepository = $boxplosiveCustomerRepository;
        $this->getPromotions = $getPromotions;
        parent::__construct($context);
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        try {
            $storeId = (int)$this->storeManager->getStore()->getId();
        } catch (\Exception $exception) {
            $storeId = null;
        }

        return $this->configRepository->isEnabled($storeId);
    }

    /**
     * @return bool
     */
    public function displayPromotions(): bool
    {
        try {
            $magentoCustomerId = $this->session->getQuote()->getCustomer()->getId();
            $this->boxplosiveCustomerRepository->getByCustomerId((int)$magentoCustomerId);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @return array
     */
    public function getPromotions(): array
    {
        return $this->getPromotions->execute();
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getFormUrl(): string
    {
        return $this->storeManager->getStore()->getBaseUrl() . 'boxplosive/coupon/apply';
    }

    /**
     * @return string
     */
    public function getCartNonLoyaltyText(): string
    {
        return $this->configRepository->getCartNonLoyaltyText();
    }

    /**
     * @return string
     */
    public function getCartLoyaltyText(): string
    {
        return $this->configRepository->getCartLoyaltyText();
    }
}
