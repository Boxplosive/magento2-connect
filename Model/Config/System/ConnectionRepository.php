<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Boxplosive\Connect\Model\Config\System;

use Boxplosive\Connect\Api\Config\System\ConnectionInterface;
use Boxplosive\Connect\Model\Config\Source\Mode;

/**
 * Credentials provider class
 */
class ConnectionRepository extends SettingsRepository implements ConnectionInterface
{

    /**
     * @inheritDoc
     */
    public function getCredentials(?int $storeId = null, ?bool $forceAcceptance = null): array
    {
        $isAcceptanceMode = $forceAcceptance === null
            ? $this->isAcceptanceMode($storeId)
            : $forceAcceptance;

        return [
            'mode' => $isAcceptanceMode ? 'acceptance' : 'production',
            'api_url' => $this->getApiUrl($storeId, $isAcceptanceMode),
            'tenant_id' => $this->getTenantId($storeId),
            'client_id' => $this->getClientId($storeId, $isAcceptanceMode),
            'client_secret' => $this->getClientSecret($storeId, $isAcceptanceMode),
            'b2c_tenant_name' => $this->getB2cTenantName($storeId, $isAcceptanceMode),
            'b2c_tenant_id' => $this->getB2cTenantId($storeId, $isAcceptanceMode)
        ];
    }

    /**
     * @inheritDoc
     */
    public function isAcceptanceMode(?int $storeId = null): bool
    {
        return $this->getStoreValue(self::XML_PATH_MODE, $storeId) == Mode::ACCEPTANCE;
    }

    /**
     * @param int|null $storeId
     * @param false $isAcceptanceMode
     *
     * @return string
     */
    private function getApiUrl(?int $storeId = null, bool $isAcceptanceMode = false): string
    {
        $path = $isAcceptanceMode
            ? self::XML_PATH_ACCEPTANCE_API_URL
            : self::XML_PATH_PRODUCTION_API_URL;

        return (string)$this->getStoreValue($path, $storeId);
    }

    /**
     * @inheritDoc
     */
    public function getTenantId(?int $storeId = null): string
    {
        return (string)$this->getStoreValue(self::XML_PATH_TENANT_ID, $storeId);
    }

    /**
     * @param int|null $storeId
     * @param false $isAcceptanceMode
     *
     * @return string
     */
    private function getClientId(?int $storeId = null, bool $isAcceptanceMode = false): string
    {
        $path = $isAcceptanceMode
            ? self::XML_PATH_ACCEPTANCE_CLIENT_ID
            : self::XML_PATH_PRODUCTION_CLIENT_ID;

        return (string)$this->getStoreValue($path, $storeId);
    }

    /**
     * @param int|null $storeId
     * @param bool $isAcceptanceMode
     *
     * @return string
     */
    private function getClientSecret(?int $storeId = null, bool $isAcceptanceMode = false): string
    {
        $path = $isAcceptanceMode
            ? self::XML_PATH_ACCEPTANCE_CLIENT_SECRET
            : self::XML_PATH_PRODUCTION_CLIENT_SECRET;

        if ($value = $this->getStoreValue($path, $storeId)) {
            return $this->encryptor->decrypt($value);
        }

        return '';
    }

    /**
     * @param int|null $storeId
     * @param false $isAcceptanceMode
     *
     * @return string
     */
    private function getB2cTenantName(?int $storeId = null, bool $isAcceptanceMode = false): string
    {
        $path = $isAcceptanceMode
            ? self::XML_PATH_ACCEPTANCE_B2C_TENANT_NAME
            : self::XML_PATH_PRODUCTION_B2C_TENANT_NAME;

        return (string)$this->getStoreValue($path, $storeId);
    }

    /**
     * @param int|null $storeId
     * @param false $isAcceptanceMode
     *
     * @return string
     */
    private function getB2cTenantId(?int $storeId = null, bool $isAcceptanceMode = false): string
    {
        $path = $isAcceptanceMode
            ? self::XML_PATH_ACCEPTANCE_B2C_TENANT_ID
            : self::XML_PATH_PRODUCTION_B2C_TENANT_ID;

        return (string)$this->getStoreValue($path, $storeId);
    }

    /**
     * @inheritDoc
     */
    public function getSubscriptionKey(?int $storeId = null): string
    {
        return (string)$this->getStoreValue(self::XML_PATH_SUBSCRIPTION_KEY, $storeId);
    }
}
