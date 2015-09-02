<?php

namespace Baseapp\Models;

/**
 * Class Roles
 * @package Baseapp\Models
 */

class Roles extends \Phalcon\Mvc\Model
{

    /**
     * Role initialize
     *
     */
    public function initialize()
    {
        $this->hasMany('id', __NAMESPACE__ . '\RolesUsers', 'role_id', array(
            'alias' => 'RolesUsers',
        ));
    }

}
