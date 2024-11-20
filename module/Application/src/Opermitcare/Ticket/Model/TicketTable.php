<?php

namespace Application\Opermitcare\Ticket\Model;

use Laminas\Db\Sql\Select;
use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;

class TicketTable
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

    public function save(Ticket $ticket)
    {
        $data = [
            'ticketId' => $ticket->ticketId,
            'ticketStatusId' => $ticket->ticketStatusId,
            'problemTypeId' => $ticket->problemTypeId,
            'title' => $ticket->title,
            'description' => $ticket->description,
            'dateCreated' => $ticket->dateCreated,
            'residentId' => $ticket->residentId,
        ];

        $ticketId = (int) $ticket->ticketId;

        if ($ticketId === 0) {
            $this->tableGateway->insert($data);
            return $this->tableGateway->getLastInsertValue();
        }

        try {
            $this->getByColumns(['ticketId' => $ticketId]);
        } catch (RuntimeException $e) {
            throw new RuntimeException(sprintf(
                                           'Cannot update user with identifier %d; does not exist',
                                           $ticketId
                                       ));
        }

        $this->tableGateway->update($data, ['ticketId' => $ticketId]);
    }

    public function delete($ticketId)
    {
        $this->tableGateway->delete(['ticketId' => (int) $ticketId]);
    }

    public function getRecentByUserId($userId)
    {
        $table = $this->tableGateway->getTable();
        $select = new Select($table);
        $select->join('reply', 'reply.ticketId = ' . $table . '.ticketId', ['*'], $select::JOIN_LEFT)
               ->where([
                           'reply.senderId' => $userId,
                       ])
               ->group($table . '.ticketId');
        $rowSet = $this->tableGateway->selectWith($select);

        return $this->parseRow($rowSet);
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
