<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Boxplosive\Connect\Plugin\Checkout;

use Boxplosive\Connect\Api\Discount\RepositoryInterface as BoxplosiveDiscountRepository;
use Magento\Checkout\Controller\Cart\CouponPost as Subject;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Checkout\Model\Session;

/**
 * Show message if boxplosive discount was applied before magento coupon
 */
class BeforeCouponPostPlugin
{
    /**
     * @var MessageManagerInterface
     */
    protected $messageManager;
    /**
     * @var BoxplosiveDiscountRepository
     */
    private $boxplosiveDiscountRepository;
    /**
     * @var Session
     */
    private $session;

    /**
     * BeforeCouponPostPlugin constructor.
     * @param MessageManagerInterface $messageManager
     * @param BoxplosiveDiscountRepository $boxplosiveDiscountRepository
     * @param Session $session
     */
    public function __construct(
        MessageManagerInterface $messageManager,
        BoxplosiveDiscountRepository $boxplosiveDiscountRepository,
        Session $session
    ) {
        $this->messageManager = $messageManager;
        $this->boxplosiveDiscountRepository = $boxplosiveDiscountRepository;
        $this->session = $session;
    }

    /**
     * @param Subject $subject
     * @return null
     */
    public function beforeExecute(Subject $subject)
    {
        try {
            $boxplosiveDiscount = $this->boxplosiveDiscountRepository->getByQuoteId((int)$this->session->getQuoteId());
            if ($boxplosiveDiscount->getDiscount()) {
                $this->messageManager->addNoticeMessage(
                    __('Only one discount can be applied, discount code or loyalty discount.')
                );
            }
        } catch (\Exception $e) {
            return null;
        }

        return null;
    }
}
