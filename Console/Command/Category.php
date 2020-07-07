<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Xigen\Menu\Console\Command;

use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Console\Cli;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\ProgressBarFactory;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Magento\Catalog\Helper\Category as HelperCategory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\ResourceConnection;
use Xigen\Menu\Api\ItemRepositoryInterface;
use Xigen\Menu\Api\Data\ItemInterfaceFactory;
use Xigen\Menu\Helper\Data;
use Xigen\Menu\Model\MenuFactory;

class Category extends Command
{
    const MENU_OPTION = 'menu';
    const STORE_OPTION = 'store';

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $state;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $dateTime;

    /**
     * @var InputInterface
     */
    protected $input;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var ProgressBarFactory
     */
    protected $progressBarFactory;

    protected $storeManager;

    /**
     * @var array
     */
    private $report = [];

    /**
     * @var string
     */
    private $tableName;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    private $connection;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resource;

    /**
     * @var \Xigen\Menu\Api\Data\ItemInterfaceFactory
     */
    protected $itemInterfaceFactory;

    /**
     * @var \Xigen\Menu\Api\ItemRepositoryInterface
     */
    protected $itemRepositoryInterface;

    /**
     * @var \Xigen\Menu\Helper\Data
     */
    protected $data;

    /**
     * @var \Xigen\Menu\Model\MenuFactory
     */
    protected $menuFactory;

    /**
     * @var HelperCategory
     */
    protected $helper;

    /**
     * Category constructor.
     * @param LoggerInterface $logger
     * @param State $state
     * @param DateTime $dateTime
     * @param ProgressBarFactory $progressBarFactory
     * @param StoreManagerInterface $storeManager
     * @param HelperCategory $helper
     * @param ResourceConnection $resource
     * @param ItemInterfaceFactory $itemInterfaceFactory
     * @param ItemRepositoryInterface $itemRepositoryInterface
     * @param Data $data
     * @param MenuFactory $menuFactory
     */
    public function __construct(
        LoggerInterface $logger,
        State $state,
        DateTime $dateTime,
        ProgressBarFactory $progressBarFactory,
        StoreManagerInterface $storeManager,
        HelperCategory $helper,
        ResourceConnection $resource,
        ItemInterfaceFactory $itemInterfaceFactory,
        ItemRepositoryInterface $itemRepositoryInterface,
        Data $data,
        MenuFactory $menuFactory
    ) {
        $this->logger = $logger;
        $this->state = $state;
        $this->dateTime = $dateTime;
        $this->progressBarFactory = $progressBarFactory;
        $this->storeManager = $storeManager;
        $this->helper = $helper;
        $this->connection = $resource->getConnection();
        $this->resource = $resource;
        $this->itemInterfaceFactory = $itemInterfaceFactory;
        $this->itemRepositoryInterface = $itemRepositoryInterface;
        $this->data = $data;
        $this->menuFactory = $menuFactory;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $this->input = $input;
        $this->output = $output;
        $this->state->setAreaCode(Area::AREA_GLOBAL);

        $menu = $this->input->getOption(self::MENU_OPTION) ?: null;
        $store = $this->input->getOption(self::STORE_OPTION) ?: null;
        if ($menu && $store) {

            $helper = $this->getHelper('question');
            $question = new ConfirmationQuestion(
                (string) __(
                    'You are about to generate tree for store ID: %1 and save as menu ID: %2 losing existing items. Are you sure? [y/N]', //phpcs:ignore
                    $store,
                    $menu
                ),
                false
            );

            if (!$helper->ask($this->input, $this->output, $question) && $this->input->isInteractive()) {
                return Cli::RETURN_FAILURE;
            }

            $this->output->writeln('[' . $this->dateTime->gmtDate() . '] Start');
            $this->storeManager->setCurrentStore($store);

            $this->output->writeln('[' . $this->dateTime->gmtDate() . '] Cleaning');
            $this->clearMenuItems($menu);

            $this->output->writeln('[' . $this->dateTime->gmtDate() . '] Generating');
            $categories = $this->helper->getStoreCategories(false, true, true);

            /** @var ProgressBar $progress */
            $progress = $this->progressBarFactory->create(
                [
                    'output' => $this->output,
                    'max' => count($categories)
                ]
            );

            $progress->setFormat(
                "%current%/%max% [%bar%] %percent:3s%% %elapsed% %memory:6s% \t| <info>%message%</info>"
            );

            if ($this->output->getVerbosity() !== OutputInterface::VERBOSITY_NORMAL) {
                $progress->setOverwrite(false);
            }

            foreach ($categories as $category) {
                $progress->setMessage((string) __(
                    'Category: %1 %2',
                    $category->getId(),
                    $category->getName()
                ));
                $this->processCategory($category, $menu);
                $progress->advance();
            }

            $this->output->writeln('');
            $progress->finish();

            $this->output->writeln('[' . $this->dateTime->gmtDate() . '] Linking');
            $this->associateItems($menu);
            $this->output->writeln('[' . $this->dateTime->gmtDate() . '] Finish');

        }
    }

