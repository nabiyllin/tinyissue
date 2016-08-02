<?php

/*
 * This file is part of the Tinyissue package.
 *
 * (c) Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tinyissue\Http\Requests\FormRequest;

use Tinyissue\Http\Requests\Request;

/**
 * UserMessageSetting is a Form Request class for managing edit user message setting submission (validating, redirect, response, ...).
 *
 * @author Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 */
class UserMessagesSettings extends Request
{
    /**
     * @var string
     */
    protected $formClassName = 'Tinyissue\Form\UserMessagesSettings';
}
