<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ContactInitiationRequest;
use App\Models\User;
use App\Models\Message;
use App\Models\ContactInitiation;
use App\Http\Resources\ContactResource;
use App\Http\Resources\ContactInitiationResource;
use App\Events\NewContactInitiation;
use App\Events\ContactInitiationStatusChange;
use App\Events\ContactRemoved;

class ContactsController extends Controller
{
    /**
     * @return ContactResource
     */
    public function getAvailableContacts(){
        //Exclude users existing contacts, users with existing contact requests and user himself
        return ContactResource::collection( auth()->user()->getAvailableContacts() );
    }

    /**
     * @return Response (json)
     */
    public function getContactInitiations(){
        return response()->json( auth()->user()->getActiveContactInitiations() );
    }

    /**
     * @param ContactInitiationRequest $request
     * @return ContactInitiationResource
     */
    public function iniciateContact(ContactInitiationRequest $request){

        $initiation = ContactInitiation::create([
            'from' => auth()->id(),
            'to' => intval($request->input('to')),
        ]);
        broadcast(new NewContactInitiation($initiation))->toOthers(); 

        return new ContactInitiationResource($initiation, "Contact Request Sent");
    }

    /**
     * @param Request $request
     * @return ContactInitiationResource
     */
    public function acceptContact(Request $request){

        $initiation = ContactInitiation::find($request->id);
        $initiation->status = 'accepted';
        $acceptedInitiation = $initiation;
        $acceptedInitiation = auth()->user()->acceptContact($initiation);

        broadcast(new ContactInitiationStatusChange($acceptedInitiation))->toOthers(); 

        return new ContactInitiationResource($acceptedInitiation, "Contact Request Accepted");
    }

    /**
     * @param Request $request
     * @return ContactInitiationResource
     */
    public function rejectContact(Request $request){

        $initiation = ContactInitiation::find($request->id);
        $initiation->status = 'rejected';
        $initiation->save();

        broadcast(new ContactInitiationStatusChange($initiation))->toOthers(); 

        return new ContactInitiationResource($initiation, "Contact Request Rejected");
    }

    /**
     * @return ContactResource
     */
    public function getUserContacts(){

        $contacts = auth()->user()->contacts()->WithUnreadMessages()->get();
        return ContactResource::collection($contacts);
    }

    /**
     * @param Request $request
     * @return ContactResource
     */
    public function removeContact(Request $request){
        $request->validate([
            'id' => 'required|integer|exists:users,id',
        ]);
        //Abort if contact already exists
        if(!auth()->user()->contactExists($request->id) ){
            return response()->json(['error' => 'This User does not exists in your contacts'], 404);
        }
        $removed = auth()->user()->removeContact($request->id);

        broadcast(new ContactRemoved($removed))->toOthers(); 

        return new ContactResource($removed);
    }

   
}
