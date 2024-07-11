<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Boxplosive\Connect\Model\Discount;

use Boxplosive\Connect\Api\Discount\DataInterface as BoxplosiveDiscountInterface;
use Boxplosive\Connect\Model\Discount\ResourceModel as BoxplosiveDiscountResource;
use Magento\Framework\Api\ExtensibleDataInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * Discount Data Model
 */
class Data extends AbstractModel implements ExtensibleDataInterface, BoxplosiveDiscountInterface
{

    /**
     * @var Json
     */
    private $json;

    /**
     * Data constructor.
     * @param Context $context
     * @param Registry $registry
     * @param Json $json
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Json $json,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->json = $json;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(BoxplosiveDiscountResource::class);
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
    public function setEntityId($entityId): BoxplosiveDiscountInterface
    {
        return $this->setData(self::ID, $entityId);
    }

    /**
     * @inheritDoc
     */
    public function getQuoteId(): int
    {
        return (int)$this->getData(self::QUOTE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setQuoteId(int $quoteId): BoxplosiveDiscountInterface
    {
        return $this->setData(self::QUOTE_ID, $quoteId);
    }

    /**
     * @inheritDoc
     */
    public function getDiscount(): array
    {
        return $this->getData(self::DISCOUNT)
            ? $this->json->unserialize($this->getData(self::DISCOUNT))
            : [];
    }

    /**
     * @inheritDoc
     */
    public function setDiscount(array $discount): BoxplosiveDiscountInterface
    {
        return $this->setData(self::DISCOUNT, $this->json->serialize($discount));
    }
}
