<?php

namespace Application\Portal\Controller;

use Application\Portal\Service\SessionService;
use ArrayObject;
use Laminas\Http\Response;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class PortalController extends AbstractActionController
{
    private $config;
    private $sessionService;

    /**
     * PortalController constructor.
     *
     * @param ArrayObject  $config
     * @param SessionService $sessionService
     */
    public function __construct(
        $config,
        SessionService $sessionService
    ) {
        $this->config = $config;
        $this->sessionService = $sessionService;
    }

    /**
     * Index Page
     *
     * @return ViewModel
     */
    public function indexAction()
    {
        $viewOptions = [];

        return $this->buildView($viewOptions);
    }

    /**
     * Login Page
     *
     * @return Response|ViewModel
     */
    public function loginAction()
    {
        $request = $this->getRequest();
        $sessionDetails = $this->sessionService->get();
        $viewOptions = [];

        if (!empty($sessionDetails['profile'])) {
            return $this->redirectTo('portal', 'dashboard');
        }

        if ($request->isPost()) {
            $post = $this->getRequest()->getPost()->toArray();
            $response = $this->sessionService->initialize($post);

            if ($response['response']['message'] === SessionService::SUCCESS_MESSAGE) {
                return $this->redirectTo('portal', 'dashboard');
            }

            $viewOptions['response'] = $response;
        }

        return $this->buildView($viewOptions);
    }

    /**
     * Logout Page
     *
     * @return Response
     */
    public function logoutAction()
    {
        $this->sessionService->delete();

        return $this->redirectTo('portal', 'login');
    }

    /**
     * Sign Up Page
     *
     * @return ViewModel
     */
    public function signUpAction()
    {
        $sessionDetails = $this->sessionService->get();
        $viewOptions = [];

        if (!empty($sessionDetails['profile'])) {
            return $this->redirectTo('portal', 'dashboard');
        }

        return $this->buildView($viewOptions);
    }

    /**
     * Find A Form Page
     *
     * @return ViewModel
     */
    public function findAFormAction()
    {
        $list = [];
        $forms = [
            'New and Renewal application Forms' => '',
            'Certification Form' => '',
            'Individual - Mayor\'s Permit Form' => '',
            'Business Additional Forms' => '',
            'Amendment Form' => '',
        ];

        return $this->buildView([
            'formCollection' => $forms,
            'listCollection' => $list,
        ]);
    }

    /**
     * Dashboard Page
     *
     * @return ViewModel
     */
    public function dashboardAction()
    {
        $viewOptions = [];

        return $this->buildView($viewOptions);
    }

    /**
     * Redirect To
     *
     * @param string $route
     * @param string $action
     * @param bool   $query
     *
     * @return Response
     */
    private function redirectTo(string $route, string $action, $query = true)
    {
        return $this->redirect()->toRoute($route, ['action' => $action], $query);
    }

    /**
     * Build View
     *
     * @param array $viewOptions
     *
     * @return ViewModel
     */
    private function buildView(array $viewOptions)
    {
        $action = $this->params()->fromRoute('action');
        $sessionDetails = $this->sessionService->get();

        $this->layout()->setVariable('layoutVariables', [
            'pageName' => $action,
            'sessionDetails' => $sessionDetails,
        ]);

        return new ViewModel($viewOptions);
    }

    public function getUserAction()
    {
        return $this->sessionService->getUserDetails();
    }
}
