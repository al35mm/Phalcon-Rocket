<?php

namespace Baseapp\Backend\Controllers;

use Baseapp\Models\Users;

/**
 * Created by PhpStorm.
 * User: Al
 * Date: 16/08/2015
 * Time: 00:59
 *
 * User CRUD etc
 */



class UsersController extends IndexController {

    public function indexAction(){
        $this->tag->setTitle('Users');
        $this->pageName = 'users';

        $users = Users::find();

        $this->view->setVar('users', $users);

    }

}