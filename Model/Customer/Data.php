<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Boxplosive\Connect\Model\Customer;

use Boxplosive\Connect\Api\Customer\DataInterface as BoxplosiveCustomerInterface;
use Boxplosive\Connect\Model\Customer\ResourceModel as BoxplosiveCustomerResource;
use Magento\Framework\Api\ExtensibleDataInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Customer Data Model
 */
class Data extends AbstractModel implements ExtensibleDataInterface, BoxplosiveCustomerInterface
{

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(BoxplosiveCustomerResource::class);
    }

    /**
     * @inheritDoc
     */
    public function getEntityId(): int
    {
        return (int)$this->getData(self::ID);
    }

    /**
     * @inheritDoc
     */
    public function setEntityId($entityId): BoxplosiveCustomerInterface
    {
        return $this->setData(self::ID, $entityId);
    }

    /**
     * @inheritDoc
     */
    public function getCustomerId(): int
    {
        return (int)$this->getData(self::CUSTOMER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setCustomerId(int $customerId): BoxplosiveCustomerInterface
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * @inheritDoc
     */
    public function getUpdatedAt(): string
    {
        return (string)$this->getData(self::UPDATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setUpdatedAt(string $updatedAt): BoxplosiveCustomerInterface
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }
}
