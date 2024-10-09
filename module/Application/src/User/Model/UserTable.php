<?php

namespace Application\User\Model;

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

        return $rowSet->current();
    }

    public function getUserByColumns($columns)
    {
        $rowSet = $this->tableGateway->select($columns);
        $row = (array) $rowSet->current();

        if (!$row) {
            $row = [];
        }

        return $row;
    }

    public function saveUser(User $user)
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
            $this->getUser($userId);
        } catch (RuntimeException $e) {
            throw new RuntimeException(sprintf(
            'Cannot update album with identifier %d; does not exist',
                $userId
            ));
        }

        $this->tableGateway->update($data, ['userId' => $userId]);
    }

    public function deleteAlbum($userId)
    {
        $this->tableGateway->delete(['userId' => (int) $userId]);
    }
}
