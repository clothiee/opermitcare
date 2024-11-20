<?php

namespace Application;

use Application\Opermitcare\File\Model\FileTable;
use Application\Opermitcare\File\Model\FileTableFactory;
use Application\Opermitcare\Permit\Model\PermitTable;
use Application\Opermitcare\Permit\Model\PermitTableFactory;
use Application\Opermitcare\PermitStatus\Model\PermitStatusTable;
use Application\Opermitcare\PermitStatus\Model\PermitStatusTableFactory;
use Application\Opermitcare\ProblemType\Model\ProblemTypeTable;
use Application\Opermitcare\ProblemType\Model\ProblemTypeTableFactory;
use Application\Opermitcare\Reply\Model\ReplyTable;
use Application\Opermitcare\Reply\Model\ReplyTableFactory;
use Application\Opermitcare\Ticket\Model\TicketTable;
use Application\Opermitcare\Ticket\Model\TicketTableFactory;
use Application\Opermitcare\TicketStatus\Model\TicketStatusTable;
use Application\Opermitcare\TicketStatus\Model\TicketStatusTableFactory;
use Application\Opermitcare\User\Model\UserTable;
use Application\Opermitcare\User\Model\UserTableFactory;
use Application\Opermitcare\UserType\Model\UserTypeTable;
use Application\Opermitcare\UserType\Model\UserTypeTableFactory;
use Application\Portal\Service\DashboardService;
use Application\Portal\Service\DashboardServiceFactory;
use Application\Portal\Service\SessionService;
use Application\Portal\Service\SessionServiceFactory;

$table = [
    FileTable::class => FileTableFactory::class,
    PermitTable::class => PermitTableFactory::class,
    PermitStatusTable::class => PermitStatusTableFactory::class,
    ProblemTypeTable::class => ProblemTypeTableFactory::class,
    TicketTable::class => TicketTableFactory::class,
    TicketStatusTable::class => TicketStatusTableFactory::class,
    UserTable::class => UserTableFactory::class,
    UserTypeTable::class => UserTypeTableFactory::class,
    ReplyTable::class => ReplyTableFactory::class,
];

$service = [
    SessionService::class => SessionServiceFactory::class,
    DashboardService::class => DashboardServiceFactory::class,
];

return [
    'factories' => array_merge($table, $service),
];
