<?php

declare(strict_types=1);

namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\View\Model\JsonModel;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        // We do backend here. bitch

        $this->layout()->setVariables([
            'layoutVars' => 'XOXOXOXOXOX',
            'test' => 'XXXXXX',
        ]);

        return new ViewModel([
            'viewVars' => 'LLLLLL',
            'test' => true,
        ]);
    }
}
