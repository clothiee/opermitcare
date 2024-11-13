<?php

namespace Application\Portal\Service;

use Application\Permit\Form\PermitForm;
use Application\Permit\Model\Permit;
use Application\Permit\Model\PermitTable;
use Application\PermitStatus\Model\PermitStatusTable;
use Application\ProblemType\Model\ProblemTypeTable;
use Application\Reply\Form\ReplyForm;
use Application\Reply\Model\Reply;
use Application\Reply\Model\ReplyTable;
use Application\Ticket\Form\TicketForm;
use Application\Ticket\Model\Ticket;
use Application\Ticket\Model\TicketTable;
use Application\TicketStatus\Model\TicketStatusTable;
use Application\User\Form\UserForm;
use Application\User\Model\User;
use Application\User\Model\UserTable;
use Application\UserType\Model\UserTypeTable;
use ArrayObject;

class DashboardService
{
    const SUCCESS_CODE = 200;
    const SUCCESS_MESSAGE = 'Success';
    const INVALID_CODE = 401;
    const INVALID_MESSAGE = 'Invalid request';

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
     * @param ReplyTable        $replyTable
     * @param PermitTable       $permitTable
     * @param PermitStatusTable $permitStatusTable
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
        PermitStatusTable $permitStatusTable
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
                    'permitStatus' => $this->getActivePermitStatuses(),
                    'tickets' => $this->getTickets(),
                    'permits' => $this->getPermits(),
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

    public function getPermits()
    {
        $sessionDetails = $this->sessionService->get();
        $permitStatuses = $this->permitStatusTable->fetchAll();
        $statusCollection = [];

        foreach ($permitStatuses as $permitStatus) {
            $statusCollection[$permitStatus->permitStatusId] = $permitStatus->permitStatusName;
        }

        $permits = $this->permitTable->getByColumns([
                                                        'residentId' => $sessionDetails['user']['userId'],
                                                    ]);
        $collection = [];

        foreach ($permits as $permit) {
            $data = (array) $permit;
            $data['permitStatusName'] = $statusCollection[$data['permitStatusId']];
            $newId = sprintf('%s%06d', date("Y", strtotime($data['dateCreated'])), $data['permitId']);
            $collection[$newId] = $data;
        }

        krsort($collection);

        return $collection;
    }

    public function getTickets()
    {
        $sessionDetails = $this->sessionService->get();
        $ticketStatus = $this->ticketStatusTable->fetchAll();
        $statusCollection = [];

        foreach ($ticketStatus as $ticketStatus) {
            $statusCollection[$ticketStatus->ticketStatusId] = $ticketStatus->ticketStatusName;
        }

        $tickets = $this->ticketTable->getByColumns([
                                                        'residentId' => $sessionDetails['user']['userId'],
                                                    ]);
        $collection = [];

        foreach ($tickets as $ticket) {
            $data = (array) $ticket;
            $data['ticketStatusName'] = $statusCollection[$data['ticketStatusId']];
            $replies = $this->replyTable->getByColumns([
                                                           'ticketId' => $data['ticketId'],
                                                       ]);
            $replyCollection = [];

            foreach ($replies as $replyKey => $replyItem) {
                $reply = (array) $replyItem;
                $sender = (array) $this->userTable->getByColumns([
                                                                     'userId' => $reply['senderId'],
                                                                 ])[0];
                $replyCollection[$replyKey] = $reply;
                $replyCollection[$replyKey]['sender'] = [
                    'userId' => $sender['userId'],
                    'userName' => $sender['userName'],
                    'firstName' => $sender['firstName'],
                    'lastName' => $sender['lastName'],
                    'email' => $sender['email'],
                    'userTypeId' => $sender['userTypeId'],
                ];
                $replyCollection[$replyKey]['userTypeId'] = $this->userTypeTable->getByUserTypeId($sender['userTypeId']);
            }

            $data['replies'] = $replyCollection;

            $newId = sprintf('%s%06d', date("Y", strtotime($data['dateCreated'])), $data['ticketId']);
            $collection[$newId] = $data;
        }

        krsort($collection);

        return $collection;
    }

