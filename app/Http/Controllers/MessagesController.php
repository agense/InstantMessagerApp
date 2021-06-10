<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Events\NewMessage;
use App\Http\Requests\MessageRequest;
use App\Http\Resources\MessageResource;

class MessagesController extends Controller
{
    /**
     * @param Int $id - id of connecting user
     * @return MessageResource
     */
    public function getMessagesBetween(Int $id){

        //Abort if two users are not connected
        if( !auth()->user()->contactExists($id) ){
            return response()->json(['error' => 'This user is not in your contact list. Please send a contact request first.'], 403);
        }

        //Mark all messages from selected contact as read
        Message::setAllMessagesFromUserAsRead($id);

        //Get all messages between two users
        $messages = Message::MessagesBetween($id)->get();
        return MessageResource::collection($messages);
    }

    /**
     * @param MessageRequest $request
     * @return MessageResource
     */
    public function create(MessageRequest $request){

        $message = Message::create([
            'from' => auth()->id(),
            'to' => $request->contact_id,
            'message' => $request->message
        ]);

        broadcast(new NewMessage($message))->toOthers();

        return new MessageResource($message);
    }

    /**
     * @param Message $message
     * @return MessageResource
     */
    public function updateMessageReadStatus(Message $message){
        $message->setAsRead();
        $message->save();
        return new MessageResource($message);
    }

}
