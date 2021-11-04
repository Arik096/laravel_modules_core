<?php


namespace Modules\Core\Models\User;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cookie;
use Modules\SignUp\Entities\RoleNotification;

class Role extends Model
{
    protected $table;

    /**
     * Role constructor.
     */
    public function __construct()
    {
        if (!Cookie::has('prefix')) {
            $this->table = "sujog_roles";
        } else {
            $this->table = Cookie::get('prefix') . '_roles';
        }
    }


    /**
     * @var string[]
     */
    protected $fillable = [
        'name', 'is_active', 'flag'
    ];


    /**
     * @var string[]
     */
    protected $hidden = [
        'created_at', 'updated_at'
    ];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    /*public function notification(){
        return $this->hasMany(RoleNotification::class, 'id','role_id');
    }*/


    /**
     * @param $id
     * @return mixed
     */
    public static function getRoleName($id)
    {
        return Self::where(['id' => $id])->firstOrFail();
    }
}
