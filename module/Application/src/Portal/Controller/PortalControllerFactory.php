<?php

namespace Application\Portal\Controller;

use Application\Portal\Service\SessionService;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class PortalControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return PortalController
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null) {
        $config = $container->get('config');
        $sessionService = $container->get(SessionService::class);

        return new PortalController(
            $config,
            $sessionService
        );
    }
}
