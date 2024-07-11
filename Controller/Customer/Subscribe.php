<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Boxplosive\Connect\Controller\Customer;

use Boxplosive\Connect\Api\Customer\RepositoryInterface as BoxplosiveCustomerRepository;
use Boxplosive\Connect\Api\WebApi\BackOffice\RepositoryInterface as BackOfficeRepository;
use Boxplosive\Connect\Api\WebApi\Distribution\RepositoryInterface as DistributionRepository;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;

/**
 * Subscribe customer controller
 */
class Subscribe implements HttpPostActionInterface
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
     * @var Session
     */
    private $session;
    /**
     * @var RedirectInterface
     */
    private $redirect;
    /**
     * @var BoxplosiveCustomerRepository
     */
    private $boxplosiveCustomerRepository;
    /**
     * @var BackOfficeRepository
     */
    private $backOfficeRepository;

    /**
     * Subscribe constructor.
     * @param Session $session
     * @param MessageManagerInterface $messageManager
     * @param RedirectFactory $resultRedirectFactory
     * @param RedirectInterface $redirect
     * @param BoxplosiveCustomerRepository $boxplosiveCustomerRepository
     * @param BackOfficeRepository $backOfficeRepository
     */
    public function __construct(
        Session $session,
        MessageManagerInterface $messageManager,
        RedirectFactory $resultRedirectFactory,
        RedirectInterface $redirect,
        BoxplosiveCustomerRepository $boxplosiveCustomerRepository,
        BackOfficeRepository $backOfficeRepository
    ) {
        $this->session = $session;
        $this->messageManager = $messageManager;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->redirect = $redirect;
        $this->boxplosiveCustomerRepository = $boxplosiveCustomerRepository;
        $this->backOfficeRepository = $backOfficeRepository;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        try {
            $customer = $this->session->getQuote()->getCustomer();
            $processCustomer = $this->backOfficeRepository->processCustomer($customer);
            if ($processCustomer['success']) {
                $boxplosiveCustomer = $this->boxplosiveCustomerRepository->create();
                $boxplosiveCustomer->setCustomerId((int)$customer->getId());
                $this->boxplosiveCustomerRepository->save($boxplosiveCustomer);
                $this->messageManager->addSuccessMessage(__('You was successfully subscribed!'));
            } else {
                $this->messageManager->addErrorMessage(__('Something went wrong'));
            }
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage(__('Something went wrong'));
        }

        return $resultRedirect->setPath(
            $this->redirect->getRefererUrl()
        );
    }
}
