<?php

namespace Application\User\Model;

class User
{
    /** @var int $userId */
    public $userId;
    /** @var string|null $userName */
    public $userName;
    /** @var string|null $password */
    public $password;
    /** @var string|null $firstName */
    public $firstName;
    /** @var string|null $lastName */
    public $lastName;
    /** @var string|null $email */
    public $email;
    /** @var string|null $phoneNumber */
    public $phoneNumber;
    /** @var string|null $address */
    public $address;
    /** @var int $userTypeId*/
    public $userTypeId;

    public function exchangeArray($data)
    {
        $this->userId = !empty($data['userId']) ? $data['userId'] : null;
        $this->userName = !empty($data['userName']) ? $data['userName'] : null;
        $this->password = !empty($data['password']) ? $data['password'] : null;
        $this->firstName = !empty($data['firstName']) ? $data['firstName'] : null;
        $this->lastName = !empty($data['lastName']) ? $data['lastName'] : null;
        $this->email = !empty($data['email']) ? $data['email'] : null;
        $this->phoneNumber = !empty($data['phoneNumber']) ? $data['phoneNumber'] : null;
        $this->address = !empty($data['address']) ? $data['address'] : null;
        $this->userTypeId = !empty($data['userTypeId']) ? $data['userTypeId'] : null;
    }
}
