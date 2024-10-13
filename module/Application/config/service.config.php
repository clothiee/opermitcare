<?php

namespace Application;

use Application\Portal\Service\DashboardService;
use Application\Portal\Service\DashboardServiceFactory;
use Application\Portal\Service\SessionService;
use Application\Portal\Service\SessionServiceFactory;
use Application\User\Model\UserTable;
use Application\User\Model\UserTableFactory;
use Application\UserType\Model\UserTypeTable;
use Application\UserType\Model\UserTypeTableFactory;

$table = [
    UserTable::class => UserTableFactory::class,
    UserTypeTable::class => UserTypeTableFactory::class,
];

$service = [
    SessionService::class => SessionServiceFactory::class,
    DashboardService::class => DashboardServiceFactory::class,
];

return [
    'factories' => array_merge($table, $service),
];
