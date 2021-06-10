<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class ContactInitiation extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * The relations to eager load on every query.
     * @var array
     */
    protected $with = ['from', 'to'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime:Y-m-d h:s',
        'updated_at' => 'datetime:Y-m-d h:s',
    ];

    /**
     * Model Relations
     */

    /**
     * Sender User
     */
    public function from()
    {
      return $this->hasOne(User::class, 'id', 'from');
    }

    /**
     * Receiver User
     */
    public function to()
    {
      return $this->hasOne(User::class, 'id', 'to');
    }

    public static function createFromObjWithRelations(\stdClass $object){
            $initiation = new self();
            $initiation->id = $object->id;
            $initiation->to = User::createFromObject($object->to);
            $initiation->from = User::createFromObject($object->from);
            $initiation->created_at = $object->created_at;
            $initiation->status = $object->status;
            return $initiation;
    }
}
