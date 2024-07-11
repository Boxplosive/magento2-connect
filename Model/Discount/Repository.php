<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Boxplosive\Connect\Model\Discount;

use Boxplosive\Connect\Api\Config\RepositoryInterface as ConfigRepository;
use Boxplosive\Connect\Api\Discount\DataInterface as BoxplosiveDiscountInterface;
use Boxplosive\Connect\Api\Discount\RepositoryInterface as BoxplosiveDiscountRepository;
use Boxplosive\Connect\Model\Discount\DataFactory as BoxplosiveDiscountInterfaceFactory;
use Boxplosive\Connect\Model\Discount\ResourceModel as BoxplosiveDiscountResource;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Quote\Api\Data\CartInterface;

class Repository implements BoxplosiveDiscountRepository
{

    /**
     * @var BoxplosiveDiscountResource
     */
    private $boxplosiveDiscountResource;
    /**
     * @var BoxplosiveDiscountInterfaceFactory
     */
    private $boxplosiveDiscountInterfaceFactory;
    /**
     * @var ConfigRepository
     */
    private $configRepository;

    /**
     * Repository constructor.
     * @param ResourceModel $boxplosiveDiscountResource
     * @param DataFactory $boxplosiveDiscountInterfaceFactory
     * @param ConfigRepository $configRepository
     */
    public function __construct(
        BoxplosiveDiscountResource $boxplosiveDiscountResource,
        BoxplosiveDiscountInterfaceFactory $boxplosiveDiscountInterfaceFactory,
        ConfigRepository $configRepository
    ) {
        $this->boxplosiveDiscountResource = $boxplosiveDiscountResource;
        $this->boxplosiveDiscountInterfaceFactory = $boxplosiveDiscountInterfaceFactory;
        $this->configRepository = $configRepository;
    }

    /**
     * @inheritDoc
     */
    public function create(): BoxplosiveDiscountInterface
    {
        return $this->boxplosiveDiscountInterfaceFactory->create();
    }

    /**
     * @inheritDoc
     */
    public function delete(BoxplosiveDiscountInterface $discount): bool
    {
        try {
            $this->boxplosiveDiscountResource->delete($discount);
        } catch (\Exception $exception) {
            $exceptionMsg = self::COULD_NOT_DELETE_EXCEPTION;
            throw new CouldNotDeleteException(__(
                $exceptionMsg,
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getByQuoteId(int $quoteId): BoxplosiveDiscountInterface
    {
        if (!$quoteId) {
            $exceptionMsg = self::INPUT_EXCEPTION;
            throw new InputException(__($exceptionMsg));
        }

        if ($this->boxplosiveDiscountResource->isQuoteIdExists($quoteId)) {
            return $this->boxplosiveDiscountInterfaceFactory->create()
                ->load($quoteId, 'quote_id');
        } else {
            return $this->create()->setQuoteId($quoteId);
        }
    }

    /**
     * @inheritDoc
     */
    public function save(BoxplosiveDiscountInterface $discount): BoxplosiveDiscountInterface
    {
        try {
            $this->boxplosiveDiscountResource->save($discount);
        } catch (\Exception $exception) {
            $exceptionMsg = self::COULD_NOT_SAVE_EXCEPTION;
            throw new CouldNotSaveException(__(
                $exceptionMsg,
                $exception->getMessage()
            ));
        }

        return $discount;
    }

    /**
     * @inheritDoc
     */
    public function canApplyDiscount(CartInterface $quote): bool
    {
        if (!$quote->getCustomer()->getId()) {
            return false;
        }
        return $this->configRepository->useMultipleDiscount((int)$quote->getStoreId()) || !$quote->getCouponCode();
    }
}
