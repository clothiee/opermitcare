<?php

namespace Application;

use Application\Portal\Controller\PortalController;
use Application\Portal\Controller\PortalControllerFactory;
use Laminas\Db\Adapter\AdapterAbstractServiceFactory;
use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'home' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/',
                    'defaults' => [
                        'controller' => PortalController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'portal' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/[:action]',
                    'defaults' => [
                        'controller' => PortalController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'find-a-form' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/find-a-form',
                    'defaults' => [
                        'controller' => PortalController::class,
                        'action' => 'find-a-form',
                    ],
                ],
            ],
            'application' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/application[/:action]',
                    'defaults' => [
                        'controller' => PortalController::class,
                        'action' => 'index',
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            PortalController::class => PortalControllerFactory::class,
        ],
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_path_stack' => [
            'portal' => __DIR__ . '/../view',
        ],
        'template_map' => [
            'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
            'error/404' => __DIR__ . '/../view/error/404.phtml',
            'error/index' => __DIR__ . '/../view/error/index.phtml',
        ],
        'strategies' => [
            'ViewJsonStrategy',
        ],
    ],
    'service_manager' => [
        'factories' => [
            'Application\Db\WriteAdapter' => AdapterAbstractServiceFactory::class,
        ],
    ],
];
