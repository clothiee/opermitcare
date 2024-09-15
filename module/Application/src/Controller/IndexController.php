<?php

namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        $this->layout()->setVariables([
                                          'layoutVars' => 'layoutVariables',
                                          'test' => true,
                                      ]);

        return new ViewModel([
                                 'viewVars' => 'viewVariables',
                                 'test' => true,
                             ]);
    }

    public function loginAction()
    {
        return new ViewModel([
                                 'viewVars' => 'viewVariables',
                                 'test' => true,
                             ]);
    }
}
