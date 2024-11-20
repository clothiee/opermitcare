<?php

namespace Application\Portal\Service;

use Application\Opermitcare\Permit\Form\PermitAssessForm;
use Application\Opermitcare\Permit\Form\PermitForm;
use Application\Opermitcare\Permit\Model\Permit;
use Application\Opermitcare\Permit\Model\PermitTable;
use Application\Opermitcare\PermitStatus\Model\PermitStatusTable;
use Application\Opermitcare\ProblemType\Model\ProblemTypeTable;
use Application\Opermitcare\Reply\Form\ReplyForm;
use Application\Opermitcare\Reply\Model\Reply;
use Application\Opermitcare\Reply\Model\ReplyTable;
use Application\Opermitcare\Ticket\Form\TicketForm;
use Application\Opermitcare\Ticket\Model\Ticket;
use Application\Opermitcare\Ticket\Model\TicketTable;
use Application\Opermitcare\TicketStatus\Model\TicketStatusTable;
use Application\Opermitcare\User\Form\UserForm;
use Application\Opermitcare\User\Model\User;
use Application\Opermitcare\User\Model\UserTable;
use Application\Opermitcare\UserType\Model\UserTypeTable;
use ArrayObject;

class DashboardService
{
    const SUCCESS_CODE = 200;
    const SUCCESS_MESSAGE = 'Success';
    const INVALID_CODE = 401;
    const INVALID_MESSAGE = 'Invalid request';
    const SUBMIT_ACCESS = 'Assess';

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
    private $fileService;

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
     * @param FileService       $fileService
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
        FileService $fileService
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
        $this->fileService = $fileService;
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
            case 'Agent':
                $viewOptions = [
                    'problemType' => $this->getActiveProblemTypes(),
                    'permitStatus' => $this->getActivePermitStatuses(),
                    'tickets' => $this->getTickets(),
                    'permits' => $this->getPermits(),
                    'recentTickets' => $this->getRecentTickets(),
                    'recentPermits' => $this->getRecentPermits(),
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

    /**
     * Get Permits
     *
     * @return array
     */
    public function getPermits()
    {
        $sessionDetails = $this->sessionService->get();
        $permitStatuses = $this->permitStatusTable->fetchAll();
        $permits = $sessionDetails['userType']['userTypeId'] === 4
            ? $this->permitTable->getByColumns([
                                                   'residentId' => $sessionDetails['user']['userId'],
                                               ])
            : $this->permitTable->fetchAll();

        return $this->parsePermits($permits, $permitStatuses);
    }

    /**
     * Get Tickets
     *
     * @return array
     */
    public function getTickets()
    {
        $sessionDetails = $this->sessionService->get();
        $ticketStatuses = $this->ticketStatusTable->fetchAll();
        $tickets = $sessionDetails['userType']['userTypeId'] === 4
            ? $this->ticketTable->getByColumns([
                                                   'residentId' => $sessionDetails['user']['userId'],
                                               ])
            : $this->ticketTable->fetchAll();

        return $this->parseTickets($tickets, $ticketStatuses);
    }

    /**
     * Get Recent Permits
     *
     * @return array
     */
    public function getRecentPermits()
    {
        $sessionDetails = $this->sessionService->get();
        $permitStatuses = $this->permitStatusTable->fetchAll();
        $tickets = $this->permitTable->getByColumns([
                                                        'agentId' => $sessionDetails['user']['userId'],
                                                    ]);

        return $this->parsePermits($tickets, $permitStatuses);
    }

    /**
     * Get Recent Tickets
     *
     * @return array
     */
    public function getRecentTickets()
    {
        $sessionDetails = $this->sessionService->get();
        $ticketStatuses = $this->ticketStatusTable->fetchAll();
        $tickets = $this->ticketTable->getRecentByUserId($sessionDetails['user']['userId']);

        return $this->parseTickets($tickets, $ticketStatuses);
    }

    /**
     * Apply Permit
     *
     * @param $post
     *
     * @return array
     */
    public function applyPermit($post)
    {
        $sessionDetails = $this->sessionService->get();

        $post['residentId'] = $sessionDetails['user']['userId'];
        $post['permitStatusId'] = 1;
        $post['dateCreated'] = date('Y-m-d H:i:s');

        if (empty($post['block']) || empty($post['lot']) || empty($post['street']) || empty($post['barangay'])) {
            return [
                'code' => self::INVALID_CODE,
                'message' => 'Please complete the address.',
                'data' => $post,
            ];
        }

        $house = !empty($post['house']) ? ' ' . $post['house'] : '';
        $addressLine1 = $post['block'] . ' ' . $post['lot'] . $house;
        $addressLine2 = ' ' . $post['street'] . ' ' . $post['barangay'];
        $post['businessAddress'] = sprintf('%s%s Las Piñas City', $addressLine1, $addressLine2);

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
                    'data' => $post,
                ];
            }

