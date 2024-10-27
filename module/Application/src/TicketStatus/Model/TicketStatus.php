<?php

namespace Application\TicketStatus\Model;

class TicketStatus
{
    /** @var int $ticketStatusId */
    public $ticketStatusId;
    /** @var string|null $ticketStatusName */
    public $ticketStatusName;
    /** @var int $active*/
    public $active;

    public function exchangeArray($data)
    {
        $this->ticketStatusId = !empty($data['ticketStatusId']) ? $data['ticketStatusId'] : null;
        $this->ticketStatusName = !empty($data['ticketStatusName']) ? $data['ticketStatusName'] : null;
        $this->active= !empty($data['active']) ? $data['active'] : 0;
    }
}
