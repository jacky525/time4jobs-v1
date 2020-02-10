<?php

namespace Common\JbcLogin\Auth;

use Common\Authentication\User\UserInterface;
use Corp104\Support\GuzzleClientAwareTrait;
use Corp104\Support\LoggerTrait;
use Psr\Container\ContainerInterface;
use Psr\Log\LogLevel;
use Lcobucci\JWT\Parser;

class AppUser implements UserInterface
{
    use GuzzleClientAwareTrait, LoggerTrait;

    const TOKEN_PID = 'pid';
    const TOKEN_IDNO = 'idNo';
    const TOKEN_IS_MEMBER = 'isMember';
    const TOKEN_LOGIN_EXPIRE_TIME = 'loginExpireTime';

    /**
     * 驗證後的使用者資訊
     */
    private $userInfo = [];

    /** @var null|string */
    private $token = null;

    /** @var ContainerInterface */
    protected $container;

    protected function config()
    {
        return $this->container->get('config');
    }

    protected function aes()
    {
        return $this->container->get('aes');
    }

    public function __construct($container)
    {
        $this->container = $container;
        $this->setLogger($this->container->get('logger'));
    }

    /**
     * @param string $token
     *
     * @return AppUser
     */
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @return bool
     */
    public function isLogin(): bool
    {
        if (!$this->token) {
            throw new \InvalidArgumentException('app token must be set');
        }

        if (!$this->tokenIsValid($this->token)) {
            return false;
        }

        try {
            $this->userInfo = $this->userInfo($this->token);
            return true;
        } catch (\Exception $e) {
            $this->log(
                LogLevel::ERROR,
                'Get token info error',
                [$e->getMessage(), $e->getTrace()]
            );
            return false;
        }
    }

    /**
     * @return bool
     */
    public function isMember(): bool
    {
        return $this->getState(self::TOKEN_IS_MEMBER, false);
    }

    /**
     * @return int
     */
    public function getIdNo(): int
    {
        return (int) $this->getState(self::TOKEN_IDNO, 0);
    }

    /**
     * @return int
     */
    public function getPid(): int
    {
        return (int) $this->getState(self::TOKEN_PID, 0);
    }

    /**
     * @return mixed
     */
    public function getLoginExpireTime()
    {
        return $this->getState(self::TOKEN_LOGIN_EXPIRE_TIME, 0);
    }

    /**
     * 取得使用者特定資訊
     *
     * @param string $key variable name
     * @param mixed  $defaultValue default value
     *
     * @return mixed the value of the variable. If it doesn't exist in the session,
     * the provided default value will be returned
     */
    private function getState($key, $defaultValue)
    {
        return isset($this->userInfo[$key]) ? $this->userInfo[$key] : $defaultValue;
    }

    /**
     * 取得使用者資訊
     *
     * @param string $token
     *
     * @return array [
     *      'pid' => 213,
     *      'idNo' => 123,
     *      'isMember' => true,
     *      'versionNo' => 333,
     *  ]
     * @throws
     */
    private function userInfo($token)
    {
        $tokenParsed = (new Parser())->parse((string) $token);

        $decryptDatas = $this->aes()->decrypt([
            $tokenParsed->getClaim('id'),
            $tokenParsed->getClaim('pid'),
        ]);

        return [
            self::TOKEN_IDNO => $decryptDatas[0],
            self::TOKEN_PID => $decryptDatas[1],
            self::TOKEN_IS_MEMBER => $tokenParsed->getClaim('isMember'),
            self::TOKEN_LOGIN_EXPIRE_TIME => $tokenParsed->getClaim('exp'),
        ];
    }

    /**
     * 透過 c api 驗證 token
     *
     * @param string $token
     *
     * @return boolean
     */
    private function tokenIsValid($token)
    {
        try {
            $cApi = $this->config()['rest']['cApi']['endpoint'];
            $url = $cApi . '/resume/token/verification';

            $client = $this->getHttpClient();
            $apiKey = getenv('C_API_JWT');
            $response = $client->request('POST', $url, [
                'headers' => [
                    '104-API-Key' => $apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'token' => $token,
                ],
            ]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            $this->log(
                LogLevel::ERROR,
                'token check api error.',
                [$e->getMessage(), $e->getTrace()]
            );
            return false;
        }
    }
}
