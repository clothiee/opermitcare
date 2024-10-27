<?php

namespace Application\Ticket\Model;

class Ticket
{
    /** @var int $ticketId */
    public $ticketId;
    /** @var int $ticketStatusId */
    public $ticketStatusId;
    /** @var int $problemTypeId */
    public $problemTypeId;
    /** @var string|null $title */
    public $title;
    /** @var string|null $description */
    public $description;
    /** @var string|null $dateCreated */
    public $dateCreated;
    /** @var int $residentId */
    public $residentId;
    /** @var int $repliesId */
    public $repliesId;

    public function exchangeArray($data)
    {
        $this->ticketId = !empty($data['ticketId']) ? $data['ticketId'] : null;
        $this->ticketStatusId = !empty($data['ticketStatusId']) ? $data['ticketStatusId'] : null;
        $this->problemTypeId = !empty($data['problemTypeId']) ? $data['problemTypeId'] : null;
        $this->title = !empty($data['title']) ? $data['title'] : null;
        $this->description = !empty($data['description']) ? $data['description'] : null;
        $this->dateCreated = !empty($data['dateCreated']) ? $data['dateCreated'] : null;
        $this->residentId = !empty($data['residentId']) ? $data['residentId'] : null;
        $this->repliesId = !empty($data['repliesId']) ? $data['repliesId'] : null;
    }
}
