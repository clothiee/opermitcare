<?php

namespace Application\Portal\Controller;

use Application\Opermitcare\User\Form\UserForm;
use Application\Portal\Service\DashboardService;
use Application\Portal\Service\SessionService;
use ArrayObject;
use Laminas\Http\Response;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class PortalController extends AbstractActionController
{
    use PortalTrait;

    private $action;
    private $param1;
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
     * FAQ Page
     *
     * @return ViewModel
     */
    public function faqAction()
    {
        return $this->buildView();
    }

    /**
     * FAQ Page
     *
     * @return ViewModel
     */
    public function supportAction()
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
        $this->action = $this->params()->fromRoute('action', null);
        $this->param1 = $this->params()->fromRoute('param1', null);
        $sessionDetails = $this->sessionService->get();
        $parameters = strlen($this->param1) ? '/' . $this->param1 : '';

        $this->layout()->setVariable('layoutVariables', [
            'pageName' => sprintf('%s%s', $this->action, $parameters),
            'env' => $this->config['env'],
            'isProfileAvailable' => empty($sessionDetails['user']) && empty($sessionDetails['userType']),
        ]);

        return $this->initialize();
    }

    /**
     * Initialize Pages
     *
     * @return Response|ViewModel
     */
    public function initialize()
    {
        switch ($this->action) {
            case 'login':
                return $this->login();
            case 'logout':
                return $this->logout();
            case 'dashboard':
                return $this->dashboard();
            case 'faq':
                return $this->faq();
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
        $viewOptions = [];
        $request = $this->getRequest();

        if ($request->isPost()) {
            $post = $request->getPost()->toArray();
            $files = $request->getFiles()->toArray();

            $viewOptions = $this->process($post, $files);
        }

        $viewModel = new ViewModel();
        $viewModel->setTemplate($this->getTemplate());
        $viewModel->setVariables(array_merge(
                                     $this->dashboardService->initialize(),
                                     $viewOptions)
        );

        switch ($this->param1) {
            case 'faq':
            default:
                break;
        }

        return $viewModel;
    }

    private function process($post, $files)
    {
        $viewOptions['activeTab'] = $post['process'] ?: 'overview';

        switch ($post['process']) {
            case 'create-ticket':
                $process = $this->dashboardService->createTicket($post, $files);
                $viewOptions['response']['code'] = $process['code'];
                $viewOptions['response']['message'] = $this->getResponseMessage($process['message']);
                break;
            case 'apply-permit':
                $process = $this->dashboardService->applyPermit($post, $files);
                $viewOptions['response']['code'] = $process['code'];
                $viewOptions['response']['message'] = $this->getResponseMessage($process['message']);
                $viewOptions['response']['data'] = $process['data'];
                break;
            case 'assess-permit':
                $process = $this->dashboardService->accessPermit($post);
                $viewOptions['response']['code'] = $process['code'];
                $viewOptions['response']['message'] = $this->getResponseMessage($process['message']);
                $viewOptions['response']['data'] = $process['data'];
                $viewOptions['activeTab'] = 'my-permit';
                $viewOptions['activePanel'] = $process['activePanel'];
                $viewOptions['activeTabAction'] = $process['activeTabAction'];
                break;
            case 'update-password':
                $process = $this->dashboardService->updatePassword($post);
                $viewOptions['response']['code'] = $process['code'];
                $viewOptions['response']['message'] = $this->getResponseMessage($process['message']);
                $viewOptions['response']['data'] = $process['data'];
                $viewOptions['activeTab'] = 'my-profile';
                break;
        }

        return $viewOptions;
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
    private function faq()
    {
        $faq = $this->dashboardService->getFAQs();
        $viewOptions = [
            'faq' => $faq['category'],
            'faqDetails' => $faq['list'],
        ];

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
            'New and Renewal application Forms' => 'UPDATED-REQUIREMENTS-NEW.jpeg.pdf',
            'Certification Form' => 'CERTIFICATION-FORM.pdf',
            'Individual - Mayor\'s Permit Form' => 'INDIVIDUAL-MAYORS-PERMIT-FORM.pdf',
            'Business Additional Forms' => 'Unified-Editable-Form-2021.xlsx',
            'Amendment Form' => '/downloads/AMENDMENT-FORM.pdf',
        ];

        $viewOptions = [
            'formCollection' => $forms,
            'listCollection' => $list,
        ];

        return new ViewModel($viewOptions);
    }
}
