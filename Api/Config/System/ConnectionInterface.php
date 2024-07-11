<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Boxplosive\Connect\Api\Config\System;

/**
 * Connection group config repository interface
 * @api
 */
interface ConnectionInterface extends SettingsInterface
{

    public const XML_PATH_MODE = 'boxplosive_connect/api/mode';
    public const XML_PATH_TENANT_ID = 'boxplosive_connect/api/tenant_id';
    public const XML_PATH_SUBSCRIPTION_KEY = 'boxplosive_connect/api/subscription_key';

    /* Acceptance */
    public const XML_PATH_ACCEPTANCE_API_URL = 'boxplosive_connect/api/acceptance_api_url';
    public const XML_PATH_ACCEPTANCE_B2C_TENANT_NAME = 'boxplosive_connect/api/acceptance_b2c_tenant_name';
    public const XML_PATH_ACCEPTANCE_B2C_TENANT_ID = 'boxplosive_connect/api/acceptance_b2c_tenant_id';
    public const XML_PATH_ACCEPTANCE_CLIENT_ID = 'boxplosive_connect/api/acceptance_client_id';
    public const XML_PATH_ACCEPTANCE_CLIENT_SECRET = 'boxplosive_connect/api/acceptance_client_secret';

    /* Production */
    public const XML_PATH_PRODUCTION_API_URL = 'boxplosive_connect/api/production_api_url';
    public const XML_PATH_PRODUCTION_B2C_TENANT_NAME = 'boxplosive_connect/api/production_b2c_tenant_name';
    public const XML_PATH_PRODUCTION_B2C_TENANT_ID = 'boxplosive_connect/api/production_b2c_tenant_id';
    public const XML_PATH_PRODUCTION_CLIENT_ID = 'boxplosive_connect/api/production_client_id';
    public const XML_PATH_PRODUCTION_CLIENT_SECRET = 'boxplosive_connect/api/production_client_secret';

    /**
     * Get Tenant id
     *
     * @param int|null $storeId
     *
     * @return string
     */
    public function getTenantId(?int $storeId = null): string;

    /**
     * Flag for sandbox mode
     *
     * @param int|null $storeId
     *
     * @return bool
     */
    public function isAcceptanceMode(?int $storeId = null): bool;

    /**
     * Get associated array of credentials
     *
     * @param int|null  $storeId
     * @param bool|null $forceAcceptance
     *
     * @return array
     */
    public function getCredentials(?int $storeId = null, ?bool $forceAcceptance = null): array;

    /**
     * Get Subscription Key
     *
     * @param int|null $storeId
     *
     * @return string
     */
    public function getSubscriptionKey(?int $storeId = null): string;
}
