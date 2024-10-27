<?php

namespace Application\TicketStatus\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;

class TicketStatusTable
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

    public function save(TicketStatus $ticketStatus)
    {
        $data = [
            'problemTypeName' => $ticketStatus->ticketStatusName,
            'active' => $ticketStatus->active,
        ];

        $ticketStatusId = (int) $ticketStatus->ticketStatusId;

        if ($ticketStatusId === 0) {
            $this->tableGateway->insert($data);
            return;
        }

        try {
            $this->getByColumns(['ticketStatusId' => $ticketStatusId]);
        } catch (RuntimeException $e) {
            throw new RuntimeException(sprintf(
                                           'Cannot update user with identifier %d; does not exist',
                                           $ticketStatusId
                                       ));
        }

        $this->tableGateway->update($data, ['ticketStatusId' => $ticketStatusId]);
    }

    public function delete($ticketStatusId)
    {
        $this->tableGateway->delete(['ticketStatusId' => (int) $ticketStatusId]);
    }
}
