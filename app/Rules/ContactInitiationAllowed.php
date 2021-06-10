<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ContactInitiationAllowed implements Rule
{
    private $message;

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        //Deny requests to oneself
        if( auth()->id() == $value){
            $this->message = "Invalid Contact";
            return false;
        }
        //Deny if contact already exists
        if( auth()->user()->contactExists(request()->input('to')) ){
            $this->message = "This User already exists in your contacts";
            return false;
        }
        //Deny if contact initiation already exists
        if( auth()->user()->contactInitiationSentExist(request()->input('to')) ){
            $this->message = "Contact request has already been sent to this user.";
            return false;
        }elseif(auth()->user()->contactInitiationReceivedExist(request()->input('to')) ){
            $this->message = "You have already received a contact request from this user. Please accept it to chat with this contact.";
            return false;
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    }
}
