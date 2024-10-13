<?php

namespace Application\Portal\Controller;

use Application\Portal\Service\DashboardService;
use Application\Portal\Service\SessionService;
use Application\User\Form\UserForm;
use ArrayObject;
use Laminas\Http\Response;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class PortalController extends AbstractActionController
{
    use PortalTrait;

    private $config;
    private $sessionService;
    private $dashboardService;

    /**
     * PortalController constructor.
     *
     * @param ArrayObject      $config
     * @param SessionService   $sessionService
     * @param DashboardService $dashboardService
     */
    public function __construct(
        $config,
        SessionService $sessionService,
        DashboardService $dashboardService
    ) {
        $this->config = $config;
        $this->sessionService = $sessionService;
        $this->dashboardService = $dashboardService;
    }

    /**
     * Index Page
     *
     * @return ViewModel
     */
    public function indexAction()
    {
        return $this->buildView();
    }

    /**
     * Login Page
     *
     * @return Response|ViewModel
     */
    public function loginAction()
    {
        return $this->buildView();
    }

    /**
     * Logout Page
     *
     * @return Response
     */
    public function logoutAction()
    {
        return $this->buildView();
    }

    /**
     * Sign Up Page
     *
     * @return Response|ViewModel
     */
    public function signUpAction()
    {
        return $this->buildView();
    }

    /**
     * Dashboard Page
     *
     * @return ViewModel
     */
    public function dashboardAction()
    {
        return $this->buildView();
    }

    /**
     * Find A Form Page
     *
     * @return ViewModel
     */
    public function findAFormAction()
    {
        return $this->buildView();
    }

    /**
     * Build View
     *
     * @return Response|ViewModel
     */
    private function buildView()
    {
        $action = $this->params()->fromRoute('action');
        $sessionDetails = $this->sessionService->get();

        $this->layout()->setVariable('layoutVariables', [
            'pageName' => $action,
            'isProfileAvailable' => empty($sessionDetails['user']) && empty($sessionDetails['userType']),
        ]);

        return $this->initialize($action);
    }

    /**
     * Initialize Pages
     *
     * @param string $action
     *
     * @return Response|ViewModel
     */
    public function initialize(string $action)
    {
        switch ($action) {
            case 'login':
                return $this->login();
            case 'logout':
                return $this->logout();
            case 'dashboard':
                return $this->dashboard();
            case 'find-a-form':
                return $this->findAForm();
            case 'sign-up':
                return $this->signUp();
            default:
                return new ViewModel();
        }
    }

    /**
     * Login
     *
     * @return Response|ViewModel
     */
    private function login()
    {
        $request = $this->getRequest();
        $sessionDetails = $this->sessionService->get();
        $viewOptions = [];

        if (!empty($sessionDetails['user'])) {
            return $this->redirectTo('portal', 'dashboard');
        }

        if ($request->isPost()) {
            $post = $request->getPost()->toArray();
            $response = $this->sessionService->initialize($post);

            if ($response['message'] === SessionService::SUCCESS_MESSAGE) {
                return $this->redirectTo('portal', 'dashboard');
            }

            $viewOptions['response'] = $response;
        }

        return new ViewModel($viewOptions);
    }

    /**
     * Logout
     *
     * @return Response
     */
    private function logout()
    {
        $this->sessionService->delete();

        return $this->redirectTo('portal', 'login');
    }

    /**
     * Dashboard
     *
     * @return ViewModel
     */
    private function dashboard()
    {
        $viewOptions = $this->dashboardService->initialize();

        $viewModel = new ViewModel();
        $viewModel->setTemplate($this->getTemplate());
        $viewModel->setVariables($viewOptions);

        return $viewModel;
    }

    /**
     * Sign Up
     *
     * @return Response|ViewModel
     */
    private function signUp()
    {
        $sessionDetails = $this->sessionService->get();

        if (!empty($sessionDetails['user'])) {
            return $this->redirectTo('portal', 'dashboard');
        }

        $request = $this->getRequest();
        $viewOptions = [
            'isDone' => false,
        ];

        if ($request->isPost()) {
            $post = $request->getPost()->toArray();
            $post['userTypeId'] = 4;
            $form = new UserForm();
            $form->setData($post);

            if ($form->isValid()) {
                $user = $this->sessionService->create($post);
                $viewOptions['response']['code'] = $user['code'];
                $viewOptions['response']['message'] = $this->getResponseMessage($user['message']);
                $viewOptions['isDone'] = $user['code'] === SessionService::SUCCESS_CODE;
            } else {
                $viewOptions['response']['code'] = SessionService::INVALID_CODE;
                $viewOptions['response']['message'] = $this->getResponseMessage($form->getMessages());
            }
        }

        return new ViewModel($viewOptions);
    }

    /**
     * Find A Form
     *
     * @return ViewModel
     */
    private function findAForm()
    {
        $list = [];
        $forms = [
            'New and Renewal application Forms' => '',
            'Certification Form' => '',
            'Individual - Mayor\'s Permit Form' => '',
            'Business Additional Forms' => '',
            'Amendment Form' => '',
        ];

        $viewOptions = [
            'formCollection' => $forms,
            'listCollection' => $list,
        ];

        return new ViewModel($viewOptions);
    }
}
