<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Boxplosive\Connect\Service\Discount;

use Boxplosive\Connect\Api\WebApi\PointOfSale\RepositoryInterface as PointOfSaleRepository;
use Boxplosive\Connect\Api\Log\RepositoryInterface as LogRepository;
use Magento\Checkout\Model\Session;

/**
 * Class GetPromotions
 */
class GetPromotions
{
    /**
     * @var PointOfSaleRepository
     */
    private $pointOfSaleRepository;
    /**
     * @var Session
     */
    private $session;
    /**
     * @var LogRepository
     */
    private $logRepository;

    /**
     * GetPromotions constructor.
     * @param PointOfSaleRepository $pointOfSaleRepository
     * @param Session $session
     * @param LogRepository $logRepository
     */
    public function __construct(
        PointOfSaleRepository $pointOfSaleRepository,
        Session $session,
        LogRepository $logRepository
    ) {
        $this->pointOfSaleRepository = $pointOfSaleRepository;
        $this->session = $session;
        $this->logRepository = $logRepository;
    }

    /**
     * Get promotions for current customer
     *
     * @return array
     */
    public function execute(): array
    {
        try {
            $customerCoupons = $this->pointOfSaleRepository->getCustomerInfo($this->session->getQuote());
            if ($customerCoupons['success'] == false) {
                return [];
            } else {
                return $customerCoupons['result'];
            }
        } catch (\Exception $e) {
            $this->logRepository->addErrorLog('GetPromotions', $e->getMessage());
            return [];
        }
    }
}
