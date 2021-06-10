<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Services\ImageStorageService;
use Illuminate\Support\Facades\Storage;
use App\Models\ContactInitiation;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'created_at',
        'updated_at',
        'email_verified_at',
        'pivot'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Model Relations
     */
    public function contacts()
    {
      return $this->belongsToMany(User::class, 'contact_user', 'user_id', 'contact_id');
    }

    public function contactInitiationsReceived()
    {
      return $this->hasMany(ContactInitiation::class, 'to');
    }

    public function contactInitiationsSent()
    {
      return $this->hasMany(ContactInitiation::class, 'from');
    }

    public function messagesSent()
    {
      return $this->hasMany(Message::class, 'from');
    }

    public function messagesReceived()
    {
      return $this->hasMany(Message::class, 'to');
    }

    /**
     * Query Scopes
     */
    /**
     * Loads the unread messages count received from each contact
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithUnreadMessages($query)
    {
        return $query->withCount(['messagesSent' => function ($query){
            $query->where('to', auth()->id())
            ->where('read_at', null);
        }]);
    }

    /**
     * Model Methods
     */
    
    /**
     * Get all messages from current user to specified other user
     * @param Int $to
     * @return Collection
     */
    public function messagesToContact(Int $to)
    {
        return $this->messagesSent()->where('to', $to)->get();
    }

    /**
     * Get all messages recieved by the current user from specified other user
     * @param Int $from
     * @return Collection
     */
    public function messagesFromContact(Int $from)
    {
        return $this->messagesReceived()->where('from', $from)->get();
    }

    /**
     * Get ids of all current users contacts
     * @return Array
     */
    public function getUsersContactIds(){
      return $this->contacts()->pluck('contact_id')->toArray();
    }

    /**
     * Get ids of all users to whom the current user has sent a contact initiation request or from whom he received one
     * @return Array
     */
    public function getContactInitiationIds(){
      return array_merge($this->getReceivedContactInitiationsIds(), $this->getSentContactInitiationsIds());
    }
    
    /**
     * Get ids of all users contacts and attached contact initiations
     * @return Array
     */
    public function getAllConnections(){
      return array_merge(
        $this->getUsersContactIds(), 
        $this->getReceivedContactInitiationsIds(), 
        $this->getSentContactInitiationsIds()
      );
    }

    /**
     * Get active contact initiations
    */
    public function getActiveContactInitiations(){
      return $this->contactInitiationsReceived()->where('status', 'pending')->get();
    }
    /**
     * Get ids of all users from whom the current user has received a contact initiation request
     * @return Array
     */
    public function getReceivedContactInitiationsIds(){
      return $this->contactInitiationsReceived()->pluck('from')->toArray();
    }

    /**
     * Get ids of all users to whom the current user has sent a contact initiation request
     * @return Array
     */
    public function getSentContactInitiationsIds(){
      return $this->contactInitiationsSent()->pluck('to')->toArray();
    }


    /**
     * Combine existing contact ids, ids of contacts having sent and received initiations, and current users id
     * Used to exclude already connected contacts from new contacts search
     * @return Array
     */
    public function getExcludedContacts(){
        $excluded = array_merge( $this->getUsersContactIds(), $this->getSentContactInitiationsIds(), $this->getReceivedContactInitiationsIds() );
        array_push($excluded, $this->id);
        return $excluded;
    }

    /**
     * Returns a list of users not yet connected in any way to a current user
     * @return Collection
     */
    public function getAvailableContacts(){
      return self::whereNotIn('id', $this->getExcludedContacts())->get();
    }

    /**
     * Check if a specific id exists in users contacts
     * @param Int $contactId
     * @return Bool
     */
    public function contactExists(Int $contactId){
      return in_array($contactId, $this->getUsersContactIds() );
    }

    /**
     * Check if a contact initiation reuqest has been sent to a specific user
     * @param Int $contactId
     * @return Bool
     */
    public function contactInitiationSentExist(Int $contactId){
      return in_array($contactId, $this->getSentContactInitiationsIds());
    }

    /**
     * Check if a contact initiation request has been received from a specific user
     * @param Int $contactId
     * @return Bool
     */
    public function contactInitiationReceivedExist(Int $contactId){
      return in_array($contactId, $this->getReceivedContactInitiationsIds());
    }

    /**
     * Check if a contact initiation reuqest has been sent to or recieved from a specific user
     * @param Int $contactId
     * @return Bool
     */
    public function contactInitiationExists(Int $contactId){
      if( $this->contactInitiationSent($contactId) || $this->contactInitiationReceived($contactId) ){
        return true;
      }
      return false;
    }

    /**
     * Return a full url to users profile image
     * @return String
     */
    public function getProfileImageUrl(){
      return ($this->profile_image ? asset(Storage::url('img/users/'.$this->profile_image)) : null);
    }

     /**
      * Handle profile image uploads
      * @param Mixed $profile_img
      * @return Void
      */
    public function setProfileImage($profile_img = null){
      // check if profile_img argument is not equal to current profile image name, i.e. image is being changed.
      // if user has no profile image its profile image url value will be null. If no new image was uploaded, $profile_img argument will also be null, nothing will be uploaded
      // check if the profile_img argument is not null, and if not, upload the image
      if( $profile_img != $this->getProfileImageUrl()){
        $uploader = new ImageStorageService();

        // If $profile_img is null, and is not equal to current users profile image url (checked above),remove the current profile image without uploading a new one, otherwise replace the profile image
        if(!is_null($profile_img)){
              //Replace profile image or upload new
              $uploader->upload_or_replace($profile_img, 'profile', $this->profile_image);
              $profile = $uploader->get_uploaded_filename();
              if($profile !== ''){
                  $this->profile_image = $profile;
              }
        }else{
            $uploader->delete_image($this->profile_image);
            $this->profile_image = null;
        }
      }
    } 

    /**
     * Add a new contact to user contacts from received contact initiation request
     * @param ContactInitiation $initiation
     * @return ContactInitiation $initiation (initiation request with accepted contact details)
     */
    public function acceptContact(ContactInitiation $initiation){
      //Add a user who sent a contact request to current users contacts
      $this->contacts()->syncWithoutDetaching([$initiation->from]);

      //Add current user to the contacts of user who sent the conatct request (get that user by id specifid in from attribute of contact initiation instance)
      $contact =  self::find($initiation->from);
      $contact->contacts()->syncWithoutDetaching([$this->id]);
      $initiation->status = 'accepted';
      $initiation->delete();
      return $initiation;
    }
    /**
     * Remove a specified contact from current users contacts and vice versa
     * @param Int $contactId
     * @return App\Models\Contact
     */
    public function removeContact(Int $contactId){
        //Remove specified contact from current users contacts
        $this->contacts()->detach($contactId);

        //Remove current user from specified user scontacts
        $contact = self::find($contactId);
        $contact->contacts()->detach($this->id);

        //Delete messages between contacts
        $this->messagesSent()->where('to', $contactId)->delete();
        $this->messagesReceived()->where('from', $contactId)->delete();

        return $contact;
    } 

    /**
     * Remove all contacts from specific user and that user as contact from every other user
     * @return Void
     */
    public function removeAllContacts(){
      foreach($this->contacts as $contact){
        $contact->contacts()->detach($this->id);
      }
      $this->contacts()->detach();
      //Delete all messages
      $this->messagesSent()->delete();
      $this->messagesReceived()->delete();
    }

    public function deleteAccount(){

      //Remove all users contacts and contact requests
      $this->removeAllContacts();
      $this->contactInitiationsReceived()->delete();
      $this->contactInitiationsSent()->delete();

      //Delete profile image from storage if exists
      if( !is_null($this->profile_image) ) {
        try{
          $uploader = new ImageStorageService();
          $uploader->delete_image($this->profile_image);
        }catch(\Exception $e){
          //do nothing, delete anyway
        }
      }
      //Delete User
      $this->delete();
    }

    public static function createFromObject(\stdClass $object){
      $user = new self;
      $user->id = $object->id;
      $user->name = $object->name;
      $user->email = $object->email;
      $user->phone = $object->phone;
      $user->profile_image = $object->profile_image;
      return $user;
    }

}
