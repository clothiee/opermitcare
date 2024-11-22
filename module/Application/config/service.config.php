<?php

namespace Application;

use Application\Opermitcare\Faq\Model\FaqTable;
use Application\Opermitcare\Faq\Model\FaqTableFactory;
use Application\Opermitcare\FaqDetails\Model\FaqDetailsTable;
use Application\Opermitcare\FaqDetails\Model\FaqDetailsTableFactory;
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
use Application\Portal\Service\FileService;
use Application\Portal\Service\FileServiceFactory;
use Application\Portal\Service\SessionService;
use Application\Portal\Service\SessionServiceFactory;

$table = [
    FaqTable::class => FaqTableFactory::class,
    FaqDetailsTable::class => FaqDetailsTableFactory::class,
    FileTable::class => FileTableFactory::class,
    PermitTable::class => PermitTableFactory::class,
    PermitStatusTable::class => PermitStatusTableFactory::class,
    ProblemTypeTable::class => ProblemTypeTableFactory::class,
    ReplyTable::class => ReplyTableFactory::class,
    TicketTable::class => TicketTableFactory::class,
    TicketStatusTable::class => TicketStatusTableFactory::class,
    UserTable::class => UserTableFactory::class,
    UserTypeTable::class => UserTypeTableFactory::class,
];

$service = [
    DashboardService::class => DashboardServiceFactory::class,
    FileService::class => FileServiceFactory::class,
    SessionService::class => SessionServiceFactory::class,
];

return [
    'factories' => array_merge($table, $service),
];
