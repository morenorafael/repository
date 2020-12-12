<?php

namespace MorenoRafael\Repository\Repositories;

use MorenoRafael\Repository\Models\User;
use MorenoRafael\Repository\Repository;

class UserRepository extends Repository
{
    /**
     * @var string
     */
    protected $model = User::class;

    /**
     * @var string
     */
    protected $baseKey = 'users';
}
