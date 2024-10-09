<?php

namespace Utilities;

class Module
{
    /**
     * Get Module Config
     *
     * @return array
     */
    public function getConfig() : array
    {
        /** @var array $config */
        $config = include __DIR__ . '/../config/module.config.php';

        return $config;
    }

    /**
     * Get Service Config
     *
     * @return array
     */
    public function getServiceConfig() : array
    {
        /** @var array $serviceConfig */
        $serviceConfig = include __DIR__ . '/../config/service.config.php';

        return $serviceConfig;
    }
}
