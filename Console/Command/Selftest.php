<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Boxplosive\Connect\Console\Command;

use Boxplosive\Connect\Api\Selftest\RepositoryInterface as SelftestRepository;
use Magento\Framework\Console\Cli;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Selftest
 *
 * Perform tests on module
 */
class Selftest extends Command
{

    /**
     * Command call name
     */
    public const COMMAND_NAME = 'boxplosive:selftest';

    /**
     * @var SelftestRepository
     */
    private $selftestRepository;

    /**
     * Selftest constructor.
     *
     * @param SelftestRepository $selftestRepository
     */
    public function __construct(
        SelftestRepository $selftestRepository
    ) {
        $this->selftestRepository = $selftestRepository;
        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    public function configure()
    {
        $this->setName(self::COMMAND_NAME);
        $this->setDescription('Perform self test of extension');
        parent::configure();
    }

    /**
     * @inheritdoc
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $result = $this->selftestRepository->test();
        foreach ($result as $test) {
            if ($test['result_code'] == 'success') {
                $output->writeln(
                    sprintf(
                        '<info>%s:</info> %s- %s',
                        $test['test'],
                        $test['result_code'],
                        $test['result_msg']
                    )
                );
            } else {
                $output->writeln(
                    sprintf(
                        '<info>%s:</info> <error>%s</error>- %s',
                        $test['test'],
                        $test['result_code'],
                        $test['result_msg']
                    )
                );
            }
        }

        return Cli::RETURN_SUCCESS;
    }
}
