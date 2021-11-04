<?php


namespace Modules\Core\Models\Auth;


use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;

class Module extends Model
{

    protected $table ="modules";

    /**
     * @var string[]
     */
    protected $fillable = [
        'title','action','action_type','icons','upload_icon','comments'
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
        return $this->hasMany(Submodule::class,'module_id','id');
    }



    /**
     * @return \Illuminate\Database\Eloqu
     * ent\Relations\hasMany
     */
    public function permissions(){
        return $this->hasMany(Permission::class,'module_id','id');
    }


    public function getPermission($sub_module_id){
        return Permission::where(['sub_module_id'=>$sub_module_id])->get();
    }

}
