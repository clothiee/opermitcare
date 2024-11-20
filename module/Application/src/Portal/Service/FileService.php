<?php

namespace Application\Portal\Service;

use Application\Opermitcare\File\Model\File;
use Application\Opermitcare\File\Model\FileTable;
use ArrayObject;

class FileService
{
    const SUCCESS_CODE = 200;
    const SUCCESS_MESSAGE = 'Success';
    const INVALID_CODE = 500;
    const INVALID_MESSAGE = 'Invalid file size.';
    const FILE_DIRECTORY = 'uploads';
    const FILE_NAME_FORMAT = '%s/%s-%s-%s.%s';
    const TEMPORARY_PATH = './public/%s';
    const UPLOAD_MAX_SIZE = 2000000;

    private $config;
    private $fileTable;

    /**
     * Session Service constructor.
     *
     * @param ArrayObject $config
     * @param FileTable   $fileTable
     */
    public function __construct(
        $config,
        FileTable $fileTable
    ) {
        $this->config = $config;
        $this->fileTable = $fileTable;
    }

    /**
     * Get Attachments
     *
     * @param string $tag
     *
     * @return array
     */
    public function getByTag($tag)
    {
        try {
            return $this->fileTable->getByColumns(['tag' => $tag]);
        } catch (\Exception $exception) {
            return [];
        }
    }

    /**
     * Upload File
     *
     * @param $tag
     * @param $files
     *
     * @return array
     */
    public function upload($tag, $files) {
        $response = [];

        if (!empty($files)) {
            foreach ($files as $file) {
                $response[] = $this->execute($tag, $file);
            }
        }

        return $response;
    }

    /**
     * Execute Upload File
     *
     * @param $tag
     * @param $file
     *
     * @return array
     */
    private function execute($tag, $file)
    {
        try {
            $fileSize = filesize($file['tmp_name']);

            if ($fileSize >= self::UPLOAD_MAX_SIZE || !$fileSize) {
                throw new \Exception(
                    self::INVALID_MESSAGE,
                    self::INVALID_CODE
                );
            }

            $pathInfo = pathinfo($file['name']);
            $newFilePath = str_replace(' ', '-', sprintf(
                self::FILE_NAME_FORMAT,
                self::FILE_DIRECTORY, $tag, strtolower($pathInfo['filename']), date('dmYHis'), $pathInfo['extension']
            ));
            $directoryPath = sprintf(self::TEMPORARY_PATH, self::FILE_DIRECTORY);

            if (!is_dir($directoryPath)) {
                mkdir($directoryPath, 0777, true);
            }

            move_uploaded_file($file['tmp_name'], sprintf(self::TEMPORARY_PATH, $newFilePath));

            $file = new File();
            $post = [
                'tag' => $tag,
                'fileName' => $pathInfo['filename'],
                'filePath' => $newFilePath,
                'dateCreated' => date('Y-m-d H:i:s'),
            ];

            $file->exchangeArray($post);
            $this->fileTable->save($file);

            return [
                'code' => self::SUCCESS_CODE,
                'message' => self::SUCCESS_MESSAGE,
            ];
        } catch (\Exception $exception) {
            return [
                'code' => self::INVALID_CODE,
                'message' => $exception->getMessage(),
            ];
        }
    }
}
