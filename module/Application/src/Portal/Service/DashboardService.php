<?php

namespace Application\Portal\Service;

use Application\User\Model\UserTable;
use Application\UserType\Model\UserTypeTable;
use ArrayObject;

class DashboardService
{
    const SUCCESS_CODE = 200;
    const SUCCESS_MESSAGE = 'Success';
    const INVALID_CODE = 401;
    const INVALID_MESSAGE = 'Invalid username or password';

    private $config;
    private $userTable;
    private $userTypeTable;
    private $sessionService;

    /**
     * Dashboard Service constructor.
     *
     * @param ArrayObject    $config
     * @param UserTable      $userTable
     * @param UserTypeTable  $userTypeTable
     * @param SessionService $sessionService
     */
    public function __construct(
        $config,
        UserTable $userTable,
        UserTypeTable $userTypeTable,
        SessionService $sessionService
    ) {
        $this->config = $config;
        $this->userTable = $userTable;
        $this->sessionService = $sessionService;
    }

    /**
     * Initialize
     *
     * @return array
     */
    public function initialize()
    {
        $viewOptions = [
            'sessionDetails' => $this->sessionService->get(),
            'pages' => $this->getDashboardPages(),
        ];

        return $viewOptions;
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
}
