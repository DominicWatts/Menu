<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Xigen\Menu\Model\Item;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\File\Mime;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Driver\File;
use Xigen\Menu\Model\ResourceModel\Item\CollectionFactory;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var array
     */
    protected $loadedData;
    /**
     * @var \Xigen\Menu\Model\ResourceModel\Item\Collection
     */
    protected $collection;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var File
     */
    protected $filesystemDriver;

    /**
     * @var Mime
     */
    private $mime;

    /**
     * @var string
     */
    private $mediapath;

    /**
     * Constructor
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        Filesystem $filesystem,
        File $filesystemDriver,
        Mime $mime,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->filesystem = $filesystem;
        $this->filesystemDriver = $filesystemDriver;
        $this->mime = $mime;
        $this->mediapath = $this->filesystem
            ->getDirectoryRead(DirectoryList::PUB)
            ->getAbsolutePath();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        foreach ($items as $model) {
            $option = $model->getData();
            $option = $this->resolveImageData($option, 'side_image', $model);
            $this->loadedData[$model->getId()] = $option;
        }
        $data = $this->dataPersistor->get('xigen_menu_item');

        if (!empty($data)) {
            $model = $this->collection->getNewEmptyItem();
            $model->setData($data);
            $this->loadedData[$model->getId()] = $model->getData();
            $this->dataPersistor->clear('xigen_menu_item');
        }

        return $this->loadedData;
    }

    /**
     * Resolve image data
     * @param array $option
     * @param string $field
     * @param \Xigen\FrameBuilder\Model\Frame $model
     * @return array
     */
    public function resolveImageData($option, $field, $model)
    {
        if (isset($option[$field])) {
            $imageName = (rtrim($this->mediapath, '/') . $model->getData($field));
            unset($option[$field]);
            if ($this->filesystemDriver->isExists($imageName)) {
                $stat = $this->filesystemDriver->stat($imageName);
                $mime = $this->mime->getMimeType($imageName);
                $option[$field][0]['name'] = basename($imageName);
                $option[$field][0]['url'] = $model->getData($field);
                $option[$field][0]['size'] = isset($stat) ? $stat['size'] : 0;
                $option[$field][0]['type'] = $mime;
            }
        }
        return $option;
    }
}
