<?php

namespace App\Actions;

use TCG\Voyager\Actions\AbstractAction;

class UserHistorialAction extends AbstractAction
{
    public function getTitle()
    {
        return 'Historial';
    }

    public function getIcon()
    {
        return 'voyager-watch';
    }

    public function getPolicy()
    {
        return 'read';
    }

    public function getDataType()
    {
        return 'users';
    }

    public function getAttributes()
    {
        return [
            'class' => 'btn btn-sm btn-warning pull-right',
            'title' => 'Ver historial de cambios del usuario',
        ];
    }

    public function getDefaultRoute()
    {
        return route('users.historial', $this->data->{$this->data->getKeyName()});
    }

    public function shouldActionDisplayOnRow($row)
    {
        return auth()->check() && auth()->user()->hasPermission('browse_users');
    }
}
