<?php

namespace Application\Reply\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;

class ReplyTable
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

    public function save(Reply $reply)
    {
        $data = [
            'replyId' => $reply->replyId,
            'ticketId' => $reply->ticketId,
            'senderId' => $reply->senderId,
            'message' => $reply->message,
            'dateCreated' => $reply->dateCreated,
        ];

        $replyId = (int) $reply->replyId;

        if ($replyId === 0) {
            $this->tableGateway->insert($data);
            return;
        }

        try {
            $this->getByColumns(['replyId' => $replyId]);
        } catch (RuntimeException $e) {
            throw new RuntimeException(sprintf(
                                           'Cannot update user with identifier %d; does not exist',
                                           $replyId
                                       ));
        }

        $this->tableGateway->update($data, ['replyId' => $replyId]);
    }

    public function delete($replyId)
    {
        $this->tableGateway->delete(['replyId' => (int) $replyId]);
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
