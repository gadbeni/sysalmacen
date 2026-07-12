<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use OwenIt\Auditing\Contracts\Auditable;

class User extends \TCG\Voyager\Models\User implements Auditable
{
    use HasFactory, Notifiable, SoftDeletes, \OwenIt\Auditing\Auditable;

    /**
     * Eliminación lógica (Voyager la usa automáticamente por el trait SoftDeletes):
     * al eliminar se registra quién lo hizo y queda rastro en user_historials;
     * al restaurar, lo mismo.
     */
    protected static function booted()
    {
        static::deleted(function (User $user) {
            if ($user->isForceDeleting()) {
                return;
            }
            $user->deleteuser_id = auth()->id();
            $user->saveQuietly();

            // Cerrar las sesiones del usuario eliminado
            DB::table('sessions')->where('user_id', $user->id)->delete();

            UserHistorial::create([
                'user_id' => $user->id,
                'changed_by' => auth()->id(),
                'accion' => 'eliminado',
                'antes' => ['Estado' => $user->status ? 'Activo' : 'Inactivo'],
                'despues' => ['Estado' => 'Eliminado'],
            ]);
        });

        static::restored(function (User $user) {
            $user->deleteuser_id = null;
            $user->saveQuietly();

            UserHistorial::create([
                'user_id' => $user->id,
                'changed_by' => auth()->id(),
                'accion' => 'restaurado',
                'antes' => ['Estado' => 'Eliminado'],
                'despues' => ['Estado' => $user->status ? 'Activo' : 'Inactivo'],
            ]);
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'funcionario_id',
        'registerUser_id',
        'email',
        'password',
        'role_id',
        'sucursal_id',
        'subSucursal_id',
        'direccionAdministrativa_id',
        'unidadAdministrativa_id',
        'contract_id',
        'last_login_at',
        'must_change_password',
        'status',
        'deleteuser_id',
    ];

    protected $auditExclude = [
        'password',
        'remember_token',
        'api_token',
        'token',
    ];

    public function registeredBy()
    {
        return $this->belongsTo(User::class, 'registerUser_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unidadAdministrativa_id');
    }

    public function direction()
    {
        return $this->belongsTo(Direction::class, 'direccionAdministrativa_id');
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    public function subAlmacen()
    {
        return $this->belongsTo(SucursalSubAlmacen::class, 'subSucursal_id');
    }

    /**
     * URL de la foto de perfil.
     * - Sin foto o default -> imagen local public/images/usuario.png (no depende de S3)
     * - URL completa -> tal cual
     * - Ruta relativa (foto subida) -> URL del disco de Voyager (S3)
     */
    public function getPhotoUrlAttribute()
    {
        $avatar = $this->avatar;

        if (empty($avatar) || $avatar === 'users/default.png') {
            return asset('images/usuario.png');
        }

        if (filter_var($avatar, FILTER_VALIDATE_URL)) {
            return $avatar;
        }

        return \TCG\Voyager\Facades\Voyager::image($avatar);
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
        'must_change_password' => 'boolean',
        'status' => 'boolean',
    ];

    public function isAdmin(){
        return $this->hasRole(['admin']);
    }
}
