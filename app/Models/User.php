<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Contracts\Auditable;

class User extends \TCG\Voyager\Models\User implements Auditable
{
    use HasFactory, Notifiable, \OwenIt\Auditing\Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'funcionario_id',
        'email',
        'password',
        'role_id',
        'sucursal_id',
        'subSucursal_id',
        'direccionAdministrativa_id',
        'unidadAdministrativa_id',
        'contract_id',
        'last_login_at',
    ];

    protected $auditExclude = [
        'password',
        'remember_token',
        'api_token',
        'token',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unidadAdministrativa_id');
    }

    public function direction()
    {
        return $this->belongsTo(Direction::class, 'direccionAdministrativa_id');
    }

    public function getFuncionarioIdBrowseAttribute()
    {
        if (!$this->funcionario_id) return null;
        $person = \Illuminate\Support\Facades\DB::connection('mamore')
            ->table('people')
            ->select(\Illuminate\Support\Facades\DB::raw("CONCAT(COALESCE(first_name,''), ' ', COALESCE(paternal_surname,''), ' ', COALESCE(maternal_surname,'')) as nombre_completo"))
            ->where('id', $this->funcionario_id)
            ->where('deleted_at', null)
            ->first();
        return $person ? trim($person->nombre_completo) : $this->funcionario_id;
    }

    public function getDireccionAdministrativaIdBrowseAttribute()
    {
        return optional($this->direction)->nombre;
    }

    public function getUnidadAdministrativaIdBrowseAttribute()
    {
        return optional($this->unit)->nombre;
    }

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function isAdmin(){
        return $this->hasRole(['admin']);
    }
}
