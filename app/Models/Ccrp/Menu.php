<?php

namespace App\Models\Ccrp;

use Encore\Admin\Traits\AdminBuilder;
use Encore\Admin\Traits\ModelTree;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

/**
 * Class Menu.
 *
 * @property int $id
 *
 * @method where($parent_id, $id)
 */
class Menu extends Coldchain2Model
{
    use ModelTree, AdminBuilder;
    protected $table = 'menu';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['pid', 'order', 'title', 'types', 'slug', 'icon', 'icon_img', 'uri', 'permission'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setParentColumn('pid');
//        $this->setOrderColumn('sort');
//        $this->setTitleColumn('name');
    }

    public function getTypesAttribute($value)
    {
        return explode(',', $value);
    }

    public function setTypesAttribute($value)
    {
        $this->attributes['types'] = implode(',', $value);
    }

    /**
     * @return array
     */
    public function allNodes(): array
    {
        $orderColumn = DB::getQueryGrammar()->wrap($this->orderColumn);

        $byOrder = $orderColumn . ' = 0,' . $orderColumn;

        return static::orderByRaw($byOrder)->get()->toArray();//with('roles')->
    }

    /**
     * determine if enable menu bind permission.
     *
     * @return bool
     */
    public function withPermission()
    {
        return (bool)true;
    }

    /**
     * Detach models from the relationship.
     *
     * @return void
     */
//    protected static function boot()
//    {
//        static::treeBoot();
//        static::deleting(function ($model) {
//            $model->roles()->detach();
//        });
//    }


    public function withRoles($role_id = 0)
    {
        if ($role_id) {
            $rs = $this->whereHas('roles', function ($query) use ($role_id) {
                $query->where('role_id', $role_id);
            });
        } else {
            $rs = $this;
        }
//        foreach ($rs->get()  as $item)
//        {
//            dd($item->roles);
//        }
        return $rs;
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class,'menu_role','menu_id','role_id');
    }
}