            return [
                'code' => self::SUCCESS_CODE,
                'message' => 'Your request was successfully submitted!',
                'data' => [],
            ];
        }

        return [
            'code' => self::INVALID_CODE,
            'message' => $form->getMessages(),
            'data' => $post,
        ];
    }

    /**
     * Create Ticket
     *
     * @param $post
     * @param $files
     *
     * @return array
     */
    public function createTicket($post, $files)
    {
        $sessionDetails = $this->sessionService->get();

        $post['residentId'] = $sessionDetails['user']['userId'];
        $post['ticketStatusId'] = 1;
        $post['dateCreated'] = date('Y-m-d H:i:s');

        $form = new TicketForm();
        $form->setData($post);

        if ($form->isValid()) {
            try {
                $ticket = new Ticket();
                $ticket->exchangeArray($post);
                $id = $this->ticketTable->save($ticket);

                $this->fileService->upload('ticket-'.$id, $files['attachment']);
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

    /**
     * Reply Ticket
     *
     * @param $post
     *
     * @return array
     */
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

                $ticket = (array) $this->ticketTable->getByColumns(['ticketId' => $post['ticketId']])[0];

                $ticket['ticketStatusId'] = $post['ticketStatusId'];
                $updateTicket = new Ticket();
                $updateTicket->exchangeArray($ticket);
                $this->ticketTable->save($updateTicket);
            } catch (\Exception $exception) {
                return [
                    'code' => self::INVALID_CODE,
                    'message' => $exception->getMessage(),
                ];
            }

            $ticketStatus = (array) $this->ticketStatusTable->getByColumns([
                                                                         'ticketStatusId' => $post['ticketStatusId'],
                                                                     ])[0];

            return [
                'code' => self::SUCCESS_CODE,
                'message' => 'Message sent successfully!',
                'ticketStatus' => $ticketStatus,
                'userType' => $sessionDetails['userType'],
                'dateCreated' => date("H:i a", strtotime($post['dateCreated'])),
            ];
        }

        return [
            'code' => self::INVALID_CODE,
            'message' => $form->getMessages(),
        ];
    }

    /**
     * Access Permit
     *
     * @param $post
     *
     * @return array
     */
    public function accessPermit($post)
    {
        $sessionDetails = $this->sessionService->get();
        $form = new PermitAssessForm($post);
        $form->setData($post);

        if ($form->isValid()) {
            $permit = (array) $this->permitTable->getByColumns(['permitId' => $post['permitId']])[0];
            $permit['permitStatusId'] = $post['submit'] === self::SUBMIT_ACCESS ? 2 : 3;
            $permit['agentId'] = $sessionDetails['user']['userId'];
            $permit['mayorsFee'] = $post['mayorsFee'] ?: 0;
            $permit['licenseFee'] = $post['licenseFee'] ?: 0;
            $permit['garbageFee'] = $post['garbageFee'] ?: 0;
            $permit['zoningFee'] = $post['zoningFee'] ?: 0;
            $permit['processingFee'] = 100;
            $permit['remarks'] = sprintf('[%s] %s', date('Y-m-d H:i:s'), $post['remarks']) ;
        } else {
            return [
                'code' => self::INVALID_CODE,
                'message' => 'Invalid Form! Remarks field is required.',
                'data' => $post,
                'activePanel' => 'permit-' . $post['permitId'],
                'activeTabAction' => 'assess',
            ];
        }

        $form = new PermitForm();
        $form->setData($permit);

        if ($form->isValid()) {
            try {
                $updatePermit = new Permit();
                $updatePermit->exchangeArray($permit);
                $this->permitTable->save($updatePermit);
            } catch (\Exception $exception) {
                return [
                    'code' => self::INVALID_CODE,
                    'message' => $exception->getMessage(),
                    'data' => $post,
                    'activePanel' => 'permit-' . $post['permitId'],
                    'activeTabAction' => 'assess',
                ];
            }

            return [
                'code' => self::SUCCESS_CODE,
                'message' => 'Updated permit successfully!',
                'data' => $permit,
                'activePanel' => 'permit-' . $post['permitId'],
                'activeTabAction' => 'overview',
            ];
        }

        return [
            'code' => self::INVALID_CODE,
            'message' => $form->getMessages(),
            'data' => $post,
            'activePanel' => 'permit-' . $post['permitId'],
            'activeTabAction' => 'assess',
        ];
    }

    /**
     * Update Password
     *
     * @param $post
     *
     * @return array
     */
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

    /**
     * Get Active Problem Types
     *
     * @return array
     */
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

    /**
     * Get Active Permit Statuses
     *
     * @return array
     */
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

    /**
     * Get Dashboard Pages
     *
     * @return array
     */
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

    /**
     * Parse Tickets
     *
     * @param $tickets
     * @param $ticketStatuses
     *
     * @return array
     */
    private function parseTickets($tickets, $ticketStatuses)
    {
        $statusCollection = [];

        foreach ($ticketStatuses as $ticketStatus) {
            $statusCollection[$ticketStatus->ticketStatusId] = $ticketStatus->ticketStatusName;
        }

        $collection = [];

        foreach ($tickets as $ticket) {
            $data = (array) $ticket;
            $data['ticketStatusName'] = $statusCollection[$data['ticketStatusId']];
            $replies = $this->replyTable->getByColumns([
                                                           'ticketId' => $data['ticketId'],
                                                       ]);
            $files = $this->fileService->getByTag('ticket-'.$data['ticketId']);
            $replyCollection = [];
            $fileCollection = [];

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

            foreach ($files as $fileItem) {
                $file = (array) $fileItem;
                $fileCollection[] = [
                    'fileName' => $file['fileName'],
                    'fileInfo' => pathinfo($file['filePath']),
                    'dateCreated' => $file['dateCreated'],
                ];
            }

            $data['replies'] = $replyCollection;
            $data['files'] = $fileCollection;

            $newId = sprintf('%s%06d', date("Y", strtotime($data['dateCreated'])), $data['ticketId']);
            $collection[$newId] = $data;
        }

        krsort($collection);

        return $collection;
    }

    /**
     * Parse Permits
     *
     * @param $permits
     * @param $permitStatuses
     *
     * @return array
     */
    private function parsePermits($permits, $permitStatuses)
    {
        $statusCollection = [];

        foreach ($permitStatuses as $permitStatus) {
            $statusCollection[$permitStatus->permitStatusId] = $permitStatus->permitStatusName;
        }

        $collection = [];

        foreach ($permits as $permit) {
            $data = (array) $permit;
            $data['permitStatusName'] = $statusCollection[$data['permitStatusId']];
            $user = $this->userTable->getByColumns([
                                                                 'userId' => $data['agentId'],
                                                             ]);
            if (!empty($user[0])) {
                $agent = (array) $user[0];
                $data['agentId'] = [
                    'userId' => $agent['userId'],
                    'userName' => $agent['userName'],
                    'firstName' => $agent['firstName'],
                    'lastName' => $agent['lastName'],
                    'email' => $agent['email'],
                    'userTypeId' => $agent['userTypeId'],
                ];
            }

            $newId = sprintf('%s%06d', date("Y", strtotime($data['dateCreated'])), $data['permitId']);
            $collection[$newId] = $data;
        }

        krsort($collection);

        return $collection;
    }

    /**
     * Validate Password
     *
     * @param $password
     * @param $currentPassword
     * @param $newPassword
     * @param $confirmPassword
     *
     * @return array|string
     */
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