    public function applyPermit($post)
    {
        $sessionDetails = $this->sessionService->get();

        $post['residentId'] = $sessionDetails['user']['userId'];
        $post['permitStatusId'] = 1;
        $post['dateCreated'] = date('Y-m-d H:i:s');

        $form = new PermitForm();
        $form->setData($post);

        if ($form->isValid()) {
            try {
                $permit = new Permit();
                $permit->exchangeArray($post);
                $this->permitTable->save($permit);
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

    public function createTicket($post)
    {
        $sessionDetails = $this->sessionService->get();

        $post['residentId'] = $sessionDetails['user']['userId'];
        $post['ticketStatusId'] = 2;
        $post['dateCreated'] = date('Y-m-d H:i:s');

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

    public function replyTicket($post)
    {
        $sessionDetails = $this->sessionService->get();

        $post['senderId'] = $sessionDetails['user']['userId'];
        $post['dateCreated'] = date('Y-m-d H:i:s');

        $form = new ReplyForm();
        $form->setData($post);

        if ($form->isValid()) {
            try {
                $reply = new Reply();
                $reply->exchangeArray($post);
                $this->replyTable->save($reply);
            } catch (\Exception $exception) {
                return [
                    'code' => self::INVALID_CODE,
                    'message' => $exception->getMessage(),
                ];
            }

            return [
                'code' => self::SUCCESS_CODE,
                'message' => 'Message sent successfully!',
            ];
        }

        return [
            'code' => self::INVALID_CODE,
            'message' => $form->getMessages(),
        ];
    }

    public function updatePassword($post)
    {
        $sessionDetails = $this->sessionService->get();
        $userData = $sessionDetails['user'];

        $validatePassword = $this->validatePassword(
            $userData['password'],
            trim($post['currentPassword']),
            trim($post['newPassword']),
            trim($post['confirmNewPassword'])
        );

        if (!empty($validatePassword)) {
            return [
                'code' => self::INVALID_CODE,
                'message' => !is_array($validatePassword) ? $validatePassword : 'Invalid Password! Please try again.',
                'data' => is_array($validatePassword) ? $validatePassword : [],
            ];
        }

        $userData['password'] = $post['newPassword'];

        $form = new UserForm();
        $form->setData($userData);

        if ($form->isValid() && !count($validatePassword)) {
            try {
                $user = new User();
                $user->exchangeArray($userData);
                $this->userTable->save($user);
            } catch (\Exception $exception) {
                return [
                    'code' => self::INVALID_CODE,
                    'message' => $exception->getMessage(),
                ];
            }

            return [
                'code' => self::SUCCESS_CODE,
                'message' => 'Your profile has been updated!',
            ];
        }

        return [
            'code' => self::INVALID_CODE,
            'message' => $form->getMessages(),
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

    private function getActivePermitStatuses()
    {
        $permitStatuses = $this->permitStatusTable->getByColumns(['active' => 1]);
        $collection = [];

        foreach ($permitStatuses as $permitStatus) {
            $collection[] = [
                'id' => $permitStatus->permitStatusId,
                'name' => $permitStatus->permitStatusName,
            ];
        }

        return $collection;
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

    private function validatePassword($password, $currentPassword, $newPassword, $confirmPassword)
    {
        $errors = [];

        if ($password !== $currentPassword) {
            $errors = 'Invalid current password.';
            return $errors;
        }

        if ($newPassword !== $confirmPassword) {
            $errors = 'New password and confirm password doesn\'t match.';
            return $errors;
        }

        if (!preg_match("/\d/", $newPassword)) {
            $errors[] = 'INVALID_ONE_DIGIT';
        }

        if (!preg_match("/[A-Z]/", $newPassword)) {
            $errors[] = 'INVALID_ONE_UPPER';
        }

        if (!preg_match("/[a-z]/", $newPassword)) {
            $errors[] = 'INVALID_ONE_LOWER';
        }

        if (strlen($newPassword) < 6 || strlen($newPassword) > 10) {
            $errors[] = 'INVALID_LENGTH';
        }

        return $errors;
    }
}
