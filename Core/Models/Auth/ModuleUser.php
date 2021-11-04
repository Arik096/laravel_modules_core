<?php


namespace Modules\Core\Models\Auth;


use Illuminate\Database\Eloquent\Model;
use Modules\Core\Models\User\RetailUser;

class ModuleUser extends Model
{

    protected $table ="module_user";

    /**
     * @var string[]
     */
    protected $fillable = [
        'user_id','module_id'
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
    public function modules(){
        return $this->hasMany(Module::class,'id','module_id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function users(){
        return $this->hasMany(RetailUser::class,'id','user_id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function module(){
        return $this->hasOne(Module::class,'id','module_id');
    }

}
