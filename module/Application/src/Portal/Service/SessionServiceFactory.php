<?php

namespace Application\Portal\Service;

use Application\Opermitcare\User\Model\UserTable;
use Application\Opermitcare\UserType\Model\UserTypeTable;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class SessionServiceFactory
{
    /**
     * @param ContainerInterface $container
     * @param                    $requestedName
     * @param array|null         $options
     *
     * @return SessionService
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $config = $container->get('config');
        $userTable = $container->get(UserTable::class);
        $userTypeTable = $container->get(UserTypeTable::class);

        return new SessionService(
            $config,
            $userTable,
            $userTypeTable,
        );
    }
}
