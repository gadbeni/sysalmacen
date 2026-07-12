<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserHistorial extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'changed_by', 'accion', 'antes', 'despues'];

    protected $casts = [
        'antes' => 'array',
        'despues' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    /**
     * Retorna solo los campos que cambiaron entre antes y despues:
     * ['Campo' => ['antes' => ..., 'despues' => ...], ...]
     */
    public function getCambiosAttribute()
    {
        $antes = $this->antes ?? [];
        $despues = $this->despues ?? [];
        $campos = array_unique(array_merge(array_keys($antes), array_keys($despues)));

        $cambios = [];
        foreach ($campos as $campo) {
            $valorAntes = $antes[$campo] ?? null;
            $valorDespues = $despues[$campo] ?? null;
            if ($valorAntes !== $valorDespues) {
                $cambios[$campo] = ['antes' => $valorAntes, 'despues' => $valorDespues];
            }
        }
        return $cambios;
    }
}
