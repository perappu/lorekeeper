# perappu's lorekeeper fork

Hello! This is where all my work surrounding Lorekeeper lives.

> [!NOTE]
> **Everything I have *personally* created in this repository is under the MIT license. You do not need to ask permission to use it or create derivative works.** 

> [!IMPORTANT]
> **Certain extension ports within this repo have been relicensed by the original author.** These branches will be under `extension/v3/[name]`. Please read the licenses before you pull them.

## table of contents

> [!CAUTION]
> **`fix/`, `pr/`, `feat/`, and `extension-pr/` branches should be disregarded.** Please do not pull these unless you know what you're doing. They serve as contributions that are intended for other branches.

### `extension/` branches

This is probably why you're here. 

> [!IMPORTANT]
> **All extension branches are based the stable LK v3.0.0 branch.** 
> 
> I do my best to keep these on the very latest version of v3. If you have an older version of v3, you may end up pulling the most recent v3 alongside the extension. 
> 
> I recommend pulling the latest v3 from core *before* merging in any of my extensions.

- `extension/logo`
    > [View the Wiki Page](https://wiki.lorekeeper.me/index.php?title=Extensions:Logo)
- `extension/carousel`
    > [View the Wiki Page](https://wiki.lorekeeper.me/index.php?title=Extensions:Carousel)
- `extension/rarity-icons`
    > [View the Wiki Page](https://wiki.lorekeeper.me/index.php?title=Extensions:Rarity_Icons)
- `extension/random-generator`
    > [View the Wiki Page](https://wiki.lorekeeper.me/index.php?title=Extensions:Random_Generators)
- `extension/games-room`
    > Inspired by the Neopets games room, this is an extension intended to make minigame development easier. Please read the documentation available on my website for how to utilize this extension: <https://perappu.blog/posts/developing-for-the-lorekeeper-games-room-extension/>
    > 
    > Essentially, you install the main extension, and then you are able to install games much like you would item tags. Games can also be added as "link" type games, which allows you to consolidate disparate game extensions into one "games room", so this extension is also useful to anyone who would like to have all their games categorized into one place.
    > 
    > **This does not come with any games by default. See the `extension/games` section below**.
- `extension/external-characters`
    > Allows for external characters to be added to prompt and gallery submissions, defined as a paired name and a link.
    > 
    > Just like normal characters, external characters be added/removed on prompt submission drafts, as well as by admins at submission approval time.
- `extension/sortable-prompts`
    > Adds a draggable sort to individual prompts, much like you can sort shops or prompt categories.
    > 
    > Prompts are displayed with the custom sort order by default, but also adds a "Custom Sort" to the sort dropdown on the prompt index.
    > 
    > __Please note that this REMOVES the pagination for prompts. Be cautious with installing if you have many pages of prompts.__
- `extension/character-thumbnail-borders`
    > An extension I haven't really announced but is likely fine to merge. Automatically adds a border around a new character image's thumbnail based on their species. 
    > 
    > **It does NOT work like the User Borders extension.** This modifies the thumbnail image itself. It is far more similar to Character Frames.

> [!IMPORTANT]
> You may also note a `ext-dev/` series of branches. These are WIP extensions that I do not officially support. Pull at your own risk.

### `extension/v3/` branches

These are extensions and features that I have either ported forward from v2 or backwards from unstable.

> [!NOTE]
>Much like my personal extensions, I do my best to keep these on the very latest version of v3. If you have an older version of v3, you may end up pulling the most recent v3 alongside the extension. I recommend pulling the latest v3 from core **before** merging.

> [!IMPORTANT]
> While I have contributed to many of these extensions, I am not their main author. For any branches that include a name, please verify if there is any custom license attached.

#### v2 -> v3
- `extension/v3/awards`
- `extension/v3/forums`
- `extension/v3/dailies`
- `extension/v3/character-titles`
- `extension/v3/crafting`
- `extension/v3/character-likes`
- `extension/v3/alt-trait-types`

#### unstable -> v3
- `extension/v3/shop-features` 
- `extension/v3/prompt-limits`
- `extension/v3/newt/user-ips`
- `extension/v3/newt/mini-clock`
- `extension/v3/newt/sellable-charas` (aka Adoption Center)
- `extension/v3/newt/character-relationships`
- `extension/v3/newt/pets` (Contains Shop Features)
- `extension/v3/newt/mod-mail`
- `extension/v3/newt/featured-character`

### `extension/games` branches

These are the games that I have created for my Games Room extension. Not all of them are on this repo, but they will be included for thoroughness: 
 - `extension/games/flappy`
    > Port of an official example game for the Phaser library. It's a flappy bird clone. Its purpose was to serve as an example for how to develop your own games, but it's perfectly fine to use in production as well.

My other games are distributed via Ko-fi, as this makes it much easier to bundle the uncompiled JS files with the LK branch.
 - [Plinko](https://ko-fi.com/s/eb3c7f0a37) (free)
 - [Blackjack](https://ko-fi.com/s/ff4d4a5856) (paid)

> [!WARNING]
> I can only provide support for the games I have personally developed. I can not help you with games that were created by a different developer. Pull them at your own risk.
> 
> I may eventually include a list of "verified" games, where I have personally been able to try them and confirm they work, but there are not any at this time.

### `tools/` branches

These are anything I consider to be less of an extension and more of a single or limited purpose tool. Keeping them separate from the extensions list is an arbitrary choice -- handle these like you would any extension.

- `tool/prepare-lorekeeper-v3`
    > This branch is intended to make a v2 to v3 upgrade significantly easier. 
    > - It makes minor changes to a few files (be sure to preserve them when merging), adds the blade formatter/pint, and a console command. 
    > - To use: 
    >     1. Merge then run `php artisan prepare-lorekeeper-v3`
    >     2. Merge in v3 with `git merge lorekeeper/release/v3.0.0 -Xignore-all-space`
    >     3. When resolved, run `php artisan update-lorekeeper-v3`. You will need to the update command on both local and live, and then `php artisan optimize:clear` on local afterwards. Do not run the prepare command on live.
- `tools/multiple-character-creation-console` 
    > This adds a console command that allows for a very large amount of placeholder characters to be created, such as when importing an existing masterlist. Tested with up to 8000 characters, so should work for most purposes!
    > 
    > - Run by entering `php artisan app:create-multiple-characters` into the console, then follow the instructions.
    > - All placeholders will use the site MYO image, but they are full characters, __not__ MYOs.
    > - When merging, please be aware that it also makes a small tweak to `CharacterManager.php` in `handleCharacterImage()`. This is required for image creation to work.

### `tweaks/` branches

These are branches that are minor tweaks to existing extensions that aren't worthwhile PRing upstream. Most of these are very old, so pull at your own risk.

- `tweaks/auto-backups-v3-backblaze` - Integrates the outdated backblaze b2 API driver package with the Automatic Backups extension. It's only here for historical purposes, just use S3 instead.

## conclusion

... and that's it! Thanks for reading. 

> [!NOTE]
> Feel free to open an issue or otherwise contact me with questions or concerns. However, please keep in mind that I am very busy. I may not respond right away, or I may only be able to offer limited advice. If you require a mentor, I offer that at my [standard coding commission rate](https://perappu.blog/commission/lorekeeper#general-work).
