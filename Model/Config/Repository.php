<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Boxplosive\Connect\Model\Config;

use Magento\Store\Model\ScopeInterface;
use Boxplosive\Connect\Api\Config\RepositoryInterface as ConfigRepositoryInterface;

/**
 * Config repository class
 */
class Repository extends System\ConnectionRepository implements ConfigRepositoryInterface
{

    /**
     * @inheritDoc
     */
    public function getExtensionVersion(): string
    {
        return $this->getStoreValue(self::XML_PATH_EXTENSION_VERSION);
    }

    /**
     * @inheritDoc
     */
    public function isDebugMode(int $storeId = null): bool
    {
        return $this->isSetFlag(
            self::XML_PATH_DEBUG,
            $storeId,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @inheritDoc
     */
    public function isEnabled(int $storeId = null): bool
    {
        return $this->isSetFlag(self::XML_PATH_EXTENSION_ENABLE, $storeId);
    }

    /**
     * Support link for extension.
     *
     * @return string
     */
    public function getSupportLink(): string
    {
        return sprintf(
            self::MODULE_SUPPORT_LINK,
            $this->getExtensionCode()
        );
    }

    /**
     * @inheritDoc
     */
    public function getExtensionCode(): string
    {
        return self::EXTENSION_CODE;
    }

    /**
     * @inheritDoc
     */
    public function getOptInText(): string
    {
        return $this->getStoreValue(self::XML_PATH_TEXTUAL_OPT_IN);
    }

    /**
     * @inheritDoc
     */
    public function getOptOutText(): string
    {
        return $this->getStoreValue(self::XML_PATH_TEXTUAL_OPT_OUT);
    }

    /**
     * @inheritDoc
     */
    public function getCartNonLoyaltyText(): string
    {
        return $this->getStoreValue(self::XML_PATH_TEXTUAL_CART_NON_LOYALTY);
    }

    /**
     * @inheritDoc
     */
    public function getCartLoyaltyText(): string
    {
        return $this->getStoreValue(self::XML_PATH_TEXTUAL_CART_LOYALTY);
    }

    /**
     * @inheritDoc
     */
    public function getCheckoutNonLoyaltyText(): string
    {
        return $this->getStoreValue(self::XML_PATH_TEXTUAL_CHECKOUT_NON_LOYALTY);
    }

    /**
     * @inheritDoc
     */
    public function getCheckoutLoyaltyText(): string
    {
        return $this->getStoreValue(self::XML_PATH_TEXTUAL_CHECKOUT_LOYALTY);
    }

    /**
     * @inheritDoc
     */
    public function getMagentoVersion(): string
    {
        return $this->metadata->getVersion();
    }
}
