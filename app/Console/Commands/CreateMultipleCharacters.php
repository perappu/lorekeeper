<?php

namespace App\Console\Commands;

use App\Models\Character\CharacterCategory;
use App\Models\User\User;
use App\Services\CharacterManager;
use Illuminate\Console\Command;

class CreateMultipleCharacters extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-multiple-characters';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create multiple placeholder characters at once';

    /**
     * Execute the console command.
     */
    public function handle() {
        $num = $this->ask('How many placeholder characters would you like to create?');

        $this->line('Please double check the following data when entered -- there is minimal error checking.');

        $cat = $this->ask('What is the character category ID?');
        $species = $this->ask('What is the species ID?');
        $rarity = $this->ask('What is the rarity ID?');
        $subtype = $this->ask('What is the subtype ID? (enter 0 for no subtype)');

        $service = new CharacterManager;
        $category = CharacterCategory::find($cat);

        for ($i = 0; $i < $num; $i++) {
            $id = $service->pullNumber($cat);

            $data = [
                'user_id'               => '1',
                'owner_url'             => null,
                'character_category_id' => $cat,
                'number'                => $id,
                'slug'                  => $category->code.'-'.$id,
                'description'           => null,
                'sale_value'            => null,
                'transferrable_at'      => null,
                'designer_id'           => [
                    0 => '1',
                    1 => null,
                ],
                'designer_url' => [
                    0 => null,
                    1 => null,
                ],
                'artist_id' => [
                    0 => null,
                    1 => null,
                ],
                'artist_url' => [
                    0 => null,
                    1 => null,
                ],
                'species_id' => $species,
                'subtype_id' => $subtype,
                'rarity_id'  => $rarity,
                'feature_id' => [
                    0 => null,
                ],
                'feature_data' => [
                    0 => null,
                ],
                'image'              => public_path('images/myo.png'),
                'thumbnail'          => public_path('images/myo-th.png'),
                'image_description'  => null,
                'extension'          => 'png',
                'fullsize_extension' => 'png',
                'default_image'      => true,
                'is_console'         => true,
            ];

            if ($character = $service->createCharacter($data, User::find(1))) {
                $this->line('Character created: '.$character->slug);
            } else {
                foreach ($service->errors()->getMessages()['error'] as $error) {
                    $this->error('Error creating character: '.$error);
                    break;
                }
            }
        }
    }
}
