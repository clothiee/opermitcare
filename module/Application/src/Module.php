<?php

namespace Application;

use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\TableGateway\TableGateway;
use Laminas\Mvc\ModuleRouteListener;
use Laminas\Mvc\MvcEvent;
use Laminas\Session\Config\SessionConfig;
use Laminas\Session\Container;
use Laminas\Session\SaveHandler\DbTableGateway;
use Laminas\Session\SaveHandler\DbTableGatewayOptions;
use Laminas\Session\SessionManager;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

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

    /**
     * On Bootstrap
     *
     * @param MvcEvent $event
     */
    public function onBootstrap(MvcEvent $event)
    {
        $eventManager = $event->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $eventManager->attach(MvcEvent::EVENT_ROUTE, [$this, 'initSession'], 1);
        $eventManager->attach(MvcEvent::EVENT_ROUTE, [$this, 'checkLogin'], 1);
    }

    /**
     * Initialize Session
     *
     * @param MvcEvent $event
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function initSession(MvcEvent $event)
    {
        $serviceManager = $event->getApplication()->getServiceManager();
        $dbAdapter = $serviceManager->get(AdapterInterface::class);
        $tableGateway = new TableGateway('session', $dbAdapter);
        $saveHandler = new DbTableGateway($tableGateway, new DbTableGatewayOptions());
        $sessionConfig = new SessionConfig();
        $sessionConfig->setOptions([
                                       'use_cookies' => true,
                                       'cookie_httponly' => true,
                                       'remember_me_seconds' => 86400,
                                       'gc_maxlifetime' => 86400,
                                       'cookie_lifetime' => 86400,
                                   ]);
        $sessionManager = new SessionManager($sessionConfig);
        $sessionManager->setSaveHandler($saveHandler);
        $sessionManager->start();
        Container::setDefaultManager($sessionManager);
        $serviceManager->setService('SessionManager', $sessionManager);
    }

    public function checkLogin(MvcEvent $event)
    {
        $request = $event->getRequest();
        $response = $event->getResponse();
        $session = new Container('User');

        if ($session->offsetExists ('username') && $session->offsetExists ('email')) {
            $url = '/dashboard';
            $response->setHeaders($response->getHeaders ()->addHeaderLine('Location', $url));
            $response->setStatusCode(302);
            $response->sendHeaders();
            die;
        }

        else return;
    }
}
