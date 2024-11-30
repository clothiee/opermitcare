<?php

namespace Application\Portal\Service;

use Application\Opermitcare\Download\Model\DownloadTable;
use Application\Opermitcare\Faq\Model\FaqTable;
use Application\Opermitcare\FaqDetails\Model\FaqDetailsTable;
use Application\Opermitcare\Permit\Model\PermitTable;
use Application\Opermitcare\PermitStatus\Model\PermitStatusTable;
use Application\Opermitcare\ProblemType\Model\ProblemTypeTable;
use Application\Opermitcare\Reply\Model\ReplyTable;
use Application\Opermitcare\Ticket\Model\TicketTable;
use Application\Opermitcare\TicketStatus\Model\TicketStatusTable;
use Application\Opermitcare\User\Model\UserTable;
use Application\Opermitcare\UserType\Model\UserTypeTable;
use ArrayObject;

class DataTableService
{
    private $config;
    private $sessionService;
    private $userTable;
    private $userTypeTable;
    private $problemTypeTable;
    private $ticketTable;
    private $ticketStatusTable;
    private $replyTable;
    private $permitTable;
    private $permitStatusTable;
    private $faqTable;
    private $faqDetailsTable;
    private $fileService;
    private $downloadTable;

    /**
     * TableService constructor.
     *
     * @param ArrayObject       $config
     * @param SessionService    $sessionService
     * @param UserTable         $userTable
     * @param UserTypeTable     $userTypeTable
     * @param ProblemTypeTable  $problemTypeTable
     * @param TicketTable       $ticketTable
     * @param TicketStatusTable $ticketStatusTable
     * @param ReplyTable        $replyTable
     * @param PermitTable       $permitTable
     * @param PermitStatusTable $permitStatusTable
     * @param FaqTable          $faqTable
     * @param FaqDetailsTable   $faqDetailsTable
     * @param FileService       $fileService
     * @param DownloadTable     $downloadTable
     */
    public function __construct(
        $config,
        SessionService $sessionService,
        UserTable $userTable,
        UserTypeTable $userTypeTable,
        ProblemTypeTable $problemTypeTable,
        TicketTable $ticketTable,
        TicketStatusTable $ticketStatusTable,
        ReplyTable $replyTable,
        PermitTable $permitTable,
        PermitStatusTable $permitStatusTable,
        FaqTable $faqTable,
        FaqDetailsTable $faqDetailsTable,
        FileService $fileService,
        DownloadTable $downloadTable
    ) {
        $this->config = $config;
        $this->sessionService = $sessionService;
        $this->userTable = $userTable;
        $this->userTypeTable = $userTypeTable;
        $this->problemTypeTable = $problemTypeTable;
        $this->ticketTable = $ticketTable;
        $this->ticketStatusTable = $ticketStatusTable;
        $this->replyTable = $replyTable;
        $this->permitTable = $permitTable;
        $this->permitStatusTable = $permitStatusTable;
        $this->faqTable = $faqTable;
        $this->faqDetailsTable = $faqDetailsTable;
        $this->fileService = $fileService;
        $this->downloadTable = $downloadTable;
    }

    /**
     * Initialize
     *
     * @param $tableName
     *
     * @return array
     */
    public function initialize($tableName)
    {
        switch ($tableName) {
            case 'download':
                return $this->getDownload();
            case 'faq':
                return $this->getFaq();
            case 'faq-details':
                return $this->getFaqDetails();
            case 'ticket':
                return $this->getTicket();
            case 'user':
                return $this->getUser();
            case 'permit':
                return $this->getPermit();
            case 'problem-type':
                return $this->getProblemType();
            default:
                return [];
        }
    }

    /**
     * Get Download
     *
     * @return array
     */
    private function getDownload()
    {
        $table = $this->downloadTable->fetchAll();
        $dataTable = [];

        foreach ($table as $row) {
            $data = (array) $row;
            $dataTable[] = [
                'Id' => $data['downloadId'],
                'Title' => $data['title'],
                'Type' => ucwords($data['type']),
                'Active' => $data['active'] ? 'True' : 'False',
                'Action' => $data['downloadId'],
            ];
        }

        return $this->parseDataTable($dataTable);
    }

