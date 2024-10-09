<?php

namespace Application\Portal\Service;

use Application\User\Model\UserTable;
use ArrayObject;
use Laminas\Session\Container;
use Laminas\View\Model\JsonModel;

class SessionService
{
    const SUCCESS_CODE = 200;
    const SUCCESS_MESSAGE = 'Success';
    const INVALID_CODE = 401;
    const INVALID_MESSAGE = 'Invalid username or password';

    private $config;
    private $userTable;

    /**
     * SessionService constructor.
     *
     * @param ArrayObject $config
     * @param UserTable   $userTable
     */
    public function __construct(
        $config,
        UserTable $userTable
    ) {
        $this->config = $config;
        $this->userTable = $userTable;
    }

    /**
     * Initialize
     *
     * @param array $post
     *
     * @return array
     */
    public function initialize(array $post)
    {
        try {
            $user = $this->getUser($post);
            $code = self::INVALID_CODE;
            $message = self::INVALID_MESSAGE;
            $userName = !empty($user['userName']) ? $user['userName'] : null;
            $password = !empty($user['password']) ? $user['password'] : null;

            if ($post['username'] === $userName && $post['password'] === $password) {
                $this->setProfile($user);
                $code = self::SUCCESS_CODE;
                $message = self::SUCCESS_MESSAGE;
            }

            return [
                'code' => $code,
                'response' => [
                    'message' => $message,
                ]
            ];
        } catch (\Exception $exception) {
            return [
                'code' => $exception->getCode(),
                'response' => [
                    'message' => $exception->getMessage(),
                ],
            ];
        }
    }

    /**
     * Get Session Details
     *
     * @return array
     */
    public function get()
    {
        $session = new Container('User');

        return [
            'profile' => $session->offsetGet('profile'),
        ];
    }

    /**
     * Delete Session Details
     *
     * @return bool
     */
    public function delete()
    {
        $session = new Container('User');
        $session->getManager()->destroy();

        return true;
    }

    /**
     * Get User by Columns
     *
     * @param array $post
     *
     * @return array
     */
    private function getUser(array $post)
    {
        return $this->userTable->getUserByColumns([
                                                      'username' => $post['username'],
                                                      'password' => $post['password']
                                                  ]);
    }

    /**
     * Set Session Profile
     *
     * @param array|null $profile
     */
    private function setProfile(array $profile = null)
    {
        $session = new Container('User');
        $session->offsetSet('profile', $profile);
    }

    /**
     * Get User Details
     *
     * @return JsonModel
     */
    public function getUserDetails()
    {
        return $this->buildResponse(self::SUCCESS_CODE, [
            'user' => $this->userTable->fetchAll(),
        ]);
    }

    /**
     * Build Response
     *
     * @param int    $code
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
