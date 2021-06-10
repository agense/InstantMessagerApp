<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MessageRequest extends FormRequest
{
    private $userContacts;

    public function __construct(){
        $this->userContacts = auth()->user()->contacts()->pluck('contact_id')->toArray();
    }
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
     * Contact_id must be an id of an existing user that is connected as contact to current user
     * @return array
     */
    public function rules()
    {
        return [
            'contact_id' => [
                'required',
                'integer', 
                'exists:users,id', 
                Rule::in($this->userContacts)
            ],
            'message' => 'required|max:500|regex:/^[A-Za-z0-9.:!?\-\+\=\/\*\@\)\(\%\& ]{0,500}[#\$\^]?$/',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     * @return array
     */
    public function messages()
    {
        return [
            'contact_id.exists' => 'This contcat does not exist',
            'contact_id.in' => 'This user is not in your contact list. Please send a contact request first.',
            'message.max' => 'Message must be at most 500 characters',
            'message.regex' => 'Message has some invalid characters. Avoid using special characters except from punctuation marks and math operators.'
        ];
    }
}
