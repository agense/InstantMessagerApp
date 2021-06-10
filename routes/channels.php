<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/
// Allow users to only listen to messages directed at them
Broadcast::channel('messages.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Allow users to only listen to contact initiations directed at them
Broadcast::channel('contact_initiations_.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Allow users to only listen to contact initiation changes directed at them
Broadcast::channel('contact_initiation_status_change_.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Only broadcast to user who has been removed from someones contacts
Broadcast::channel('removed_from_contacts_.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Only broadcast to users who are on a contact list of user who is deleting his account
Broadcast::channel('account_deleted_.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

