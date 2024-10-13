<?php

namespace Application\Portal\Service;

use Application\User\Model\User;
use Application\User\Model\UserTable;
use Application\UserType\Model\UserTypeTable;
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
    private $userTypeTable;

    /**
     * Session Service constructor.
     *
     * @param ArrayObject   $config
     * @param UserTable     $userTable
     * @param UserTypeTable $userTypeTable
     */
    public function __construct(
        $config,
        UserTable $userTable,
        UserTypeTable $userTypeTable
    ) {
        $this->config = $config;
        $this->userTable = $userTable;
        $this->userTypeTable = $userTypeTable;
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
                'message' => $message,
            ];
        } catch (\Exception $exception) {
            return [
                'code' => $exception->getCode(),
                'message' => $exception->getMessage(),
            ];
        }
    }

    public function create(array $post)
    {
        try {
            $user = new User();
            $user->exchangeArray($post);
            $this->userTable->saveUser($user);
            return [
                'code' => self::SUCCESS_CODE,
                'message' => 'Thanks for Signing Up!',
            ];
        } catch (\Exception $exception) {
            return [
                'code' => self::INVALID_CODE,
                'message' => $exception->getMessage(),
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
        $session = new Container('Profile');

        return [
            'user' => $session->offsetGet('user'),
            'userType' => $session->offsetGet('userType'),
        ];
    }

    /**
     * Delete Session Details
     *
     * @return bool
     */
    public function delete()
    {
        $session = new Container('Profile');
        $session->offsetSet('user', '');
        $session->offsetSet('userType', '');
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
     * @param array|null $user
     */
    private function setProfile(array $user = null)
    {
        $userType = $this->userTypeTable->getUserTypeByUserTypeId($user['userTypeId']);

        $session = new Container('Profile');
        $session->offsetSet('user', $user);
        $session->offsetSet('userType', $userType);
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
