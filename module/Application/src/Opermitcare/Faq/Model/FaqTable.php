<?php

namespace Application\Opermitcare\Faq\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;

class FaqTable
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

    public function save(Faq $faq)
    {
        $data = [
            'faqName' => $faq->faqName,
            'active' => $faq->active,
        ];

        $faqId = (int) $faq->faqId;

        if ($faqId === 0) {
            $this->tableGateway->insert($data);
            return;
        }

        try {
            $this->getByColumns(['faqId' => $faqId]);
        } catch (RuntimeException $e) {
            throw new RuntimeException(sprintf(
                                           'Cannot update user with identifier %d; does not exist',
                                           $faqId
                                       ));
        }

        $this->tableGateway->update($data, ['faqId' => $faqId]);
    }

    public function delete($faqId)
    {
        $this->tableGateway->delete(['faqId' => (int) $faqId]);
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
