<?php

namespace Boxplosive\Connect\Model;

use Boxplosive\Connect\Service\Discount\GetPromotions;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Checkout\Model\Session;
use Boxplosive\Connect\Api\Config\RepositoryInterface as ConfigRepository;
use Boxplosive\Connect\Api\Customer\RepositoryInterface as BoxplosiveCustomerRepository;

/**
 * Class CheckoutConfigVars
 */
class CheckoutConfigVars implements ConfigProviderInterface
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
    private $checkoutSession;
    /**
     * @var BoxplosiveCustomerRepository
     */
    private $boxplosiveCustomerRepository;
    /**
     * @var GetPromotions
     */
    private $getPromotions;

    /**
     * CheckoutConfigVars constructor.
     * @param ConfigRepository $config
     * @param StoreManagerInterface $storeManager
     * @param Session $checkoutSession
     * @param BoxplosiveCustomerRepository $boxplosiveCustomerRepository
     * @param GetPromotions $getPromotions
     */
    public function __construct(
        ConfigRepository $config,
        StoreManagerInterface $storeManager,
        Session $checkoutSession,
        BoxplosiveCustomerRepository $boxplosiveCustomerRepository,
        GetPromotions $getPromotions
    ) {
        $this->configRepository = $config;
        $this->storeManager = $storeManager;
        $this->checkoutSession = $checkoutSession;
        $this->boxplosiveCustomerRepository = $boxplosiveCustomerRepository;
        $this->getPromotions = $getPromotions;
    }

    /**
     * @return array
     * @throws NoSuchEntityException
     */
    public function getConfig(): array
    {
        $storeId = (int)$this->storeManager->getStore()->getId();
        $additionalVariables['boxplosiveConfig'] = [
            'isEnabled' => $this->configRepository->isEnabled($storeId),
            'nonLoyaltyText' => $this->configRepository->getCheckoutNonLoyaltyText(),
            'loyaltyText' => $this->configRepository->getCheckoutLoyaltyText(),
            'optInText' => $this->configRepository->getOptInText(),
            'isSubscribed' => $this->getSubscriptionState(),
            'promotions' => $this->getPromotions->execute(),
            'formUrl' => $this->getFormUrl()
        ];

        return $additionalVariables;
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    private function getSubscriptionState(): string
    {
        $customerId = $this->checkoutSession->getQuote()->getCustomerId();
        if ($customerId) {
            try {
                $this->boxplosiveCustomerRepository->getByCustomerId((int)$customerId);
                return 'subscribed';
            } catch (\Exception $e) {
                return 'not-subscribed';
            }
        } else {
            return 'guest';
        }
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getFormUrl(): string
    {
        return $this->storeManager->getStore()->getBaseUrl() . 'boxplosive/coupon/apply';
    }
}
