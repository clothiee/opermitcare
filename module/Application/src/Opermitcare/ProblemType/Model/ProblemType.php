<?php

namespace Application\Opermitcare\ProblemType\Model;

class ProblemType
{
    /** @var int $problemTypeId */
    public $problemTypeId;
    /** @var string|null $problemTypeName */
    public $problemTypeName;
    /** @var int $active*/
    public $active;

    public function exchangeArray($data)
    {
        $this->problemTypeId = !empty($data['problemTypeId']) ? $data['problemTypeId'] : null;
        $this->problemTypeName = !empty($data['problemTypeName']) ? $data['problemTypeName'] : null;
        $this->active= !empty($data['active']) ? $data['active'] : 0;
    }
}
