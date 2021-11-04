<?php


namespace Modules\Core\Models\Auth;


use Illuminate\Database\Eloquent\Model;
use Modules\Core\Models\User\RetailUser;

class SubmoduleUser extends Model
{
    protected $table ="submodule_user";

    /**
     * @var string[]
     */
    protected $fillable = [
        'user_id','submodule_id'
    ];

    /**
     * @var string[]
     */
    protected $hidden = [
        'created_at','updated_at'
    ];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function submodules(){
        return $this->hasMany(Submodule::class,'id','submodule_id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function submodule(){
        return $this->hasOne(Submodule::class,'id','submodule_id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function users(){
        return $this->hasMany(RetailUser::class,'id','user_id');
    }

}
