<?php

namespace Application\Opermitcare\File\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;

class FileTable
{
    private $tableGateway;

    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $rowSet = $this->tableGateway->select();

        return $this->parseRow($rowSet);
    }

    public function getByColumns($columns)
    {
        $rowSet = $this->tableGateway->select($columns);

        return $this->parseRow($rowSet);
    }

    public function save(File $file)
    {
        $data = [
            'ticketId' => $file->ticketId,
            'fileName' => $file->fileName,
            'filePath' => $file->filePath,
        ];

        $fileId = (int) $file->fileId;

        if ($fileId === 0) {
            $this->tableGateway->insert($data);
            return;
        }

        try {
            $this->getByColumns(['fileId' => $fileId]);
        } catch (RuntimeException $e) {
            throw new RuntimeException(sprintf(
                                           'Cannot update user with identifier %d; does not exist',
                                           $fileId
                                       ));
        }

        $this->tableGateway->update($data, ['fileId' => $fileId]);
    }

    public function delete($fileId)
    {
        $this->tableGateway->delete(['fileId' => (int) $fileId]);
    }

    private function parseRow($rowSet)
    {
        $data = [];

        foreach ($rowSet as $row) {
            $data[] = $row;
        }

        return $data;
    }
}
