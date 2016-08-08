<?php

/*
 * This file is part of the Tinyissue package.
 *
 * (c) Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tinyissue\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Contracts\Auth\Guard;
use Tinyissue\Model;
use Illuminate\View\View;

/**
 * Controller is an abstract class for the controller classes.
 *
 * @author Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 */
abstract class Controller extends BaseController
{
    use DispatchesJobs, ValidatesRequests;

    /**
     * Current logged in user.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * Constructor, inject an instance of logged user.
     *
     * @param \Illuminate\Contracts\Auth\Guard $auth
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Return instance of the logged user.
     *
     * @return Model\User
     */
    protected function getLoggedUser()
    {
        $user = $this->auth->user();

        if (!$user instanceof Model\User) {
            $user =  null;
        }

        return $user;
    }
}
