<?php

namespace Application\ProblemType\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;

class ProblemTypeTable
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

    public function save(ProblemType $problemType)
    {
        $data = [
            'problemTypeName' => $problemType->problemTypeName,
            'active' => $problemType->active,
        ];

        $problemTypeId = (int) $problemType->problemTypeId;

        if ($problemTypeId === 0) {
            $this->tableGateway->insert($data);
            return;
        }

        try {
            $this->getByColumns(['problemTypeId' => $problemTypeId]);
        } catch (RuntimeException $e) {
            throw new RuntimeException(sprintf(
                                           'Cannot update user with identifier %d; does not exist',
                                           $problemTypeId
                                       ));
        }

        $this->tableGateway->update($data, ['problemTypeId' => $problemTypeId]);
    }

    public function delete($problemTypeId)
    {
        $this->tableGateway->delete(['problemTypeId' => (int) $problemTypeId]);
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
