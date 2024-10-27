<?php

namespace Application;

use Application\Portal\Service\DashboardService;
use Application\Portal\Service\DashboardServiceFactory;
use Application\Portal\Service\SessionService;
use Application\Portal\Service\SessionServiceFactory;
use Application\ProblemType\Model\ProblemTypeTable;
use Application\ProblemType\Model\ProblemTypeTableFactory;
use Application\Ticket\Model\TicketTable;
use Application\Ticket\Model\TicketTableFactory;
use Application\TicketStatus\Model\TicketStatusTable;
use Application\TicketStatus\Model\TicketStatusTableFactory;
use Application\User\Model\UserTable;
use Application\User\Model\UserTableFactory;
use Application\UserType\Model\UserTypeTable;
use Application\UserType\Model\UserTypeTableFactory;

$table = [
    UserTable::class => UserTableFactory::class,
    UserTypeTable::class => UserTypeTableFactory::class,
    ProblemTypeTable::class => ProblemTypeTableFactory::class,
    TicketTable::class => TicketTableFactory::class,
    TicketStatusTable::class => TicketStatusTableFactory::class,
];

$service = [
    SessionService::class => SessionServiceFactory::class,
    DashboardService::class => DashboardServiceFactory::class,
];

return [
    'factories' => array_merge($table, $service),
];
