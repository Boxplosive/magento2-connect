<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Boxplosive\Connect\Plugin\Customer;

use Boxplosive\Connect\Api\Config\RepositoryInterface as ConfigRepository;
use Boxplosive\Connect\Api\Customer\RepositoryInterface as BoxplosiveCustomerRepository;
use Boxplosive\Connect\Api\Log\RepositoryInterface as LogRepository;
use Boxplosive\Connect\Api\WebApi\BackOffice\RepositoryInterface as BackOfficeRepository;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\App\RequestInterface;

/**
 * After Customer Registration Plugin
 */
class AfterRegistrationPlugin
{

    /**
     * @var ConfigRepository
     */
    private $configRepository;
    /**
     * @var BackOfficeRepository
     */
    private $backOfficeRepository;
    /**
     * @var BoxplosiveCustomerRepository
     */
    private $boxplosiveCustomerRepository;
    /**
     * @var LogRepository
     */
    private $logRepository;
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * AfterRegistrationPlugin constructor.
     * @param ConfigRepository $configRepository
     * @param BackOfficeRepository $backOfficeRepository
     * @param BoxplosiveCustomerRepository $boxplosiveCustomerRepository
     * @param LogRepository $logRepository
     * @param RequestInterface $request
     */
    public function __construct(
        ConfigRepository $configRepository,
        BackOfficeRepository $backOfficeRepository,
        BoxplosiveCustomerRepository $boxplosiveCustomerRepository,
        LogRepository $logRepository,
        RequestInterface $request
    ) {
        $this->configRepository = $configRepository;
        $this->backOfficeRepository = $backOfficeRepository;
        $this->boxplosiveCustomerRepository = $boxplosiveCustomerRepository;
        $this->logRepository = $logRepository;
        $this->request = $request;
    }

    /**
     * @param AccountManagementInterface $subject
     * @param CustomerInterface $resultCustomer
     * @param CustomerInterface $customer
     * @return CustomerInterface
     */
    public function afterCreateAccount(
        AccountManagementInterface $subject,
        CustomerInterface $resultCustomer,
        CustomerInterface $customer
    ): CustomerInterface {
        if ($this->configRepository->isEnabled((int)$customer->getStoreId()) &&
            $this->request->getParam('boxplosive-opt-in')) {
            try {
                $processCustomer = $this->backOfficeRepository->processCustomer($resultCustomer);
                if ($processCustomer['success']) {
                    $boxplosiveCustomer = $this->boxplosiveCustomerRepository->create();
                    $boxplosiveCustomer->setCustomerId((int)$resultCustomer->getId());

                    $this->boxplosiveCustomerRepository->save($boxplosiveCustomer);
                }
            } catch (\Exception $exception) {
                $this->logRepository->addErrorLog('afterCreateAccount', $exception->getMessage());
                return $resultCustomer;
            }
        }
        return $resultCustomer;
    }
}
