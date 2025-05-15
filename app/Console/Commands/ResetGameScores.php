<?php

namespace App\Console\Commands;

use App\Models\Game\Game;
use App\Models\Game\GameScore;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ResetGameScores extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reset-game-scores';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset daily game plays.';

    /**
     * Create a new command instance.
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() {
        $games = Game::all();

        $scores = collect();

        foreach ($games as $game) {
            switch ($game->playable_timeframe) {
                case 'daily':
                    $scores = $scores->merge(GameScore::where('game_id', $game->id)->whereDate('updated_at', '!=', Carbon::today())->get());
                    break;
                case 'weekly':
                    $scores = $scores->merge(GameScore::where('game_id', $game->id)->whereDate('updated_at', '<', Carbon::now()->startOfWeek())->get());
                    break;
                case 'monthly':
                    $scores = $scores->merge(GameScore::where('game_id', $game->id)->whereDate('updated_at', '<', Carbon::now()->startOfMonth())->get());
                    break;
            }
        }

        $this->line('Resetting '.$scores->count().' scores...');

        foreach ($scores as $score) {
            $score->update(['times_played' => 0]);
        }

        $this->line('Game scores reset!');

        return 0;
    }
}
