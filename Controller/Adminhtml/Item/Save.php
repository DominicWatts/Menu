<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Xigen\Menu\Controller\Adminhtml\Item;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;

class Save extends \Magento\Backend\App\Action
{
    const TEMP_PATH = 'catalog/tmp/category/';
    const OPTION_PATH = 'menu/';
    const MEDIA_PATH = DirectoryList::MEDIA . '/';

    /**
     * @var \Magento\Framework\App\Request\DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    protected $filesystemDriver;

    /**
     * @var string
     */
    protected $mediapath;

    /**
     * Save function
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Filesystem\Driver\File $filesystemDriver
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Filesystem\Driver\File $filesystemDriver
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->filesystem = $filesystem;
        $this->filesystemDriver = $filesystemDriver;
        $this->mediapath = $this->filesystem
            ->getDirectoryRead(DirectoryList::MEDIA)
            ->getAbsolutePath();
        parent::__construct($context);
    }

    /**
     * Save action
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $id = $this->getRequest()->getParam('item_id');

            $data = $this->uploadImage($data, 'side_image');

            $model = $this->_objectManager->create(\Xigen\Menu\Model\Item::class)->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This Item no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }

            $model->setData($data);

            try {
                $model->save();
                $this->messageManager->addSuccessMessage(__('You saved the Item.'));
                $this->dataPersistor->clear('xigen_menu_item');

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['item_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Item.'));
            }

            $this->dataPersistor->set('xigen_menu_item', $data);
            return $resultRedirect->setPath('*/*/edit', ['item_id' => $this->getRequest()->getParam('item_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Upload image function
     * @param array $data
     * @param string $field
     * @return array
     */
    public function uploadImage($data, $field = null)
    {
        if (isset($data[$field][0]['name']) && isset($data[$field][0]['url'])) {
            try {
                $filename = $data[$field][0]['name'];
                $url = $data[$field][0]['url'];
                $tempPath = $this->mediapath . self::TEMP_PATH;
                $destinationPath = $this->mediapath . self::OPTION_PATH;
                if (!$this->filesystemDriver->isDirectory($destinationPath)) {
                    $this->filesystemDriver->createDirectory($destinationPath);
                }

                unset($data[$field]);
                if ($this->filesystemDriver->isExists($tempPath . $filename)) {
                    $this->filesystemDriver->copy($tempPath . $filename, $destinationPath . $filename);
                    $url = '/' . self::MEDIA_PATH . self::OPTION_PATH . $filename;
                    $data[$field] = $url;
                }
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong with image.'));
            }
        }
        return $data;
    }
}
