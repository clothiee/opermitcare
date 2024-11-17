<?php

namespace Application\UserType\Model;

use Laminas\Db\Sql\Select;
use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;

class UserTypeTable
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

    public function save(UserType $userType)
    {
        $data = [
            'userTypeName' => $userType->userTypeName,
        ];

        $userTypeId = (int) $userType->userTypeId;

        if ($userTypeId === 0) {
            $this->tableGateway->insert($data);
            return;
        }

        try {
            $this->getByColumns(['userTypeId' => $userTypeId]);
        } catch (RuntimeException $e) {
            throw new RuntimeException(sprintf(
                                           'Cannot update user type with identifier %d; does not exist',
                                           $userTypeId
                                       ));
        }

        $this->tableGateway->update($data, ['userTypeId' => $userTypeId]);
    }

    public function delete($userTypeId)
    {
        $this->tableGateway->delete(['userTypeId' => (int) $userTypeId]);
    }

    public function getByUserTypeId($userTypeId)
    {
        $table = $this->tableGateway->getTable();
        $select = new Select($table);
        $select->join('user', 'user.userTypeId = ' . $table . '.userTypeId', ['*'], $select::JOIN_LEFT)
            ->where([
                        $table . '.userTypeId' => $userTypeId,
                    ]);
        $rowSet = $this->tableGateway->selectWith($select);
        $row = (array) $rowSet->current();

        if (!$row) {
            $row = [];
        }

        return $row;
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
