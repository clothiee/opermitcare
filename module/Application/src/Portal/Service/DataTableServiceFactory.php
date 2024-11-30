<?php

namespace Application\Portal\Service;

use Application\Opermitcare\Download\Model\Download;
use Application\Opermitcare\Download\Model\DownloadTable;
use Application\Opermitcare\Faq\Model\FaqTable;
use Application\Opermitcare\FaqDetails\Model\FaqDetailsTable;
use Application\Opermitcare\Permit\Model\PermitTable;
use Application\Opermitcare\PermitStatus\Model\PermitStatusTable;
use Application\Opermitcare\ProblemType\Model\ProblemTypeTable;
use Application\Opermitcare\Reply\Model\ReplyTable;
use Application\Opermitcare\Ticket\Model\TicketTable;
use Application\Opermitcare\TicketStatus\Model\TicketStatusTable;
use Application\Opermitcare\User\Model\UserTable;
use Application\Opermitcare\UserType\Model\UserTypeTable;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class DataTableServiceFactory
{
    /**
     * @param ContainerInterface $container
     * @param                    $requestedName
     * @param array|null         $options
     *
     * @return DataTableService
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        return new DataTableService(
            $container->get('config'),
            $container->get(SessionService::class),
            $container->get(UserTable::class),
            $container->get(UserTypeTable::class),
            $container->get(ProblemTypeTable::class),
            $container->get(TicketTable::class),
            $container->get(TicketStatusTable::class),
            $container->get(ReplyTable::class),
            $container->get(PermitTable::class),
            $container->get(PermitStatusTable::class),
            $container->get(FaqTable::class),
            $container->get(FaqDetailsTable::class),
            $container->get(FileService::class),
            $container->get(DownloadTable::class),
        );
    }
}