    /**
     * Get Faq
     *
     * @return array
     */
    private function getFaq()
    {
        $table = $this->faqTable->fetchAll();
        $dataTable = [];

        foreach ($table as $row) {
            $data = (array) $row;
            $dataTable[] = [
                'Id' => $data['faqId'],
                'Category' => $data['faqName'],
                'Active' => $data['active'] ? 'True' : 'False',
                'Action' => $data['faqId'],
            ];
        }

        return $this->parseDataTable($dataTable);
    }

    /**
     * Get Faq Details
     *
     * @return array
     */
    private function getFaqDetails()
    {
        $table = $this->faqDetailsTable->fetchAll();
        $dataTable = [];

        foreach ($table as $row) {
            $data = (array) $row;
            $faq = (array) $this->faqTable->getByColumns(['faqId' => $data['faqId']])[0];
            $dataTable[] = [
                'Category' => $faq['faqName'],
                'Title' => $data['title'],
                'Active' => $data['active'] ? 'True' : 'False',
                'Action' => $data['faqDetailsId'],
            ];
        }

        return $this->parseDataTable($dataTable);
    }

    /**
     * Get Problem Type
     *
     * @return array
     */
    private function getProblemType()
    {
        $table = $this->problemTypeTable->fetchAll();
        $dataTable = [];

        foreach ($table as $row) {
            $data = (array) $row;
            $dataTable[] = [
                'Id' => $data['problemTypeId'],
                'Title' => $data['problemTypeName'],
                'Active' => $data['active'] ? 'True' : 'False',
                'Action' => $data['problemTypeId'],
            ];
        }

        return $this->parseDataTable($dataTable);
    }

    /**
     * Get Ticket
     *
     * @return array
     */
    private function getTicket()
    {
        $table = $this->ticketTable->fetchAll();
        $dataTable = [];

        foreach ($table as $row) {
            $data = (array) $row;
            $ticketStatus = (array) $this->ticketStatusTable->getByColumns(['ticketStatusId' => $data['ticketStatusId']])[0];
            $problemType = (array) $this->problemTypeTable->getByColumns(['problemTypeId' => $data['problemTypeId']])[0];
            $dataTable[] = [
                'Id' => sprintf('%s%06d', date("Y", strtotime($data['dateCreated'])), $data['ticketId']),
                'Problem Type' => $problemType['problemTypeName'],
                'Status' => ucwords($ticketStatus['ticketStatusName']),
                'Date Created' => date("d M Y", strtotime($data['dateCreated'])),
                'Action' => $data['ticketId'],
            ];
        }

        return $this->parseDataTable($dataTable);
    }

    /**
     * Get User Table
     *
     * @return array
     */
    private function getUser()
    {
        $table = $this->userTable->fetchAll();
        $dataTable = [];

        foreach ($table as $row) {
            $data = (array) $row;
            $userType = (array) $this->userTypeTable->getByUserTypeId(['userTypeId' => $data['userTypeId']]);
            $dataTable[] = [
                'Id' => $data['userId'],
                'Email' => $data['email'],
                'Username' => $data['userName'],
                'User Type' => $userType['userTypeName'],
                'Action' => $data['userId'],
            ];
        }

        return $this->parseDataTable($dataTable);
    }

    /**
     * Get Permit
     *
     * @return array
     */
    private function getPermit()
    {
        $table = $this->permitTable->fetchAll();
        $dataTable = [];

        foreach ($table as $row) {
            $data = (array) $row;
            $permitStatus = (array) $this->permitStatusTable->getByColumns(['permitStatusId' => $data['permitStatusId']])[0];
            $dataTable[] = [
                'Id' => sprintf('%s%06d', date("Y", strtotime($data['dateCreated'])), $data['permitId']),
                'Trade Name' => $data['tradeName'],
                'Status' => ucwords($permitStatus['permitStatusName']),
                'Date Created' => date("d M Y", strtotime($data['dateCreated'])),
                'Action' => $data['permitId'],
            ];
        }

        return $this->parseDataTable($dataTable);
    }

    /**
     * Parse Data Table
     *
     * @param array $table
     *
     * @return array
     */
    private function parseDataTable($table = [])
    {
        $data = [];

        foreach ($table as $columnKey => $row) {
            foreach ($row as $rowKey => $rowValue) {
                $data[$columnKey][] = (string) $rowValue;
            }
        }

        return $data;
    }
}
