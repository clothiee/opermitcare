<?php

namespace Application\Opermitcare\User\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;

class UserTable
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

    public function save(User $user)
    {
        $data = [
            'userName' => $user->userName,
            'password' => $user->password,
            'firstName' => $user->firstName,
            'lastName' => $user->lastName,
            'email' => $user->email,
            'phoneNumber' => $user->phoneNumber,
            'address' => $user->address,
            'userTypeId' => $user->userTypeId,
        ];

        $userId = (int) $user->userId;

        if ($userId === 0) {
            $this->tableGateway->insert($data);
            return;
        }

        try {
            $this->getByColumns(['userId' => $userId]);
        } catch (RuntimeException $e) {
            throw new RuntimeException(sprintf(
            'Cannot update user with identifier %d; does not exist',
                $userId
            ));
        }

        $this->tableGateway->update($data, ['userId' => $userId]);
    }

    public function delete($userId)
    {
        $this->tableGateway->delete(['userId' => (int) $userId]);
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
