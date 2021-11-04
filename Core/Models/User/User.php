<?php


namespace Modules\Core\Models\User;


use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cookie;
use Laravel\Sanctum\HasApiTokens;
use Modules\Core\Models\Hub\Hub;
use Modules\Core\Models\Hub\HubManager;
use Modules\Core\Models\Hub\HubSEManager;
use Modules\Core\Models\Hub\HubWmm;
use Modules\Core\Models\Setting\District;
use Modules\Core\Models\Setting\Division;
use Modules\Core\Models\Setting\Union;
use Modules\Core\Models\Setting\Upazila;
use Modules\Core\Models\Setting\Village;
use Modules\Core\Models\Territory\TerritoryManager;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Translatable\HasTranslations;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;
    use HasTranslations;
    use SoftDeletes;
    use HasRoles;

    protected $table;
    protected $flag;

    public $translatable = ['name', 'guardian_name'];

    protected $dates = ['deleted_at'];

    protected $guard_name = 'web';

    /**
     * RetailUser constructor.
     */
    public function __construct()
    {
        if (!Cookie::has('prefix')) {
            $this->table = "sujog_users";
        } else {
            $this->table = Cookie::get('prefix') . '_users';
        }
    }


    /**
     * @var string[]
     */
    protected $fillable = [
        'spatie_role_id','role_id', 'flag', 'points', 'name', 'email', 'mobile', 'division_id', 'district_id', 'upazila_id', 'union_id', 'village_id', 'mouza', 'is_active', 'password', 'username', 'gender', 'date_of_birth', 'age', 'is_nid_card', 'self_nid_number', 'self_nid_fathers_name',
        'self_nid_mothers_name', 'self_nid_present_address', 'self_permenant_address', 'self_picture', 'self_mfs', 'self_bank_asia_account', 'guardian_nid_number', 'guardian_name', 'guardian_phone', 'guardian_gender', 'guardian_nid_present_address',
        'guardian_nid_permenant_address', 'guardian_picture', 'guardian_relation', 'guardian_mfs', 'is_complete_genarel_signup', 'education_requirement_id', 'investment_requirement_id', 'channel', 'category_type_ids', 'category_ids', 'suggest_course_ids',
        'pin', 'designation', 'registration_type', 'is_assign_hub', 'assign_hub_type', 'is_apologized', 'is_interested', 'otp', 'otp_status', 'user_status', 'asset_availabilitiey_id', 'is_complete_earn_signup', 'category_with_channel','signup_reference_id','referral_number',
        'device_info','longitude','latitude'
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
    public function division()
    {
        return $this->hasOne(Division::class, 'id', 'division_id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function district()
    {
        return $this->hasOne(District::class, 'id', 'district_id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function upazila()
    {
        return $this->hasOne(Upazila::class, 'id', 'upazila_id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function union()
    {
        return $this->hasOne(Union::class, 'id', 'union_id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function village()
    {
        return $this->hasOne(Village::class, 'id', 'village_id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function hub()
    {
        return $this->hasOne(Hub::class, 'fo_manager_id', 'id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo]
     */
    public function hubs()
    {
        return $this->belongsTo(Hub::class, 'fo_manager_id', 'id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function hubManager()
    {
        return $this->hasOne(HubManager::class, 'user_id', 'id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function HubWmm()
    {
        return $this->hasOne(HubWmm::class, 'user_id', 'id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function wmm()
    {
        return $this->hasOne(HubWmm::class, 'user_id', 'id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function wmmHub()
    {
        return $this->hasOne(HubWmm::class, 'hub_id', 'id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function territoryManager()
    {
        return $this->hasMany(TerritoryManager::class, 'user_id', 'id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function foHubsIds()
    {
        return $this->hasMany(HubManager::class, 'user_id', 'id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function foHubAssignHubsInfo()
    {
        return $this->belongsToMany(Hub::class, Cookie::get('prefix') . '_hub_managers', 'user_id', 'hub_id')
            ->using('Modules\Core\Models\Hub\HubManager');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function wmmAssignHubsInfo()
    {
        return $this->belongsToMany(Hub::class, Cookie::get('prefix') . '_hub_user', 'user_id', 'hub_id');
    }



    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function medal()
    {
        return $this->belongsToMany(Medal::class, Cookie::get('prefix') . '_medal_user', 'user_id', 'medal_id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function numberOfHubForSE()
    {
        return $this->hasMany(HubSEManager::class, 'user_id', 'id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany(\Modules\Core\Models\User\UserSignupComments::class, 'user_id', 'id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function education()
    {
        return $this->hasOne(\Modules\Core\Models\Management\EducationRequirement::class, 'id', 'education_requirement_id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function investment()
    {
        return $this->hasOne(\Modules\Core\Models\Management\InvestmentRequirement::class, 'id', 'investment_requirement_id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function asset()
    {
        return $this->hasOne(\Modules\Core\Models\Management\AssetAvailability::class, 'id', 'asset_availabilitiey_id');
    }


    /**
     * @param $id
     * @return mixed
     */
    public static function getName($id)
    {
        return RetailUser::where(['id' => $id])->firstOrFail();
    }


    /**
     * @param mixed $value
     * @return false|string
     */
    protected function asJson($value)
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }


    public function spatieRole(){
        return $this->hasOne(\Spatie\Permission\Models\Role::class,'id','spatie_role_id');
    }


}
