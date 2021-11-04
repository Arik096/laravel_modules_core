<?php


namespace Modules\Core\Models\User;


use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $table = 'user_permission';

    /**
     * @var string[]
     */
    protected $fillable = [
        'name','controller','action'
    ];


    /**
     * @var string[]
     */
    protected $hidden = [
        'created_at','updated_at'
    ];


    /**
     * @return mixed
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
}
