<?php
use Codeception\Util\HttpCode;

// 參照 https://codeception.com/docs/10-WebServices#REST

/**
 * Class UserCest
 */
class UserCest
{

    /**
     * @param ApiTester $I
     */
    public function _before(ApiTester $I)
    {
    }

    // tests

    /**
     * @param ApiTester $I
     */
    public function tryToTest(ApiTester $I)
    {
        $excepted = "hello";

        // Act
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGET('user/aaa');

        // Assert
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseContains('{"id":"aaa"}');
    }
}
