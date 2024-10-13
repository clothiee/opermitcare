<?php

namespace Application\UserType\Model;

class UserType
{
    /** @var int $userTypeId*/
    public $userTypeId;
    /** @var int $userTypeName*/
    public $userTypeName;

    public function exchangeArray($data)
    {
        $this->userTypeId = !empty($data['userTypeId']) ? $data['userTypeId'] : null;
        $this->userTypeName = !empty($data['userTypeName']) ? $data['userTypeName'] : null;
    }
}
