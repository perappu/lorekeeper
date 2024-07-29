<?php

namespace App\Console\Commands;

use App\Models\Game\GameScore;
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
        $gameScores = GameScore::all();

        foreach ($gameScores as $gameScore) {
            $gameScore->update(['times_played' => 0]);
        }
        $this->line('Game scores reset!');

        return 0;
    }
}