<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Boxplosive\Connect\Api\WebApi\BackOffice;

use Magento\Customer\Api\Data\CustomerInterface;

/**
 * Back Office Repository Interface
 * @api
 */
interface RepositoryInterface
{

    public const REQUEST_URI_PROCESS_CUSTOMER = 'backoffice/tenant/%s/ProcessCustomer';

    /**
     * Enable an external customer registration process to share customer ID
     *
     * @param CustomerInterface $customer
     * @param bool $remove
     * @return array
     */
    public function processCustomer(CustomerInterface $customer, bool $remove = false): array;

    /**
     * Enable an external customer registration process for guest customer
     *
     * @return array
     */
    public function processGuestCustomer(): array;
}
