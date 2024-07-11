<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Boxplosive\Connect\Api\Customer;

use Boxplosive\Connect\Api\Customer\DataInterface as BoxplosiveCustomer;
use Magento\Framework\Exception\LocalizedException;

/**
 * Customer Repository Interface
 * @api
 */
interface RepositoryInterface
{

    /**
     * Input exception text
     */
    public const INPUT_EXCEPTION = 'An ID is needed. Set the ID and try again.';
    /**
     * "No such entity" exception text
     */
    public const NO_SUCH_ENTITY_EXCEPTION = 'The customer with id "%1" does not exist.';
    /**
     * "Could not delete" exception text
     */
    public const COULD_NOT_DELETE_EXCEPTION = 'Could not delete the customer: %1';
    /**
     * "Could not save" exception text
     */
    public const COULD_NOT_SAVE_EXCEPTION = 'Could not save the customer: %1';

    /**
     * Loads a specified category
     *
     * @param int $id
     *
     * @return BoxplosiveCustomer
     * @throws LocalizedException
     */
    public function get(int $id): BoxplosiveCustomer;

    /**
     * Loads a specified customer
     *
     * @param int $customerId
     *
     * @return BoxplosiveCustomer
     * @throws LocalizedException
     */
    public function getByCustomerId(int $customerId): BoxplosiveCustomer;

    /**
     * Return new category object
     *
     * @return BoxplosiveCustomer
     */
    public function create(): DataInterface;

    /**
     * Register entity to delete
     *
     * @param BoxplosiveCustomer $customer
     *
     * @return bool true on success
     * @throws LocalizedException
     */
    public function delete(BoxplosiveCustomer $customer): bool;

    /**
     * Register entity to save
     *
     * @param BoxplosiveCustomer $customer
     *
     * @return BoxplosiveCustomer
     * @throws LocalizedException
     */
    public function save(BoxplosiveCustomer $customer): BoxplosiveCustomer;
}
