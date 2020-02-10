<?php

namespace Common\JbcLogin\Auth;

use Common\Authentication\User\UserInterface;
use Corp104\Support\GuzzleClientAwareTrait;
use Corp104\Support\LoggerTrait;
use Psr\Container\ContainerInterface;
use Psr\Log\LogLevel;

class WebUser implements UserInterface
{
    use GuzzleClientAwareTrait, LoggerTrait;

    const SESSION_PID = 'pid';
    const SESSION_IDNO = 'idNo';
    const SESSION_LOGIN_TIME = 'loginTime';
    const SESSION_LOGIN_METHOD = 'loginMethod';
    const SESSION_LOGIN_EXPIRE_TIME = 'loginExpireTime';
    const SESSION_IS_MEMBER = 'isMember';

    /**
     * Cookie token name AuthTokenCookie
     * @var string
     */
    const AUTH_TOKEN_COOKIE = 'JBCLOGIN';
    /**
     * 登入成功，AC 寫的全域 cookie
     *
     * @see https://github.com/104corp/104isgd-ac/blob/f5703f83d52e630f920cbc7242e071900097a6e1/services/oidc/custom-cookie.md#ac
     */
    const COOKIE_NAME_LOGIN_AC = 'AC';

    /**
     * 成功登入的登入資訊
     * @var array
     */
    private $loginInfo = [];

    /** @var ContainerInterface */
    protected $container;

    /**
     * 跟登入有關的 getState Key
     * @var array
     */
    public $requiredField = [
        self::SESSION_PID,
        self::SESSION_IDNO,
        self::SESSION_IS_MEMBER,
        self::SESSION_LOGIN_METHOD,
        self::SESSION_LOGIN_TIME,
        self::SESSION_LOGIN_EXPIRE_TIME
    ];

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->setLogger($this->container->get('logger'));
    }

    /**
     * 打 login server 拿登入訊息
     *
     * @param string $cookieToken
     *
     * @return array [
     *    'active' => bool,   //登入有效性
     *    'loginInfo' => [
     *       'idNo' =>
     *       'pid'=> ...
     *    ],
     * ]
     * @throws
     */
    public function checkLogin($cookieToken)
    {
        $jsonArray = [];
        try {
            $url = $this->container->get('config')['path']['loginInternal'] . '/checkLogin';

            $client = $this->getHttpClient();
            $response = $client->request('POST', $url, [
                'form_params' => [
                    'jbclogin' => $cookieToken,
                ],
            ]);
            $jsonArray = json_decode($response->getBody()->getContents(), true);

            return $jsonArray;
        } catch (\Exception $e) {
            $this->log(
                LogLevel::ERROR,
                'Check Login API Error.',
                [$e->getMessage(), $e->getTrace()]
            );
        }
        return $jsonArray;
    }

    public function setSessionCookieExpire($lifeTime)
    {
        $config = session_get_cookie_params();
        $path = $config['path'];
        $domain = $config['domain'];
        $secure = $config['secure'];
        $httpOnly = $config['httponly'];
        session_set_cookie_params($lifeTime, $path, $domain, $secure, $httpOnly);
    }

    /**
     * @return array
     */
    public function getLoginSession()
    {
        $result = [];
        foreach ($this->requiredField as $row) {
            $result[$row] = $this->getState($row, '');
        }
        return $result;
    }

    /**
     * @return int
     */
    public function getIdNo(): int
    {
        return (int) $this->getState(self::SESSION_IDNO, 0);
    }

    /**
     * @return int
     */
    public function getPid(): int
    {
        return (int) $this->getState(self::SESSION_PID, 0);
    }

    /**
     * @return mixed
     */
    public function getLoginExpireTime()
    {
        return $this->getState(self::SESSION_LOGIN_EXPIRE_TIME, 0);
    }

    /**
     * @return mixed
     */
    public function getLoginTime()
    {
        return $this->getState(self::SESSION_LOGIN_TIME, 0);
    }

    /**
     * @return boolean true:登入且未過期, false:未登入或時效過期
     */
    public function isLogin(): bool
    {
        $loginMethod = $this->getState('loginMethod', '');
        switch ($loginMethod) {
            case 'httpToken':
                return true;
            case 'cookie':
                // FALLTHROUGH
            default:
                $loginCookieAC = $this->getLoginCookieAC();
                if (!$loginCookieAC) {
                    return false;
                }

                // idno 有值就是login
                return !$this->getIsGuest();
        }
    }

    /**
     * @return bool
     */
    public function isMember(): bool
    {
        return $this->getState(self::SESSION_IS_MEMBER, false);
    }

    /**
     * @return bool
     */
    public function getIsGuest()
    {
        // idNo default = 0
        return empty($this->getIdNo());
    }

    /** 取得 cookie
     * @return string
     */
    private function getLoginCookie()
    {
        return $this->container->get('request')->getCookieParam(self::AUTH_TOKEN_COOKIE, null);
    }

    /**
     * AC 寫的全域 login cookie
     *
     * @return string|null
     */
    private function getLoginCookieAC()
    {
        return $this->container->get('request')->getCookieParam(self::COOKIE_NAME_LOGIN_AC, null);
    }

    /**
     * 沒有 AC Login cookie 但有 C Login cookie, 需清 C Login 狀態並踢登出
     *
     * @return bool
     */
    public function forceLogoutCheck()
    {
        $loginCookieAC = $this->getLoginCookieAC();
        $loginCookieC = $this->getLoginCookie();
        if (!$loginCookieAC && $loginCookieC) {
            // 代登 沒有 AC cookie
            return $this->getState(self::SESSION_LOGIN_METHOD, '') !== 'httpToken';
        }
        return false;
    }

    /**
     * 確認 SESSION KEY 後 Check Login
     *
     * @param string $key variable name
     * @param mixed  $defaultValue default value
     *
     * @return mixed the value of the variable. If it doesn't exist in the session,
     * the provided default value will be returned
     */
    private function getState($key, $defaultValue)
    {
        if (array_key_exists($key, $this->loginInfo)) {
            return $this->loginInfo[$key];
        }

        $loginCookie = $this->getLoginCookie();
        if (!$loginCookie) { // 沒有 login cookie
            return $defaultValue;
        }

        try {
            $loginInfo = $this->checkLogin($loginCookie);

            if (!$loginInfo['active']) { // 未登入或登入時效過期
                return $defaultValue;
            }
            // valid login
            $this->loginInfo = $loginInfo['loginInfo'];
            return isset($this->loginInfo[$key]) ? $this->loginInfo[$key] : $defaultValue;
        } catch (\Exception $e) {
            $this->log(
                LogLevel::ERROR,
                '查詢 user login 狀態失敗',
                [$e->getMessage(), $e->getTrace()]
            );
            return $defaultValue;
        }
    }
}
