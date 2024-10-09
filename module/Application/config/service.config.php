<?php

namespace Application;

use Application\Portal\Service\SessionService;
use Application\Portal\Service\SessionServiceFactory;
use Application\User\Model\UserTable;
use Application\User\Model\UserTableFactory;

$table = [
    UserTable::class => UserTableFactory::class,
];

$service = [
    SessionService::class => SessionServiceFactory::class,
];

return [
    'factories' => array_merge($table, $service),
];
