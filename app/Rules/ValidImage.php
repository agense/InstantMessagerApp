<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Services\ImageStorageService;

class ValidImage implements Rule
{
    private $uploader;
    private $message;
    /**
     * Create a new rule instance.
     * @return void
     */
    public function __construct(){
        $this->uploader = new ImageStorageService();
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        //Validate File Extension
        if(!$this->uploader->validate_file_extension($value)){
            $allowed = implode(',', $this->uploader->get_allowed_extensions());
            $this->message =  'Uploading this file type is not allowed. Allowed file extensions are: '. $allowed;
            return false;
        }
        //Validate File Size
        elseif(!$this->uploader->validate_file_size($value)){
            $max = $this->uploader->get_max_size()/1000; //KB
            $this->message = 'File is too big. Maximum file size is ' .$max . ' KB';
            return false;
        }
        else{
            return true;
        }

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
