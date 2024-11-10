<?php

namespace Application\Portal\Service;

use Application\ProblemType\Model\ProblemTypeTable;
use Application\Reply\Model\ReplyTable;
use Application\Ticket\Model\TicketTable;
use Application\TicketStatus\Model\TicketStatusTable;
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
        $sessionService = $container->get(SessionService::class);
        $userTable = $container->get(UserTable::class);
        $userTypeTable = $container->get(UserTypeTable::class);
        $problemTypeTable = $container->get(ProblemTypeTable::class);
        $ticketTable = $container->get(TicketTable::class);
        $ticketStatusTable = $container->get(TicketStatusTable::class);
        $replyTable = $container->get(ReplyTable::class);

        return new DashboardService(
            $config,
            $sessionService,
            $userTable,
            $userTypeTable,
            $problemTypeTable,
            $ticketTable,
            $ticketStatusTable,
            $replyTable
        );
    }
}
