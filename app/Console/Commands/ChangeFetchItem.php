<?php

namespace App\Console\Commands;

use DB;
use Settings;
use Log;
use Illuminate\Console\Command;
use App\Models\Item\Item;
use App\Models\FetchQuest;

class ChangeFetchItem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'change-fetch-item';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Changes currently wanted fetch item.';

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

        //fetch all fetch quests so we can update them
        $fetches = FetchQuest::all();
        foreach($fetches as $fetch){
            //fetch the items 
            //if category set, fish through that category
            //if none, pull from all items onsite
            //filter through both and remove everything in the "exception" list as well
            if($fetch->fetch_category_id){
                $items = Item::where('item_category_id', $fetch->fetch_category_id)->released()->get();
            }else{
                $items = Item::released()->where('allow_transfer', 1)->get();
            }
            if(!$items->count()) throw new \Exception('There are no items to select from!');
    
            //randomly select an item
            $totalWeight = $items->count();
            $roll = mt_rand(0, $totalWeight - 1);
            $result = $items[$roll]->id;
    
            //if the result = the current item set as the fetch, reroll until it is a new one (it's possible to get stuck in a loop if not enough items are available)
            $setting = $fetch->fetch_category_id;
            while($result == $setting) {
                $roll = mt_rand(0, $totalWeight - 1);
                $result = $items[$roll]->id;
            }

            //randomize currency max/min if all set. otherwise ignore
            if(isset($fetch->extras['reward_min_min']) && isset($fetch->extras['reward_min_max']) && isset($fetch->extras['reward_max_min']) && isset($fetch->extras['reward_max_max'])){
                //start with rand the min
                $roll2 = mt_rand($fetch->extras['reward_min_min'], $fetch->extras['reward_min_max']);
                //rand the max
                $roll3 = mt_rand($fetch->extras['reward_max_min'], $fetch->extras['reward_max_max']);
                //set the value.
                $fetch->current_min = $roll2;
                $fetch->current_max = $roll3;
                $fetch->save();
                
            }
    
            //set the current requested item as the result
            $fetch->fetch_item = $result;
            $fetch->save();

        }
        
    }
}
