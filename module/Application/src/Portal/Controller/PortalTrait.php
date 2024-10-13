<?php

namespace Application\Portal\Controller;

use Laminas\Http\Response;

trait PortalTrait
{
    /**
     * Get Template
     *
     * @return bool|string
     */
    protected function getTemplate()
    {
        $action = $this->params()->fromRoute('action');
        $sessionDetails = $this->sessionService->get();
        $userTypeName = !empty($sessionDetails['userType'])
            ? strtolower($sessionDetails['userType']['userTypeName'])
            : '';

        if ($userTypeName) {
            return "application/portal/portal/$userTypeName/$action";
        }

        return "application/portal/portal/invalid";
    }

    /**
     * Redirect Page
     *
     * @param string $route
     * @param string $action
     * @param bool   $query
     *
     * @return Response
     */
    protected function redirectTo(string $route, string $action, $query = true)
    {
        return $this->redirect()->toRoute($route, ['action' => $action], $query);
    }
}
