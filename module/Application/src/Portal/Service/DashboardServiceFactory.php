<?php

namespace Application\Portal\Service;

use Application\User\Model\UserTable;
use Application\UserType\Model\UserTypeTable;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class DashboardServiceFactory
{
    /**
     * @param ContainerInterface $container
     * @param                    $requestedName
     * @param array|null         $options
     *
     * @return DashboardService
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $config = $container->get('config');
        $userTable = $container->get(UserTable::class);
        $userTypeTable = $container->get(UserTypeTable::class);
        $sessionService = $container->get(SessionService::class);

        return new DashboardService(
            $config,
            $userTable,
            $userTypeTable,
            $sessionService
        );
    }
}