    public function processCategory($category, $menu)
    {
        // Check category status and menu visibility
        if (!$category->getIsActive()) {
            return false;
        }

        if (!$category->getIncludeInMenu()) {
            return false;
        }

        $item = $this->itemInterfaceFactory
            ->create()
            ->setMenuId($menu)
            ->setParentId(0)
            ->setTitle($category->getName())
            ->setIdentifier($category->getUrlKey())
            ->setOpenType(Data::SAME_WINDOW)
            ->setUrlType(Data::CATEGORY)
            ->setCategoryId($category->getId())
            ->setPosition($category->getPosition())
            ->setIsActive($category->getIsActive());

        try {
            return $this->itemRepositoryInterface->save($item);
        } catch (\Exception $e) {
            $this->logger->critical($e);
        }
        return false;
    }

    public function associateItems($menu)
    {
        $items = $this->menuFactory->create()
            ->load($menu)
            ->getItemCollection();

        foreach ($items as $item) {
            if ($category = $item->getCategory()) {
                $parentItem = $this->data->getByMenuAndCategory($menu, $category->getParentId());
                if ($parentItem && $parentItem->getItemId()) {
                    try {
                        $item->setParentId($parentItem->getItemId());
                        $item->save();
                    } catch (\Exception $e) {
                        $this->logger->critical($e);
                    }
                }
            }
        }
    }

    /**
     * Clean customer quotes
     * @return array
     */
    public function clearMenuItems($menuId = null)
    {
        $this->tableName = $this->getTableName();
        $this->report = [];
        if ($menuId) {
            $select = $this->connection
                ->select()
                ->from($this->tableName)
                ->where('menu_id = ?', $menuId);

            $report = $this->doDeleteFromSelect($select);
        }
        return $report;
    }

    /**
     * Get quote table name
     * @return string
     */
    public function getTableName()
    {
        return $this->resource->getTableName('xigen_menu_item');
    }

    public function doDeleteFromSelect($select)
    {
        try {
            // deliberate empty second argument
            $query = $this->connection->deleteFromSelect($select, []);
            $statement = $this->connection->query($query);
            $this->report['quote_count'] = $statement->rowCount();
        } catch (\Exception $e) {
            $this->logger->critical($e);
        }
        return $this->report;
    }

    /**
     * {@inheritdoc}
     * xigen:menu:category [-m|--menu MENU] [-s|--store STORE]
     */
    protected function configure()
    {
        $this->setName("xigen:menu:category");
        $this->setDescription("import category tree into menu builder");
        $this->setDefinition([
            new InputOption(self::MENU_OPTION, '-m', InputOption::VALUE_REQUIRED, 'Menu Id'),
            new InputOption(self::STORE_OPTION, '-s', InputOption::VALUE_REQUIRED, 'Store Id'),
        ]);
        parent::configure();
    }
}
