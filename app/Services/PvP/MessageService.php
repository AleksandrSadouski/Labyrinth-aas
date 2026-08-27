<?php
namespace App\Services\PvP;

use App\Models\Profile;
use App\Models\Player;
use App\Models\Room;
use App\Models\Message;
use App\Http\Resources\MessageResource;

use DomainException;

class MessageService
{
    public function write(Profile $profile, string $new_message): Message
    {
        $player = $profile->player;
        if ($player == null) 
            {
                throw new DomainException('Player not in a room', 409);
            }
        $message = new Message();
        $message->player_id = $player->id;
        $message->message = $new_message;
        $message->save();

        return $message;
    }
}