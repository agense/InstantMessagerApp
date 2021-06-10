<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ValidImage;
use Illuminate\Support\Str;

class UserValidationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {   
        // check if a new profile image is being uploaded
        // if a new image is being uploaded, request()->profile_image will be a base64 formatted string
        // if it is a put(update) request and user has a profile image, request()->profile_image will be a string with users profile image url
        // if a user does not have a profile image or a new user is being created, request()->profile_image will be null
        // if request()->profile_image string does not equal users profile image url, validate the image
        $validateImg = Str::endsWith(request()->profile_image, auth()->user()->profile_image) ? '' : new ValidImage; 

        return [
            'name' => 'required|string|max:191|regex:/(^[A-Za-z0-9 ]+$)+/',
            'email' => 'required|string|email|max:191|unique:users,email,'.auth()->id(),
            'phone' => 'sometimes|nullable|digits_between:5,10',
            'profile_image' => ['sometimes','nullable', $validateImg],
        ];
    }
}
