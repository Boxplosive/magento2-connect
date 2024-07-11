<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Boxplosive\Connect\Logger;

use Boxplosive\Connect\Api\Config\RepositoryInterface as ConfigRepository;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Monolog\Logger;

/**
 * DebugLogger logger class
 */
class DebugLogger extends Logger
{

    /**
     * @var Json
     */
    private $json;
    /**
     * @var ConfigRepository
     */
    private $configRepository;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * DebugLogger constructor.
     *
     * @param Json $json
     * @param string $name
     * @param ConfigRepository $configRepository
     * @param StoreManagerInterface $storeManager
     * @param array $handlers
     * @param array $processors
     */
    public function __construct(
        Json $json,
        string $name,
        ConfigRepository $configRepository,
        StoreManagerInterface $storeManager,
        array $handlers = [],
        array $processors = []
    ) {
        $this->json = $json;
        $this->configRepository = $configRepository;
        $this->storeManager = $storeManager;
        parent::__construct($name, $handlers, $processors);
    }

    /**
     * Add debug data to Log
     *
     * @param string $type
     * @param mixed $data
     */
    public function addLog(string $type, $data): void
    {
        try {
            $storeId = (int)$this->storeManager->getStore()->getId();
        } catch (\Exception $e) {
            $storeId = 0;
        }

        if (!$this->configRepository->isDebugMode($storeId)) {
            return;
        }

        if (is_array($data) || is_object($data)) {
            $this->addRecord(static::INFO, $type . ': ' . $this->json->serialize($data));
        } else {
            $this->addRecord(static::INFO, $type . ': ' . $data);
        }
    }
}
