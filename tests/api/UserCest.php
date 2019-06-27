<?php
use Codeception\Util\HttpCode;

class UserCest
{

    public function _before(ApiTester $I)
    {
    }

    // tests
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
