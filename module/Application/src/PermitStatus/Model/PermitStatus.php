<?php

namespace Application\PermitStatus\Model;

class PermitStatus
{
    /** @var int $permitStatusId */
    public $permitStatusId;
    /** @var string|null $permitStatusName */
    public $permitStatusName;
    /** @var int $active*/
    public $active;

    public function exchangeArray($data)
    {
        $this->permitStatusId = !empty($data['permitStatusId']) ? $data['permitStatusId'] : null;
        $this->permitStatusName = !empty($data['permitStatusName']) ? $data['permitStatusName'] : null;
        $this->active= !empty($data['active']) ? $data['active'] : 0;
    }
}
