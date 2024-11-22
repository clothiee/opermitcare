<?php

namespace Application\Portal\Controller;

use Application\Portal\Service\DashboardService;
use Application\Portal\Service\SessionService;
use ArrayObject;
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
            case  'refresh-ticket':
                return $this->refreshTicket();
            default:
                return $this->buildResponse(DashboardService::INVALID_CODE, [
                    'message' => DashboardService::INVALID_MESSAGE,
                ]);
        }
    }

    private function replyTicket()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $post = $request->getPost()->toArray();
            $process = $this->dashboardService->replyTicket($post);

            return $this->buildResponse($process['code'], [
                'message' => $this->getResponseMessage($process['message']),
                'ticketStatusId' => $process['ticketStatus']['ticketStatusId'],
                'ticketStatusName' => $process['ticketStatus']['ticketStatusName'],
                'userType' => $process['userType'],
                'dateCreated' => $process['dateCreated'],
            ]);
        }

        return $this->buildResponse(DashboardService::INVALID_CODE, [
            'message' => DashboardService::INVALID_MESSAGE,
        ]);
    }

    private function refreshTicket()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $post = $request->getPost()->toArray();
            $process = $this->dashboardService->refreshTicket($post);

            return $this->buildResponse($process['code'], [
                'message' => $process['message'],
                'data' => $process['data'],
            ]);
        }

        return $this->buildResponse(DashboardService::INVALID_CODE, [
            'message' => DashboardService::INVALID_MESSAGE,
            'data' => [],
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
