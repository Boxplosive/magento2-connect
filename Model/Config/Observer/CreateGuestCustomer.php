<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Boxplosive\Connect\Model\Config\Observer;

use Boxplosive\Connect\Api\WebApi\BackOffice\RepositoryInterface as BackOfficeRepository;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;

/**
 * Class CreateGuestCustomer
 */
class CreateGuestCustomer implements ObserverInterface
{

    /**
     * @var RequestInterface
     */
    private $request;
    /**
     * @var BackOfficeRepository
     */
    private $backOfficeRepository;
    /**
     * @var MessageManagerInterface
     */
    protected $messageManager;

    /**
     * CreateGuestCustomer constructor.
     * @param RequestInterface $request
     * @param BackOfficeRepository $backOfficeRepository
     * @param MessageManagerInterface $messageManager
     */
    public function __construct(
        RequestInterface $request,
        BackOfficeRepository $backOfficeRepository,
        MessageManagerInterface $messageManager
    ) {
        $this->request = $request;
        $this->backOfficeRepository = $backOfficeRepository;
        $this->messageManager = $messageManager;
    }

    /**
     * @param EventObserver $observer
     * @return void
     * @throws LocalizedException
     */
    public function execute(EventObserver $observer)
    {
        $data = $this->getApiFieldsPostData();
        if (empty($data['tenant_id'])) {
            return;
        }

        $processGuestCustomer = $this->backOfficeRepository->processGuestCustomer();
        if (!$processGuestCustomer['success']) {
            $this->messageManager->addErrorMessage('Boxplosive guest customer was not processed');
        }
    }

    /**
     * @return array
     */
    private function getApiFieldsPostData(): array
    {
        return $this->request->getParam('groups')['api']['fields'] ?? [];
    }
}
