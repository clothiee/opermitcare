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

        return $rowSet->current();
    }

    public function getUserTypeByColumns($columns)
    {
        $rowSet = $this->tableGateway->select($columns);
        $row = (array) $rowSet->current();

        if (!$row) {
            $row = [];
        }

        return $row;
    }

    public function saveUserType(UserType $userType)
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
            $this->getUserTypeByColumns(['userTypeId' => $userTypeId]);
        } catch (RuntimeException $e) {
            throw new RuntimeException(sprintf(
                                           'Cannot update user type with identifier %d; does not exist',
                                           $userTypeId
                                       ));
        }

        $this->tableGateway->update($data, ['userTypeId' => $userTypeId]);
    }

    public function deleteUserType($userTypeId)
    {
        $this->tableGateway->delete(['userTypeId' => (int) $userTypeId]);
    }

    public function getUserTypeByUserTypeId()
    {
        $table = $this->tableGateway->getTable();
        $select = new Select($table);
        $select->join('user', 'user.userTypeId = ' . $table . '.userTypeId', ['*'], $select::JOIN_LEFT);
        $rowSet = $this->tableGateway->selectWith($select);
        $row = (array) $rowSet->current();

        if (!$row) {
            $row = [];
        }

        return $row;
    }
}
