<?php

namespace Common\Authentication\User;

interface UserInterface
{
    public function getIdNo(): int;

    public function getPid(): int;

    public function isLogin(): bool;

    public function isMember(): bool;

    public function getLoginExpireTime();
}
