<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * The relations to eager load on every query.
     * @var array
     */
    //protected $with = ['sender', 'receiver'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime:Y-m-d h:s',
        'updated_at' => 'datetime:Y-m-d h:s',
        'read_at' => 'datetime:Y-m-d h:s',
    ];

    /**
     * Model Relations
     */
     public function receiver(){
         return $this->belongsTo(User::class, 'to');
     }

     public function sender(){
        return $this->belongsTo(User::class, 'from');
    }

    /**
     * Query Scopes
     */
    /**
     * Scope a query to get messages between authenticated user and another user
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param Int $id
     * @return \Illuminate\Database\Eloquent\Builder
     */
     function scopeMessagesBetween( $q, Int $id){
        return self::where(function($q) use($id) {
            $q->where('from', auth()->id());
            $q->where('to', $id);
        })
        ->orWhere(function($q) use($id) {
            $q->where('from', $id);
            $q->where('to', auth()->id());
        });
     }

    /**
      * Model Methods
    */
    public static function setAllMessagesFromUserAsRead(Int $id){
        self::where('from', $id)->where('to', auth()->id())->update(['read_at' => date("Y-m-d H:i:s", time())]);
    }

    public function setAsRead(){
        $this->read_at = date("Y-m-d H:i:s", time());
    }
}
