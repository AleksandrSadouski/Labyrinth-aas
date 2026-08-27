<?php
namespace App\Services\PvP;

use App\Models\Player;
use App\Models\Room;
use App\Models\Profile;
use App\Services\PvP\PvPUpdateStatsService;
use App\Services\PvP\PvPGameHistoryService;
use App\Enums\RoomStatus;

class PvPCheckResultService
{
    private PvPUpdateStatsService $pvpUpdateStatsService;
    private PvPGameHistoryService $pvpGameHistoryService;

    public function __construct(PvPUpdateStatsService $pvpUpdateStatsService, PvPGameHistoryService $pvpGameHistoryService)
    {
        $this->pvpUpdateStatsService = $pvpUpdateStatsService;
        $this->pvpGameHistoryService = $pvpGameHistoryService;
    }

    public function checkResultMove(Player $player, Player $otherPlayer, Room $room, Profile $profile): array
    {
        if($player->y == $room->exit_y && $player->x == $room->exit_x)
            {
                if($player->player_order == 2 && $room->first_finished == false)
                    {
                $this->pvpUpdateStatsService->updateStats('win', $player, $otherPlayer, $room, $profile);
                $this->pvpGameHistoryService->createHistory('win', $profile, $otherPlayer->profile);
                $this->pvpGameHistoryService->createHistory('lose', $otherPlayer->profile, $profile);

                return $answer = ['message' => 'You win!',
                'room' => $room];
                    }

                elseif($player->player_order == 2 && $room->first_finished == true)
                    {
                $this->pvpUpdateStatsService->updateStats('draw', $player, $otherPlayer, $room, $profile);
                $this->pvpGameHistoryService->createHistory('draw', $profile, $otherPlayer->profile);
                $this->pvpGameHistoryService->createHistory('draw', $otherPlayer->profile, $profile);

                return $answer = ['message' => 'Draw',
                'room' => $room];
                }

                elseif($player->player_order == 1)
                    {
                        $room->first_finished = true;
                        $room->save();
                        return null;
                    }
            }
        elseif($player->player_order == 2 && $room->first_finished == true)
            {
                $this->pvpUpdateStatsService->updateStats('lose', $player, $otherPlayer, $room, $profile);
                $this->pvpGameHistoryService->createHistory('lose', $profile, $otherPlayer->profile);
                $this->pvpGameHistoryService->createHistory('win', $otherPlayer->profile, $profile);
                
                return $answer = ['message' => 'You lose!',
                'room' => $room];
            }
            else return $answer = null;
    }

    public function checkResultExit(Player $player, Player $otherPlayer, Room $room, Profile $profile): void
    {
        if($room->status == RoomStatus::Active)
            {
                $this->pvpUpdateStatsService->updateStats('lose', $player, $otherPlayer, $room, $profile);
                $this->pvpGameHistoryService->createHistory('lose', $profile, $otherPlayer->profile);
                $this->pvpGameHistoryService->createHistory('win', $otherPlayer->profile, $profile);
            }
    }
}