<?php

namespace Application\Reply\Model;

class Reply
{
    /** @var int $replyId */
    public $replyId;
    /** @var int $ticketId */
    public $ticketId;
    /** @var int $senderId */
    public $senderId;
    /** @var string $message */
    public $message;
    /** @var string|null $dateCreated */
    public $dateCreated;

    public function exchangeArray($data)
    {
        $this->replyId = !empty($data['replyId']) ? $data['replyId'] : null;
        $this->ticketId = !empty($data['ticketId']) ? $data['ticketId'] : null;
        $this->senderId= !empty($data['senderId']) ? $data['senderId'] : null;
        $this->message = !empty($data['message']) ? $data['message'] : null;
        $this->dateCreated = !empty($data['dateCreated']) ? $data['dateCreated'] : null;
    }
}
