<?php

namespace Application\Portal\Service;

use Application\Opermitcare\File\Model\FileTable;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class FileServiceFactory
{
    /**
     * @param ContainerInterface $container
     * @param                    $requestedName
     * @param array|null         $options
     *
     * @return FileService
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $config = $container->get('config');
        $fileTable = $container->get(FileTable::class);

        return new FileService(
            $config,
            $fileTable,
        );
    }
}
