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

    /**
     * Get Error Message
     *
     * @param string|array $errorMessage
     *
     * @return string
     */
    protected function getResponseMessage($errorMessage)
    {
        if (is_array($errorMessage)) {
            foreach ($errorMessage as $errorKey => $errorItem) {
                if ($errorItem['isEmpty']) {
                    $errorMessage = 'Invalid Form! Please try again.';
                    continue;
                }
            }
        } else {
            if (strpos($errorMessage,'EMAIL_UNIQUE') !== false) {
                $errorMessage = 'Email already exists!';
            }

            if (strpos($errorMessage,'user.userName_UNIQUE') !== false) {
                $errorMessage = 'Username already exists!';
            }
        }

        return $errorMessage;
    }
}
