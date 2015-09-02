<?php

namespace Baseapp\Models;

/**
 * Class RolesUsers
 * @package Baseapp\Models
 */

class RolesUsers extends \Phalcon\Mvc\Model
{

    /**
     * Roles Users initialize
     *
     */
    public function initialize()
    {
        $this->belongsTo('user_id', __NAMESPACE__ . '\Users', 'id', array(
            'alias' => 'User',
            'foreignKey' => true
        ));
        $this->belongsTo('role_id', __NAMESPACE__ . '\Roles', 'id', array(
            'alias' => 'Role',
            'foreignKey' => true
        ));
    }

}
