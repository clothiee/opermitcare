<?php

namespace Application\Portal\Service;

use Application\Opermitcare\Download\Model\DownloadTable;
use Application\Opermitcare\Faq\Form\FaqForm;
use Application\Opermitcare\Faq\Model\Faq;
use Application\Opermitcare\Faq\Model\FaqTable;
use Application\Opermitcare\FaqDetails\Form\FaqDetailsForm;
use Application\Opermitcare\FaqDetails\Model\FaqDetails;
use Application\Opermitcare\FaqDetails\Model\FaqDetailsTable;
use Application\Opermitcare\Permit\Form\PermitAssessForm;
use Application\Opermitcare\Permit\Form\PermitForm;
use Application\Opermitcare\Permit\Model\Permit;
use Application\Opermitcare\Permit\Model\PermitTable;
use Application\Opermitcare\PermitStatus\Model\PermitStatusTable;
use Application\Opermitcare\ProblemType\Form\ProblemTypeForm;
use Application\Opermitcare\ProblemType\Model\ProblemType;
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
    const SUBMIT = [
        'Reopen' => 1,
        'Assess' => 2,
        'Reject' => 3,
    ];

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
    private $faqTable;
    private $faqDetailsTable;
    private $downloadTable;

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
     * @param FaqTable          $faqTable
     * @param FaqDetailsTable   $faqDetailsTable
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
        FileService $fileService,
        FaqTable $faqTable,
        FaqDetailsTable $faqDetailsTable,
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
        $this->fileService = $fileService;
        $this->faqTable = $faqTable;
        $this->faqDetailsTable = $faqDetailsTable;
        $this->downloadTable = $downloadTable;
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
            case 'Administrator':
                $viewOptions = [
                    'templates' => $this->getDashboardTemplates(),
                    'overviewDetails' => $this->parseOverview(),
                    'tableCollection' => [
                        'user' => $this->userTable->fetchAll(),
                        'faq' => $this->faqTable->fetchAll(),
                        'faqDetails' => $this->faqDetailsTable->fetchAll(),
                        'ticketStatus' => $this->ticketStatusTable->fetchAll(),
                        'permitStatus' => $this->permitStatusTable->fetchAll(),
                        'problemType' => $this->problemTypeTable->fetchAll(),
                        'ticket' => $this->ticketTable->fetchAll(),
                        'permit' => $this->permitTable->fetchAll(),
                    ],
                    'activeTab' => 'overview',
                ];
                break;
            case 'Agent':
            case 'Moderator':
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
                    'problemType' => $this->getActiveProblemTypes(),
                    'permitStatus' => $this->getActivePermitStatuses(),
                    'tickets' => $this->getTickets(),
                    'permits' => $this->getPermits(),
                    'activeTab' => 'overview',
                ];
                break;
        }

        return array_merge($viewOptions, [
            'sessionDetails' => $sessionDetails,
        ]);
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
     * Get Downloads
     *
     * @return array
     */
    public function getDownloads()
    {
        $collection['form'] = [];
        $collection['list'] = [];
        $downloads = $this->downloadTable->getByColumns([
                                                            'active' => 1,
                                                        ]);

        foreach ($downloads as $download) {
            $download = (array) $download;
            $file = (array) $this->fileService->getByFileId($download['fileId'])[0];

            $collection[$download['type']][$download['title']] = $file['filePath'];
        }

        return [
            'form' => $collection['form'],
            'list' => $collection['list'],
        ];
    }

    /**
     * Get FAQs
     *
     * @return array
     */
    public function getFAQs()
    {
        $category = [];
        $list = [];
        $faqs = $this->faqTable->getByColumns([
                                                  'active' => 1,
                                              ]);

        foreach ($faqs as $faq) {
            $faq = (array) $faq;
            $faqDetails = $this->faqDetailsTable->getByColumns([
                                                                   'faqId' => $faq['faqId'],
                                                                   'active' => 1,
                                                               ]);
            $category[] = [
                'faqId' => $faq['faqId'],
                'faqName' => $faq['faqName'],
            ];
            $list[$faq['faqId']] = [
                'category' => $faq['faqName'],
                'list' => (array) $faqDetails,
            ];
        }

        return [
            'category' => $category,
            'list' => $list,
        ];
    }

    /**
     * Get Files
     *
     * @param int $ticketId
     *
     * @return array
     */
    private function getFiles($ticketId)
    {
        $fileCollection = [];
        $files = $this->fileService->getByTag('ticket-' . $ticketId);

        foreach ($files as $fileItem) {
            $file = (array) $fileItem;
            $fileCollection[] = [
                'fileName' => $file['fileName'],
                'fileInfo' => pathinfo($file['filePath']),
                'dateCreated' => $file['dateCreated'],
            ];
        }

        return $fileCollection;
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
     * Get Replies
     *
     * @param int $ticketId
     *
     * @return array
     */
    private function getReplies($ticketId)
    {
        $replyCollection = [];
        $replies = $this->replyTable->getByColumns([
                                                       'ticketId' => $ticketId,
                                                   ]);

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

        return $replyCollection;
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
     * Apply Permit
     *
     * @param $post
     * @param $files
     *
     * @return array
     */
    public function applyPermit($post, $files)
    {
        $validateFile = $this->fileService->validate($files['attachment']);

        if ($validateFile['code'] === FileService::INVALID_CODE) {
            $validateFile['data'] = $post;

            return $validateFile;
        }

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
                $id = $this->permitTable->save($permit);

                $response = $this->fileService->upload('permit-' . $id, $files['attachment']);
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
     * Access Permit
     *
     * @param array $post
     *
     * @return array
     */
    public function assessPermit($post)
    {
        $sessionDetails = $this->sessionService->get();
        $form = new PermitAssessForm();
        $form->setData($post);

        if (!$form->isValid()) {
            return [
                'code' => self::INVALID_CODE,
                'message' => 'Invalid Form! Remarks field is required.',
                'data' => $post,
                'activePanel' => 'permit-' . $post['permitId'],
                'activeTabAction' => 'assess',
            ];
        }

        $permit = (array) $this->permitTable->getByColumns(['permitId' => $post['permitId']])[0];
        $permit['permitStatusId'] = self::SUBMIT[$post['submit']];
        $permit['agentId'] = $sessionDetails['user']['userId'];
        $permit['mayorsFee'] = $post['mayorsFee'] ?: 0;
        $permit['licenseFee'] = $post['licenseFee'] ?: 0;
        $permit['garbageFee'] = $post['garbageFee'] ?: 0;
        $permit['zoningFee'] = $post['zoningFee'] ?: 0;
        $permit['processingFee'] = 100;
        $permit['remarks'] = sprintf('[%s] %s', date('Y-m-d H:i:s'), $post['remarks']);

        $form = new PermitForm();
        $form->setData($permit);

        if (!$form->isValid()) {
            return [
                'code' => self::INVALID_CODE,
                'message' => $form->getMessages(),
                'data' => $post,
                'activePanel' => 'permit-' . $post['permitId'],
                'activeTabAction' => 'assess',
            ];
        }

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
        $validateFile = $this->fileService->validate($files['attachment']);

        if ($validateFile['code'] === FileService::INVALID_CODE) {
            $validateFile['data'] = $post;

            return $validateFile;
        }

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

                $this->fileService->upload('ticket-' . $id, $files['attachment']);
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
     * Refresh Ticket
     *
     * @param array $post
     *
     * @return array
     */
    public function refreshTicket($post)
    {
        try {
            $sessionDetails = $this->sessionService->get();
            $lastId = $post['lastId'];
            $tickets = $this->getReplies($post['ticketId']);
            $reply = null;

            foreach ($tickets as $ticket) {
                if ($ticket['replyId'] > $lastId
                    && $ticket['senderId'] !== $sessionDetails['user']['userId']) {
                    $reply = $ticket;
                    continue;
                }
            }

            return [
                'code' => self::SUCCESS_CODE,
                'message' => self::SUCCESS_MESSAGE,
                'data' => $reply,
            ];
        } catch (\Exception $exception) {
            return [
                'code' => self::INVALID_CODE,
                'message' => $exception->getMessage(),
            ];
        }
    }

    /**
     * Reply Ticket
     *
     * @param array $post
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
     * Update Password
     *
     * @param array $post
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
     * Get Dashboard Pages
     *
     * @return array
     */
    private function getDashboardTemplates()
    {
        return [
            [
                'action' => 'overview',
                'description' => 'Manage modules.',
                'icon' => '<path d="M19 9L19 17C19 18.8856 19 19.8284 18.4142 20.4142C17.8284 21 16.8856 21 15 21L14 21L10 21L9 21C7.11438 21 6.17157 21 5.58579 20.4142C5 19.8284 5 18.8856 5 17L5 9" stroke-width="2" stroke-linejoin="round"/><path d="M3 11L7.5 7L10.6713 4.18109C11.429 3.50752 12.571 3.50752 13.3287 4.18109L16.5 7L21 11" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M10 21V17C10 15.8954 10.8954 15 12 15V15C13.1046 15 14 15.8954 14 17V21" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>',
                'table' => [],
                'title' => 'Overview',
            ],
            [
                'action' => 'report',
                'description' => 'Generate and see all reports.',
                'icon' => '<path d="M21 21H6.2C5.07989 21 4.51984 21 4.09202 20.782C3.71569 20.5903 3.40973 20.2843 3.21799 19.908C3 19.4802 3 18.9201 3 17.8V3M7 15L12 9L16 13L21 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>',
                'table' => [],
                'title' => 'Report (s)',
            ],
//            [
//                'action' => 'employee',
//                'description' => 'View Employee performance.',
//                'icon' => '<rect width="24" height="24" fill="none"/><path d="M12,2a8,8,0,0,0-8,8v1.9A2.92,2.92,0,0,0,3,14a2.88,2.88,0,0,0,1.94,2.61C6.24,19.72,8.85,22,12,22h3V20H12c-2.26,0-4.31-1.7-5.34-4.39l-.21-.55L5.86,15A1,1,0,0,1,5,14a1,1,0,0,1,.5-.86l.5-.29V11a1,1,0,0,1,1-1H17a1,1,0,0,1,1,1v5H13.91a1.5,1.5,0,1,0-1.52,2H20a2,2,0,0,0,2-2V14a2,2,0,0,0-2-2V10A8,8,0,0,0,12,2Z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>',
//                'table' => [],
//                'title' => 'Employee (s)',
//            ],
            [
                'action' => 'user',
                'description' => 'Add, disable and update Users, Residents and Employees.',
                'icon' => '<circle cx="9" cy="9" r="2" stroke-width="2"/><path d="M13 15C13 16.1046 13 17 9 17C5 17 5 16.1046 5 15C5 13.8954 6.79086 13 9 13C11.2091 13 13 13.8954 13 15Z" stroke-width="2"/><path d="M22 12C22 15.7712 22 17.6569 20.8284 18.8284C19.6569 20 17.7712 20 14 20H10C6.22876 20 4.34315 20 3.17157 18.8284C2 17.6569 2 15.7712 2 12C2 8.22876 2 6.34315 3.17157 5.17157C4.34315 4 6.22876 4 10 4H14C17.7712 4 19.6569 4 20.8284 5.17157C21.298 5.64118 21.5794 6.2255 21.748 7" stroke-width="2" stroke-linecap="round"/><path d="M19 12H15" stroke-width="2" stroke-linecap="round"/><path d="M19 9H14" stroke-width="2" stroke-linecap="round"/><path d="M19 15H16" stroke-width="2" stroke-linecap="round"/>',
                'table' => ['user'],
                'title' => 'User (s)',
            ],
            [
                'action' => 'ticket',
                'description' => 'Add and update Tickets.',
                'icon' => '<path d="M20.3116 12.6473L20.8293 10.7154C21.4335 8.46034 21.7356 7.3328 21.5081 6.35703C21.3285 5.58657 20.9244 4.88668 20.347 4.34587C19.6157 3.66095 18.4881 3.35883 16.2331 2.75458C13.978 2.15033 12.8504 1.84821 11.8747 2.07573C11.1042 2.25537 10.4043 2.65945 9.86351 3.23687C9.27709 3.86298 8.97128 4.77957 8.51621 6.44561C8.43979 6.7254 8.35915 7.02633 8.27227 7.35057L8.27222 7.35077L7.75458 9.28263C7.15033 11.5377 6.84821 12.6652 7.07573 13.641C7.25537 14.4115 7.65945 15.1114 8.23687 15.6522C8.96815 16.3371 10.0957 16.6392 12.3508 17.2435L12.3508 17.2435C14.3834 17.7881 15.4999 18.0873 16.415 17.9744C16.5152 17.9621 16.6129 17.9448 16.7092 17.9223C17.4796 17.7427 18.1795 17.3386 18.7203 16.7612C19.4052 16.0299 19.7074 14.9024 20.3116 12.6473Z" stroke-width="2"/><path d="M16.415 17.9741C16.2065 18.6126 15.8399 19.1902 15.347 19.6519C14.6157 20.3368 13.4881 20.6389 11.2331 21.2432C8.97798 21.8474 7.85044 22.1495 6.87466 21.922C6.10421 21.7424 5.40432 21.3383 4.86351 20.7609C4.17859 20.0296 3.87647 18.9021 3.27222 16.647L2.75458 14.7151C2.15033 12.46 1.84821 11.3325 2.07573 10.3567C2.25537 9.58627 2.65945 8.88638 3.23687 8.34557C3.96815 7.66065 5.09569 7.35853 7.35077 6.75428C7.77741 6.63996 8.16368 6.53646 8.51621 6.44531" stroke="#1C274C" stroke-width="2"/><path d="M11.7769 10L16.6065 11.2941" stroke-width="2" stroke-linecap="round"/><path d="M11 12.8975L13.8978 13.6739" stroke-width="2" stroke-linecap="round"/>',
                'table' => ['ticket'],
                'title' => 'Ticket (s)',
            ],
            [
                'action' => 'permit',
                'description' => 'Add and update Tickets.',
                'icon' => '<path d="M4 12V20H20V4H4V7M7 8H17M7 12H17M7 16H13" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>',
                'table' => ['permit'],
                'title' => 'Permit (s)',
            ],
            [
                'action' => 'problem-type',
                'description' => 'Add and update Problem Types.',
                'icon' => '<path d="M12 19C9.23858 19 7 16.7614 7 14M12 19C14.7614 19 17 16.7614 17 14M12 19V14M7 14V11.8571C7 11.0592 7 10.6602 7.11223 10.3394C7.31326 9.76495 7.76495 9.31326 8.33944 9.11223C8.66019 9 9.05917 9 9.85714 9H14.1429C14.9408 9 15.3398 9 15.6606 9.11223C16.2351 9.31326 16.6867 9.76495 16.8878 10.3394C17 10.6602 17 11.0592 17 11.8571V14M7 14H4M17 14H20M17 10L19.5 7.5M4.5 20.5L8 17M7 10L4.5 7.5M19.5 20.5L16 17M14 6V5C14 3.89543 13.1046 3 12 3C10.8954 3 10 3.89543 10 5V6H14Z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>',
                'table' => ['problem-type'],
                'title' => 'Problem Type (s)',
            ],
            [
                'action' => 'faq',
                'description' => 'Add and update Frequently Asked Questions.',
                'icon' => '<path d="M5,22H19a1,1,0,0,0,1-1V6.414a1,1,0,0,0-.293-.707L16.293,2.293A1,1,0,0,0,15.586,2H5A1,1,0,0,0,4,3V21A1,1,0,0,0,5,22Zm8-5a1,1,0,0,1-2,0V16a1,1,0,0,1,2,0ZM10.127,5.682a2.927,2.927,0,0,1,2.418-.631,3.084,3.084,0,0,1,2.409,2.52,3.142,3.142,0,0,1-1.79,3.421.407.407,0,0,0-.164.359V12a1,1,0,0,1-2,0v-.649A2.359,2.359,0,0,1,12.363,9.16,1.144,1.144,0,0,0,12.981,7.9a1.067,1.067,0,0,0-.8-.879.913.913,0,0,0-.775.2,1.155,1.155,0,0,0-.4.9,1,1,0,1,1-2,0A3.151,3.151,0,0,1,10.127,5.682Z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>',
                'table' => ['faq', 'faq-details',],
                'title' => 'FAQ (s)',
            ],
//            [
//                'action' => 'forms-and-list',
//                'description' => 'Add and update Downloadable Forms and Lists.',
//                'icon' => '<path d="M17 9.00195C19.175 9.01406 20.3529 9.11051 21.1213 9.8789C22 10.7576 22 12.1718 22 15.0002V16.0002C22 18.8286 22 20.2429 21.1213 21.1215C20.2426 22.0002 18.8284 22.0002 16 22.0002H8C5.17157 22.0002 3.75736 22.0002 2.87868 21.1215C2 20.2429 2 18.8286 2 16.0002L2 15.0002C2 12.1718 2 10.7576 2.87868 9.87889C3.64706 9.11051 4.82497 9.01406 7 9.00195" stroke-width="2" stroke-linecap="round"/><path d="M12 2L12 15M12 15L9 11.5M12 15L15 11.5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>',
//                'table' => ['download'],
//                'title' => 'Downloadable (s)',
//            ],
//            [
//                'action' => 'press-release',
//                'description' => 'Add and update Press Release or Latest News.',
//                'icon' => '<path d="M22 7.99992V11.9999M10.25 5.49991H6.8C5.11984 5.49991 4.27976 5.49991 3.63803 5.82689C3.07354 6.11451 2.6146 6.57345 2.32698 7.13794C2 7.77968 2 8.61976 2 10.2999L2 11.4999C2 12.4318 2 12.8977 2.15224 13.2653C2.35523 13.7553 2.74458 14.1447 3.23463 14.3477C3.60218 14.4999 4.06812 14.4999 5 14.4999V18.7499C5 18.9821 5 19.0982 5.00963 19.1959C5.10316 20.1455 5.85441 20.8968 6.80397 20.9903C6.90175 20.9999 7.01783 20.9999 7.25 20.9999C7.48217 20.9999 7.59826 20.9999 7.69604 20.9903C8.64559 20.8968 9.39685 20.1455 9.49037 19.1959C9.5 19.0982 9.5 18.9821 9.5 18.7499V14.4999H10.25C12.0164 14.4999 14.1772 15.4468 15.8443 16.3556C16.8168 16.8857 17.3031 17.1508 17.6216 17.1118C17.9169 17.0756 18.1402 16.943 18.3133 16.701C18.5 16.4401 18.5 15.9179 18.5 14.8736V5.1262C18.5 4.08191 18.5 3.55976 18.3133 3.2988C18.1402 3.05681 17.9169 2.92421 17.6216 2.88804C17.3031 2.84903 16.8168 3.11411 15.8443 3.64427C14.1772 4.55302 12.0164 5.49991 10.25 5.49991Z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>',
//                'table' => [],
//                'title' => 'Press Release (s)',
//            ],
//            [
//                'action' => 'setting',
//                'description' => 'Add and update Website pages.',
//                'icon' => '<path d="M15.0505 9H5.5C4.11929 9 3 7.88071 3 6.5C3 5.11929 4.11929 4 5.5 4H15.0505M8.94949 20H18.5C19.8807 20 21 18.8807 21 17.5C21 16.1193 19.8807 15 18.5 15H8.94949M3 17.5C3 19.433 4.567 21 6.5 21C8.433 21 10 19.433 10 17.5C10 15.567 8.433 14 6.5 14C4.567 14 3 15.567 3 17.5ZM21 6.5C21 8.433 19.433 10 17.5 10C15.567 10 14 8.433 14 6.5C14 4.567 15.567 3 17.5 3C19.433 3 21 4.567 21 6.5Z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>',
//                'table' => [],
//                'title' => 'Settings',
//            ],
        ];
    }

    private function parseOverview()
    {
        $ticketStatusCategory = [];
        $problemTypeCategory = [];
        $permitStatusCategory = [];
        $assessedPermitsCategory = [];
        $ticketStatusList = [];
        $permitStatusList = [];
        $problemTypeList = [];
        $assessedPermitsList = [];

        $tickets = $this->ticketTable->fetchAll();
        $permits = $this->permitTable->fetchAll();

        foreach ($tickets as $ticket) {
            $ticket = (array) $ticket;

            if(!in_array($ticket['ticketStatusId'], $ticketStatusCategory)) {
                $ticketStatusCategory[$ticket['ticketStatusId']] = (array) $this->ticketStatusTable->getByColumns(['ticketStatusId' => $ticket['ticketStatusId']])[0];
            }

            if(!in_array($ticket['problemTypeId'], $problemTypeCategory)) {
                $problemTypeCategory[$ticket['problemTypeId']] =  (array) $this->problemTypeTable->getByColumns(['problemTypeId' => $ticket['problemTypeId']])[0];
            }

            $ticketStatusList[$ticket['ticketStatusId']][] = $ticket;
            $problemTypeList[$ticket['problemTypeId']][] = $ticket;
        }

        foreach ($ticketStatusCategory as $ticketStatusId => $ticketStatus) {
            $ticketStatus['count'] = count($ticketStatusList[$ticketStatusId]);
            $ticketStatusCategory[$ticketStatusId] = $ticketStatus;
        }

        foreach ($problemTypeCategory as $problemTypeId => $problemType) {
            $problemType['count'] = count($problemTypeList[$problemTypeId]);
            $problemTypeCategory[$problemTypeId] = $problemType;
        }

        foreach ($permits as $permit) {
            $permit = (array) $permit;

            if(!in_array($permit['permitStatusId'], $permitStatusCategory)) {
                $permitStatusCategory[$permit['permitStatusId']] = (array) $this->permitStatusTable->getByColumns(['permitStatusId' => $permit['permitStatusId']])[0];
            }

            if ($permit['permitStatusId'] === 2) {
                $user = (array) $this->userTable->getByColumns(['userId' => $permit['agentId']])[0];
                $assessedPermitsList[$permit['agentId']][] =  [
                    'permitId' => $permit['permitId'],
                    'userId' => $user['userId'],
                    'firstName' => $user['firstName'],
                    'lastName' => $user['lastName'],
                    'email' => $user['email'],
                    'userName' => $user['userName'],
                ];
            }

            $permitStatusList[$permit['permitStatusId']][] = $permit;
        }

        foreach ($permitStatusCategory as $permitStatusId => $permitStatus) {
            $permitStatus['count'] = count($permitStatusList[$permitStatusId]);
            $permitStatusCategory[$permitStatusId] = $permitStatus;
        }

        foreach ($assessedPermitsList as $agentId => $assessed) {
            $assessed['count'] = count($assessedPermitsList[$agentId]);
            $assessedPermitsCategory[$agentId] = [
                'userId' => $assessed[0]['userId'],
                'firstName' => $assessed[0]['firstName'],
                'lastName' => $assessed[0]['lastName'],
                'email' => $assessed[0]['email'],
                'userName' => $assessed[0]['userName'],
                'count' => count($assessedPermitsList[$agentId]),
            ];
        }

        return [
            'ticketStatus' => $ticketStatusCategory,
            'problemType' => $problemTypeCategory,
            'permitStatus' => $permitStatusCategory,
            'assessedPermitsList' => $assessedPermitsCategory,
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
            $data['replies'] = $this->getReplies($data['ticketId']);
            $data['files'] = $this->getFiles($data['ticketId']);

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
            $files = $this->fileService->getByTag('permit-' . $data['permitId']);
            $fileCollection = [];

            if (!empty($user[0])) {
                $agent = (array) $user[0];
                $data['agentId'] = $agent;
            }

            foreach ($files as $fileItem) {
                $file = (array) $fileItem;
                $fileCollection[] = [
                    'fileName' => $file['fileName'],
                    'fileInfo' => pathinfo($file['filePath']),
                    'dateCreated' => $file['dateCreated'],
                ];
            }

            $data['files'] = $fileCollection;

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

    public function updateUser($post)
    {
        $form = new UserForm();
        $form->setData($post);

        if ($form->isValid()) {
            try {
                $user = new User();
                $user->exchangeArray($post);
                $this->userTable->save($user);
                return [
                    'code' => self::SUCCESS_CODE,
                    'message' => self::SUCCESS_MESSAGE,
                ];
            } catch (\Exception $exception) {
                return [
                    'code' => SessionService::INVALID_CODE,
                    'message' => $exception->getMessage(),
                ];
            }
        }

        return [
            'code' => self::INVALID_CODE,
            'message' => self::INVALID_MESSAGE,
        ];
    }

    public function updateProblemType($post)
    {
        $form = new ProblemTypeForm();
        $form->setData($post);

        if ($form->isValid()) {
            try {
                $problemType = new ProblemType();
                $problemType->exchangeArray($post);
                $this->problemTypeTable->save($problemType);
                return [
                    'code' => self::SUCCESS_CODE,
                    'message' => self::SUCCESS_MESSAGE,
                ];
            } catch (\Exception $exception) {
                return [
                    'code' => SessionService::INVALID_CODE,
                    'message' => $exception->getMessage(),
                ];
            }
        }

        return [
            'code' => self::INVALID_CODE,
            'message' => self::INVALID_MESSAGE,
        ];
    }

    public function updateFaq($post)
    {
        $form = new FaqForm();
        $form->setData($post);

        if ($form->isValid()) {
            try {
                $faq = new Faq();
                $faq->exchangeArray($post);
                $this->faqTable->save($faq);
                return [
                    'code' => self::SUCCESS_CODE,
                    'message' => self::SUCCESS_MESSAGE,
                ];
            } catch (\Exception $exception) {
                return [
                    'code' => SessionService::INVALID_CODE,
                    'message' => $exception->getMessage(),
                ];
            }
        }

        return [
            'code' => self::INVALID_CODE,
            'message' => self::INVALID_MESSAGE,
        ];
    }

    public function updateFaqDetails($post)
    {
        $form = new FaqDetailsForm();
        $form->setData($post);

        if ($form->isValid()) {
            try {
                $faqDetails = new FaqDetails();
                $faqDetails->exchangeArray($post);
                $this->faqDetailsTable->save($faqDetails);
                return [
                    'code' => self::SUCCESS_CODE,
                    'message' => self::SUCCESS_MESSAGE,
                ];
            } catch (\Exception $exception) {
                return [
                    'code' => SessionService::INVALID_CODE,
                    'message' => $exception->getMessage(),
                ];
            }
        }

        return [
            'code' => self::INVALID_CODE,
            'message' => self::INVALID_MESSAGE,
        ];
    }

    public function updateTicket($post)
    {
        $oldTicket = (array) $this->ticketTable->getByColumns(['ticketId' => $post['ticketId']])[0];
        $oldTicket['ticketStatusId'] = $post['ticketStatusId'];

        $form = new TicketForm();
        $form->setData($oldTicket);

        if ($form->isValid()) {
            try {
                $ticket = new Ticket();
                $ticket->exchangeArray($oldTicket);
                $this->ticketTable->save($ticket);
                return [
                    'code' => self::SUCCESS_CODE,
                    'message' => self::SUCCESS_MESSAGE,
                ];
            } catch (\Exception $exception) {
                return [
                    'code' => SessionService::INVALID_CODE,
                    'message' => $exception->getMessage(),
                ];
            }
        }

        return [
            'code' => self::INVALID_CODE,
            'message' => self::INVALID_MESSAGE,
        ];
    }

    public function updatePermit($post)
    {
        $oldPermit = (array) $this->permitTable->getByColumns(['permitId' => $post['permitId']])[0];
        $oldPermit['permitStatusId'] = $post['permitStatusId'];

        try {
            $permit = new Permit();
            $permit->exchangeArray($oldPermit);
            $this->permitTable->save($permit);
            return [
                'code' => self::SUCCESS_CODE,
                'message' => self::SUCCESS_MESSAGE,
            ];
        } catch (\Exception $exception) {
            return [
                'code' => SessionService::INVALID_CODE,
                'message' => $exception->getMessage(),
            ];
        }
    }
}