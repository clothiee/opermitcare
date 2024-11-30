<?php

namespace Application\Opermitcare\Download\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;

class DownloadTable
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

    public function save(Download $download)
    {
        $data = [
            'fileId' => $download->fileId,
            'title' => $download->title,
            'type' => $download->type,
            'active' => $download->active,
        ];

        $downloadId = (int) $download->downloadId;

        if ($downloadId === 0) {
            $this->tableGateway->insert($data);
            return;
        }

        try {
            $this->getByColumns(['downloadId' => $downloadId]);
        } catch (RuntimeException $e) {
            throw new RuntimeException(sprintf(
                                           'Cannot update user with identifier %d; does not exist',
                                           $downloadId
                                       ));
        }

        $this->tableGateway->update($data, ['downloadId' => $downloadId]);
    }

    public function delete($downloadId)
    {
        $this->tableGateway->delete(['downloadId' => (int) $downloadId]);
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
