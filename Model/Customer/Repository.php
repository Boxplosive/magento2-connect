<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Boxplosive\Connect\Model\Customer;

use Boxplosive\Connect\Api\Customer\DataInterface as BoxplosiveCustomerInterface;
use Boxplosive\Connect\Api\Customer\RepositoryInterface as BoxplosiveCustomerRepository;
use Boxplosive\Connect\Model\Customer\DataFactory as BoxplosiveCustomerInterfaceFactory;
use Boxplosive\Connect\Model\Customer\ResourceModel as BoxplosiveCustomerResource;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;

class Repository implements BoxplosiveCustomerRepository
{

    /**
     * @var BoxplosiveCustomerResource
     */
    private $boxplosiveCustomerResource;
    /**
     * @var BoxplosiveCustomerInterfaceFactory
     */
    private $boxplosiveCustomerInterfaceFactory;

    /**
     * Repository constructor.
     * @param ResourceModel $boxplosiveCustomerResource
     * @param DataFactory $boxplosiveCustomerInterfaceFactory
     */
    public function __construct(
        BoxplosiveCustomerResource $boxplosiveCustomerResource,
        BoxplosiveCustomerInterfaceFactory $boxplosiveCustomerInterfaceFactory
    ) {
        $this->boxplosiveCustomerResource = $boxplosiveCustomerResource;
        $this->boxplosiveCustomerInterfaceFactory = $boxplosiveCustomerInterfaceFactory;
    }

    /**
     * @inheritDoc
     */
    public function get(int $id): BoxplosiveCustomerInterface
    {
        if (!$id) {
            $exceptionMsg = self::INPUT_EXCEPTION;
            throw new InputException(__($exceptionMsg));
        } elseif (!$this->boxplosiveCustomerResource->isExists($id)) {
            $exceptionMsg = self::NO_SUCH_ENTITY_EXCEPTION;
            throw new NoSuchEntityException(__($exceptionMsg, $id));
        }

        return $this->boxplosiveCustomerInterfaceFactory->create()
            ->load($id);
    }

    /**
     * @inheritDoc
     */
    public function create(): BoxplosiveCustomerInterface
    {
        return $this->boxplosiveCustomerInterfaceFactory->create();
    }

    /**
     * @inheritDoc
     */
    public function delete(BoxplosiveCustomerInterface $customer): bool
    {
        try {
            $this->boxplosiveCustomerResource->delete($customer);
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
    public function getByCustomerId(int $customerId): BoxplosiveCustomerInterface
    {
        if (!$customerId) {
            $exceptionMsg = self::INPUT_EXCEPTION;
            throw new InputException(__($exceptionMsg));
        } elseif (!$this->boxplosiveCustomerResource->isCustomerExists($customerId)) {
            $exceptionMsg = self::NO_SUCH_ENTITY_EXCEPTION;
            throw new NoSuchEntityException(__($exceptionMsg, $customerId));
        }

        return $this->boxplosiveCustomerInterfaceFactory->create()
            ->load($customerId, 'customer_id');
    }

    /**
     * @inheritDoc
     */
    public function save(BoxplosiveCustomerInterface $customer): BoxplosiveCustomerInterface
    {
        try {
            $this->boxplosiveCustomerResource->save($customer);
        } catch (\Exception $exception) {
            $exceptionMsg = self::COULD_NOT_SAVE_EXCEPTION;
            throw new CouldNotSaveException(__(
                $exceptionMsg,
                $exception->getMessage()
            ));
        }

        return $customer;
    }
}
