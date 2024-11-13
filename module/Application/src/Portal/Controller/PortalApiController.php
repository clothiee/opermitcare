<?php

namespace Application\Portal\Controller;

use Application\Portal\Service\DashboardService;
use Application\Portal\Service\SessionService;
use ArrayObject;
use Laminas\Http\Response;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\JsonModel;
use Laminas\View\Model\ViewModel;

class PortalApiController extends AbstractActionController
{
    use PortalTrait;

    private $action;
    private $param1;
    private $config;
    private $sessionService;
    private $dashboardService;

    /**
     * PortalApiController constructor.
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
     * Dashboard Actions
     *
     * @return JsonModel|ViewModel
     */
    public function dashboardAction()
    {
        $this->param1 = $this->params()->fromRoute('param1', null);

        switch ($this->param1) {
            case 'reply-ticket':
                return $this->replyTicket();
            default:
                return $this->buildResponse(DashboardService::INVALID_CODE, [
                    'message' => DashboardService::INVALID_MESSAGE,
                ]);
        }
    }

    public function replyTicket()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $post = $request->getPost()->toArray();
            $process = $this->dashboardService->replyTicket($post);

            return $this->buildResponse($process['code'], [
                'message' => $this->getResponseMessage($process['message']),
            ]);
        }

        return $this->buildResponse(DashboardService::INVALID_CODE, [
            'message' => DashboardService::INVALID_MESSAGE,
        ]);
    }

    /**
     * Build Response
     *
     * @param int   $code
     * @param array $response
     *
     * @return JsonModel
     */
    private function buildResponse(int $code, array $response)
    {
        return new JsonModel([
                                 'code' => $code,
                                 'response' => $response,
                             ]);
    }
}
