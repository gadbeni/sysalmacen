<?php

namespace App\Actions;

use TCG\Voyager\Actions\AbstractAction;

class ToggleUserStatusAction extends AbstractAction
{
    public function getTitle()
    {
        return $this->data->status ? 'Desactivar' : 'Activar';
    }

    public function getIcon()
    {
        return $this->data->status ? 'voyager-power' : 'voyager-check';
    }

    public function getPolicy()
    {
        return 'edit';
    }

    public function getDataType()
    {
        return 'users';
    }

    public function getAttributes()
    {
        $attributes = [
            'class' => $this->data->status
                ? 'btn btn-sm btn-dark pull-right'
                : 'btn btn-sm btn-success pull-right',
            'title' => $this->data->status ? 'Desactivar usuario' : 'Activar usuario',
        ];

        if ($this->data->status) {
            $attributes['style'] = 'background:#343a40;border-color:#343a40;color:#fff;';
        }

        return $attributes;
    }

    public function getDefaultRoute()
    {
        return route('users.toggle-status', $this->data->{$this->data->getKeyName()});
    }

    public function shouldActionDisplayOnRow($row)
    {
        return auth()->check()
            && auth()->id() !== $row->{$row->getKeyName()}
            && auth()->user()->hasPermission('edit_users');
    }
}
