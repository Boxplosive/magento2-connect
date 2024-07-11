<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Boxplosive\Connect\Controller\Coupon;

use Boxplosive\Connect\Api\Discount\RepositoryInterface as BoxplosiveDiscountRepository;
use Boxplosive\Connect\Api\WebApi\Distribution\RepositoryInterface as DistributionRepository;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Quote\Api\Data\CartInterface;

/**
 * Apply coupon controller
 */
class Apply implements HttpPostActionInterface
{

    /**
     * @var MessageManagerInterface
     */
    protected $messageManager;
    /**
     * @var RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @var RequestInterface
     */
    private $request;
    /**
     * @var DistributionRepository
     */
    private $distributionRepository;
    /**
     * @var Session
     */
    private $session;
    /**
     * @var RedirectInterface
     */
    private $redirect;
    /**
     * @var BoxplosiveDiscountRepository
     */
    private $boxplosiveDiscountRepository;

    /**
     * Apply constructor.
     * @param RequestInterface $request
     * @param DistributionRepository $distributionRepository
     * @param Session $session
     * @param MessageManagerInterface $messageManager
     * @param RedirectFactory $resultRedirectFactory
     * @param RedirectInterface $redirect
     * @param BoxplosiveDiscountRepository $boxplosiveDiscountRepository
     */
    public function __construct(
        RequestInterface $request,
        DistributionRepository $distributionRepository,
        Session $session,
        MessageManagerInterface $messageManager,
        RedirectFactory $resultRedirectFactory,
        RedirectInterface $redirect,
        BoxplosiveDiscountRepository $boxplosiveDiscountRepository
    ) {
        $this->request = $request;
        $this->distributionRepository = $distributionRepository;
        $this->session = $session;
        $this->messageManager = $messageManager;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->redirect = $redirect;
        $this->boxplosiveDiscountRepository = $boxplosiveDiscountRepository;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        if (!$this->boxplosiveDiscountRepository->canApplyDiscount($this->session->getQuote())) {
            $this->messageManager->addErrorMessage(
                __('Only one discount can be applied, discount code or loyalty discount.')
            );
            return $resultRedirect->setPath(
                $this->redirect->getRefererUrl()
            );
        }
        $params = $this->request->getParams();

        //unset all params except reserved
        foreach ($params as $key => $param) {
            if (!str_contains($key, 'reserved')) {
                unset($params[$key]);
            }
        }

        try {
            $customer = $this->session->getQuote()->getCustomer();
            foreach ($params as $code) {
                $result = $this->distributionRepository->claimCoupon($customer, $code);
                if ($result['success']) {
                    $this->session->getQuote()->collectTotals();
                    $this->messageManager->addSuccessMessage(__('Your coupon was successfully applied.'));
                } else {
                    $this->messageManager->addErrorMessage(__('Something went wrong'));
                }
            }
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage(__('Something went wrong'));
        }

        return $resultRedirect->setPath(
            $this->redirect->getRefererUrl() . '#payment'
        );
    }
}
