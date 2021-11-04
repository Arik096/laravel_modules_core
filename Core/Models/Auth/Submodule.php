<?php


namespace Modules\Core\Models\Auth;


use Illuminate\Database\Eloquent\Model;
use Modules\SignUp\Entities\ComponentCategory;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class Submodule extends Model
{

    protected $table = "submodules";

    /**
     * @var string[]
     */
    protected $fillable = [
        'title', 'action', 'action_type', 'icons', 'module_id', 'is_active', 'upload_icon', 'comments','icon_hover','nick_name','bg_color_one','bg_color_two'
    ];

    /**
     * @var string[]
     */
    protected $hidden = [
        'created_at', 'updated_at'
    ];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function module()
    {
        return $this->hasOne(Module::class, 'id', 'module_id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function permissions()
    {
        return $this->hasMany(Permission::class, 'id', 'sub_module_id');
    }


    public function categories()
    {
        return $this->hasMany(ComponentCategory::class, 'component_id', 'id');

    }

}
