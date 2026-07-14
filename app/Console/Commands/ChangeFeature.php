<?php

namespace App\Console\Commands;

use App\Facades\Settings;
use Illuminate\Console\Command;
use App\Models\Character\Character;
use Illuminate\Support\Facades\DB;

class ChangeFeature extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'change-feature';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Changes current featured character.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $characters = Character::myo()->get();
        $random = $characters->random();
        $setting = Settings::get('featured_character');
        while($random->id == $setting) {
            $random = $characters->random();
        }

        DB::table('site_settings')->where('key', 'featured_character')->update(['value' => $random->id]);
    }
}
