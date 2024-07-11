<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Boxplosive\Connect\Api\Config;

/**
 * Config Repository Interface
 * @api
 */
interface RepositoryInterface extends System\ConnectionInterface
{

    public const EXTENSION_CODE = 'Boxplosive_Connect';
    public const XML_PATH_EXTENSION_VERSION = 'boxplosive_connect/general/version';
    public const XML_PATH_EXTENSION_ENABLE = 'boxplosive_connect/general/enable';
    public const XML_PATH_DEBUG = 'boxplosive_connect/general/debug';
    public const MODULE_SUPPORT_LINK = 'https://www.magmodules.eu/help/%s';

    public const XML_PATH_TEXTUAL_OPT_IN = 'boxplosive_connect/textual/opt_in_text';
    public const XML_PATH_TEXTUAL_OPT_OUT = 'boxplosive_connect/textual/opt_out_text';
    public const XML_PATH_TEXTUAL_CART_NON_LOYALTY = 'boxplosive_connect/textual/cart_non_loyalty_text';
    public const XML_PATH_TEXTUAL_CART_LOYALTY = 'boxplosive_connect/textual/cart_loyalty_text';
    public const XML_PATH_TEXTUAL_CHECKOUT_NON_LOYALTY = 'boxplosive_connect/textual/checkout_non_loyalty_text';
    public const XML_PATH_TEXTUAL_CHECKOUT_LOYALTY = 'boxplosive_connect/textual/checkout_loyalty_text';
    
    /**
     * Get extension version
     *
     * @return string
     */
    public function getExtensionVersion(): string;

    /**
     * Get extension code
     *
     * @return string
     */
    public function getExtensionCode(): string;

    /**
     * Get Magento Version
     *
     * @return string
     */
    public function getMagentoVersion(): string;

    /**
     * Check if module is enabled
     *
     * @param int|null $storeId
     *
     * @return bool
     */
    public function isEnabled(int $storeId = null): bool;

    /**
     * Check if debug mode is enabled
     *
     * @param int|null $storeId
     *
     * @return bool
     */
    public function isDebugMode(int $storeId = null): bool;

    /**
     * Support link for extension.
     *
     * @return string
     */
    public function getSupportLink(): string;

    /**
     * Opt-in Checkbox text
     *
     * @return string
     */
    public function getOptInText(): string;

    /**
     * Opt-out Checkbox text
     *
     * @return string
     */
    public function getOptOutText(): string;

    /**
     * Cart text for Non-Loyalty clients
     *
     * @return string
     */
    public function getCartNonLoyaltyText(): string;

    /**
     * Cart text for Loyalty clients
     *
     * @return string
     */
    public function getCartLoyaltyText(): string;

    /**
     * Checkout text for Non-Loyalty clients
     *
     * @return string
     */
    public function getCheckoutNonLoyaltyText(): string;

    /**
     * Checkout text for Loyalty clients
     *
     * @return string
     */
    public function getCheckoutLoyaltyText(): string;
}
