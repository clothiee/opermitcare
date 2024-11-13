<?php

namespace Application\PermitStatus\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;

class PermitStatusTable
{
    private $tableGateway;

    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $rowSet = $this->tableGateway->select();
        $data = [];

        foreach ($rowSet as $row) {
            $data[] = $row;
        }

        return $data;
    }

    public function getByColumns($columns)
    {
        $rowSet = $this->tableGateway->select($columns);
        $data = [];

        foreach ($rowSet as $row) {
            $data[] = $row;
        }

        return $data;
    }

    public function save(PermitStatus $permitStatus)
    {
        $data = [
            'problemTypeName' => $permitStatus->permitStatusName,
            'active' => $permitStatus->active,
        ];

        $permitStatusId = (int) $permitStatus->permitStatusId;

        if ($permitStatusId === 0) {
            $this->tableGateway->insert($data);
            return;
        }

        try {
            $this->getByColumns(['permitStatusId' => $permitStatusId]);
        } catch (RuntimeException $e) {
            throw new RuntimeException(sprintf(
                                           'Cannot update user with identifier %d; does not exist',
                                           $permitStatusId
                                       ));
        }

        $this->tableGateway->update($data, ['permitStatusId' => $permitStatusId]);
    }

    public function delete($permitStatusId)
    {
        $this->tableGateway->delete(['permitStatusId' => (int) $permitStatusId]);
    }
}
