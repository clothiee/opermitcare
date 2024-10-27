<?php

namespace Application\Ticket\Model;

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
        $data = [];

        foreach ($rowSet as $row) {
            $data[] = $row;
        }

        return $data;
    }

    public function getByColumns($columns)
    {
        $rowSet = $this->tableGateway->select($columns);
        $data = [];

        foreach ($rowSet as $row) {
            $data[] = $row;
        }

        return $data;
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
            'repliesId' => $ticket->repliesId,
        ];

        $ticketId = (int) $ticket->ticketId;

        if ($ticketId === 0) {
            $this->tableGateway->insert($data);
            return;
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
}
