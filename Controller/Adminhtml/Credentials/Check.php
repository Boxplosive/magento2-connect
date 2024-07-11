<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Boxplosive\Connect\Controller\Adminhtml\Credentials;

use Boxplosive\Connect\Api\Config\RepositoryInterface as ConfigRepository;
use Boxplosive\Connect\Service\Api\Adapter;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;

/**
 * Credential check controller to validate API data
 */
class Check extends Action
{

    /**
     * @var Adapter
     */
    private $adapter;
    /**
     * @var Json
     */
    private $resultJson;
    /**
     * @var ConfigRepository
     */
    private $configProvider;

    /**
     * Check constructor.
     *
     * @param Action\Context $context
     * @param JsonFactory $resultJsonFactory
     * @param Adapter $adapter
     * @param ConfigRepository $configProvider
     */
    public function __construct(
        Action\Context $context,
        JsonFactory $resultJsonFactory,
        Adapter $adapter,
        ConfigRepository $configProvider
    ) {
        $this->adapter = $adapter;
        $this->resultJson = $resultJsonFactory->create();
        $this->configProvider = $configProvider;
        parent::__construct($context);
    }

    /**
     * @return Json
     */
    public function execute(): Json
    {
        $config = $this->getCredentials();
        if (!$config['credentials']['client_id'] ||
            !$config['credentials']['client_secret'] ||
            !$config['credentials']['tenant_id']
        ) {
            return $this->resultJson->setData(
                [
                    'success' => true,
                    'msg' => __('Set credentials first')
                ]
            );
        }

        try {
            $result = $this->adapter->execute(
                'CredentialsTest',
                'GET',
                ['credentials' => $config['credentials']],
                (int)$config['store_id']
            );

            if (!$result['success']) {
                return $this->resultJson->setData([
                    'success' => false,
                    'msg' => $result['result']
                ]);
            }
            return $this->resultJson->setData([
                'success' => true,
                'msg' => __('Credentials correct!')->render()
            ]);
        } catch (\Exception $exception) {
            return $this->resultJson->setData([
                'success' => true,
                'msg' => $exception->getMessage()
            ]);
        }
    }

    /**
     * @return array
     */
    private function getCredentials(): array
    {
        $storeId = (int)$this->getRequest()->getParam('store');
        $mode = $this->getRequest()->getParam('mode');
        $tenantId = $this->getRequest()->getParam('tenant_id');
        if ($mode == 'acceptance') {
            $clientId = $this->getRequest()->getParam('acceptance_client_id');
            $apiUrl = $this->getRequest()->getParam('acceptance_api_url');
            $b2cTenantName = $this->getRequest()->getParam('acceptance_b2c_tenant_name');
            $b2cTenantId = $this->getRequest()->getParam('acceptance_b2c_tenant_id');
            $clientSecret = $this->getRequest()->getParam('acceptance_client_secret');
        } else {
            $clientId = $this->getRequest()->getParam('production_client_id');
            $clientSecret = $this->getRequest()->getParam('production_client_secret');
            $apiUrl = $this->getRequest()->getParam('production_api_url');
            $b2cTenantName = $this->getRequest()->getParam('production_b2c_tenant_name');
            $b2cTenantId = $this->getRequest()->getParam('production_b2c_tenant_id');
        }

        if ($clientSecret == '******') {
            $clientSecret = $this->configProvider->getCredentials($storeId)['client_secret'];
        }

        return [
            'store_id' => $storeId,
            'credentials' => [
                "mode" => $mode,
                "api_url" => $apiUrl,
                "b2c_tenant_name" => $b2cTenantName,
                "b2c_tenant_id" => $b2cTenantId,
                "tenant_id" => $tenantId,
                "client_id" => $clientId,
                "client_secret" => $clientSecret,
            ]
        ];
    }
}
