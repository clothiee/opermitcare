<?php

namespace Application\Portal\Service;

use Application\ProblemType\Model\ProblemTypeTable;
use Application\Ticket\Form\TicketForm;
use Application\Ticket\Model\Ticket;
use Application\Ticket\Model\TicketTable;
use Application\TicketStatus\Model\TicketStatusTable;
use Application\User\Model\UserTable;
use Application\UserType\Model\UserTypeTable;
use ArrayObject;
use Laminas\Validator\Date;

class DashboardService
{
    const SUCCESS_CODE = 200;
    const SUCCESS_MESSAGE = 'Success';
    const INVALID_CODE = 401;
    const INVALID_MESSAGE = 'Invalid username or password';

    private $config;
    private $sessionService;
    private $userTable;
    private $userTypeTable;
    private $problemTypeTable;
    private $ticketTable;
    private $ticketStatusTable;

    /**
     * Dashboard Service constructor.
     *
     * @param ArrayObject       $config
     * @param SessionService    $sessionService
     * @param UserTable         $userTable
     * @param UserTypeTable     $userTypeTable
     * @param ProblemTypeTable  $problemTypeTable
     * @param TicketTable       $ticketTable
     * @param TicketStatusTable $ticketStatusTable
     */
    public function __construct(
        $config,
        SessionService $sessionService,
        UserTable $userTable,
        UserTypeTable $userTypeTable,
        ProblemTypeTable $problemTypeTable,
        TicketTable $ticketTable,
        TicketStatusTable $ticketStatusTable
    ) {
        $this->config = $config;
        $this->sessionService = $sessionService;
        $this->userTable = $userTable;
        $this->userTypeTable = $userTypeTable;
        $this->problemTypeTable = $problemTypeTable;
        $this->ticketTable = $ticketTable;
        $this->ticketStatusTable = $ticketStatusTable;
    }

    /**
     * Initialize
     *
     * @return array
     */
    public function initialize()
    {
        $sessionDetails = $this->sessionService->get();

        switch ($sessionDetails['userType']['userTypeName']) {
            case 'Resident':
                $viewOptions = [
                    'problemType' => $this->getActiveProblemTypes(),
                    'tickets' => $this->getTickets(),
                    'activeTab' => 'overview',
                ];
                break;
            default:
                $viewOptions = [
                    'pages' => $this->getDashboardPages(),
                ];
                break;
        }

        return array_merge($viewOptions, [
            'sessionDetails' => $sessionDetails,

        ]);
    }

    private function getDashboardPages()
    {
        return [
            [
                'title' => 'Settings',
                'description' => 'Add, delete and update Website pages.',
                'action' => 'setting',
            ],
            [
                'title' => 'Users',
                'description' => 'Add, delete and update Users, Residents and Employees.',
                'action' => 'user',
            ],
            [
                'title' => 'Employees',
                'description' => 'View Employees performances.',
                'action' => 'employee',
            ],
            [
                'title' => 'FAQs',
                'description' => 'Add, delete and update Frequently Asked Questions.',
                'action' => 'faq',
            ],
            [
                'title' => 'Problem Types',
                'description' => 'Add, delete and update Problem Types.',
                'action' => 'problem-type',
            ],
            [
                'title' => 'Tickets',
                'description' => 'Add, delete and update Tickets.',
                'action' => 'ticket',
            ],
            [
                'title' => 'Forms and List',
                'description' => 'Add, delete and update Downloadable Forms and Lists.',
                'action' => 'forms-and-list',
            ],
            [
                'title' => 'Press Release',
                'description' => 'Add, delete and update Press Release or Latest News.',
                'action' => 'press-release',
            ],
            [
                'title' => 'Reports',
                'description' => 'Generate and see all reports.',
                'action' => 'report',
            ],
        ];
    }

    private function getActiveProblemTypes()
    {
        $problemTypes = $this->problemTypeTable->getByColumns(['active' => 1]);
        $collection = [];

        foreach ($problemTypes as $problemType) {
            $collection[] = [
                'id' => $problemType->problemTypeId,
                'name' => $problemType->problemTypeName,
            ];
        }

        return $collection;
    }

    public function getTickets()
    {
        $sessionDetails = $this->sessionService->get();
        $columns = [
            'residentId' => $sessionDetails['user']['userId'],
        ];
        $ticketStatus = $this->ticketStatusTable->fetchAll();
        $statusCollection = [];

        foreach ($ticketStatus as $ticketStatus) {
            $statusCollection[$ticketStatus->ticketStatusId] = $ticketStatus->ticketStatusName;
        }

        $tickets = $this->ticketTable->getByColumns($columns);
        $collection = [];

        foreach ($tickets as $ticket) {
            $data = (array) $ticket;
            $data['ticketStatusName'] = $statusCollection[$data['ticketStatusId']];
            $newId = sprintf('%s%06d', date("Y", strtotime($data['dateCreated'])), $data['ticketId']);
            // date("d M Y", strtotime($data['dateCreated']));
            $collection[$newId] = $data;
        }

        krsort($collection);

        return $collection;
    }

    public function createTicket($post)
    {
        $sessionDetails = $this->sessionService->get();

        $post['residentId'] = $sessionDetails['user']['userId'];
        $post['ticketStatusId'] = 2;
        $post['dateCreated'] = date('Y-m-d');

        $form = new TicketForm();
        $form->setData($post);

        if ($form->isValid()) {
            try {
                $ticket = new Ticket();
                $ticket->exchangeArray($post);
                $this->ticketTable->save($ticket);
            } catch (\Exception $exception) {
                return [
                    'code' => self::INVALID_CODE,
                    'message' => $exception->getMessage(),
                ];
            }

            return [
                'code' => self::SUCCESS_CODE,
                'message' => 'Your request was successfully submitted!',
            ];
        }

        return [
            'code' => self::INVALID_CODE,
            'message' => $form->getMessages(),
        ];
    }
}
