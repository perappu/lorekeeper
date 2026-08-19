<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileObject;
use Symfony\Component\Process\Process;

class PrepareLorekeeperV3 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'prepare-lorekeeper-v3';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs commands to prepare for updating Lorekeeper to version 3.0 from version 2.';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        if (! App::environment('local')) {
            $this->warn('This command should only be run on your local. Do not run it on live.');

            return 0;
        }

        $this->info('****************************************************');
        $this->info('********* PREPARE LOREKEEPER FOR V2 -> V3 **********');
        $this->info('****************************************************'."\n");

        $this->error("!! THIS COMMAND WILL DIRECTLY MODIFY YOUR CODE !!\n");
        $this->info('This command is intended to be run directly before merging V3 into your code.');
        $this->warn('- Do not have any pending/uncommitted changes before running this command.');
        $this->warn('- Do not attempt to alter your code while running this command.');
        $this->info("It is safe to run more than once if an error is encountered.\n");
        if ($this->confirm('Please confirm that you are ready to run this command')) {

            // Delete squashed migrations
            $this->info('Deleting squashed migrations...');

            // rather than hardcode the filenames, we pluck the squashed migrations out of the sql manually
            // SplFileObject bc the file is big.
            // trying to make this as future proof as possible by not hardcoding which line number to start from...
            // so we search the whole file line by line
            $sqlDump = new SplFileObject(base_path('database\schema\mysql-schema.dump'), 'r');
            foreach ($sqlDump as $line) {
                if (is_string($line) && $line !== null) {
                    if (Str::contains($line, 'INSERT INTO `migrations`')) {
                        preg_match('/\QINSERT INTO `migrations` VALUES (\E\d+,\'(.+)\',.*/', $line, $matches);
                        $migration = $matches[1];
                        $path = base_path("database\migrations\\{$migration}.php");
                        if (File::exists($path)) {
                            try {
                                File::delete($path);
                                $this->warn("{$migration} deleted.");
                            } catch (Exception $e) {
                                $this->error('Error deleting file '.$path);
                                $this->error($e->getMessage());

                                return 1;
                            }

                            continue;
                        } else {
                            $this->line("{$migration} was already deleted.");
                        }
                    }
                }
            }
            // close the file
            $sqlDump = null;

            // Convert extension tracker
            $extensions = config('lorekeeper.extension_tracker');
            $this->info("\nConverting old single extension tracker file into multiple files...");
            $indent = '    ';
            // just in case someone ran optimize when they shouldn't have
            $this->call('config:clear');

            foreach ($extensions as $configKey => $configData) {
                $filename = strtolower($configData['key']).'.php';
                $path = config_path('lorekeeper/ext-tracker/'.$filename);
                if (File::exists($path)) {
                    $this->line("Config file {$filename} already exists.");

                    continue;
                }

                $contents = "<?php\n\nreturn [\n";
                foreach ($configData as $key => $value) {
                    // the json_encode in the extension tracker file was evaluated
                    // so we have to decode it and recreate the string
                    if ($valueAsArray = json_decode($value, true)) {
                        $arrayString = "\n";
                        foreach ($valueAsArray as $arrKey => $arrValue) {
                            $arrayString .= "{$indent}{$indent}'{$arrKey}' => '{$arrValue}',\n";
                        }
                        $value = "json_encode([{$arrayString}{$indent}])";
                    } else {
                        $value = "'{$value}'";
                    }
                    $contents .= "{$indent}'{$key}' => {$value},\n";
                }
                $contents .= "];\n";

                try {
                    File::put($path, $contents);
                } catch (Exception $e) {
                    $this->error('Error creating file '.$path);
                    $this->fail($e->getMessage());
                }

                $this->info('Config file '.$filename.' created.');
            }
            $this->call('config:clear');

            // Simple conversions
            $this->info('Performing simple conversions...');

            $conversions = [
                ['use App\Models\Comment;', 'use App\Models\Comment\Comment;', 'Comment'],
                ['orderByRaw(DB::raw(', 'orderBy(DB::raw(', 'orderByRaw'],
                ['Config::get(', 'config(', 'Config::get'],
                ['return new $class;', 'return new $class();', '$class'],
            ];

            $lkDirectory = new RecursiveDirectoryIterator(base_path());
            $filter = new \RecursiveCallbackFilterIterator($lkDirectory, function ($current, $key, $iterator) {
                if ($current->getFilename()[0] === '.') {
                    // skip hidden files/directories
                    return false;
                }
                if ($current->isDir()) {
                    // make sure it's a directory we care about
                    return preg_match('/app|config|database|resources|routes|tests/', $current->getPathname());
                } else {
                    // make sure it's a php file
                    return pathinfo($current->getPathname(), PATHINFO_EXTENSION) === 'php' && $current->getFilename() !== 'PrepareLorekeeperV3.php';
                }
            });
            $iterator = new RecursiveIteratorIterator($filter);
            foreach ($iterator as $file) {
                $pathName = $file->getPathname();
                $readFile = new SplFileObject($pathName);
                $writeFile = new SplFileObject(base_path('temp.txt'), 'w');

                $contents = '';
                $changed = false;
                $changes = [];
                foreach ($readFile as $index => $line) {
                    if (is_string($line) && $line !== null) {
                        $temp = $line;
                        foreach ($conversions as $conversion) {
                            $temp = str_replace($conversion[0], $conversion[1], $temp, $count);
                            if ($count) {
                                $changed = true;
                                $changes[] = $conversion[2];
                            }
                        }
                        $writeFile->fwrite($temp);
                    } else {
                        $writeFile->fwrite($line);
                    }
                }

                $readFile = null;
                $writeFile = null;

                if ($changed) {
                    unlink($pathName);
                    rename(base_path('temp.txt'), $pathName);
                    $this->warn("Updated {$pathName} (".implode(', ', $changes).')');
                }
            }
            unlink(base_path('temp.txt'));

            // Format + lint
            if (function_exists('system')) {
                $this->info('Running composer lint, please be patient...');
                system('composer lint');
                $this->info('Running blade formatter, please be patient...');

                // TODO: getting the blade formatter to run from an artisan command is a nightmare
                // figure out if we're on windows or not
                /*if(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                    if(stripos(getenv('PSModulePath'), 'powershell')) {
                        $formatPath = '.\node_modules\.bin\blade-formatter.ps1';
                    } else {
                        $formatPath = '.\node_modules\.bin\blade-formatter.cmd';
                    }
                } else {
                    $formatPath = '.\node_modules\.bin\blade-formatter';
                }

                $viewsDirectory = new RecursiveDirectoryIterator(base_path('resources\views'));
                foreach($viewsDirectory as $directory) {
                    if ($directory->getFilename()[0] !== '.' && $directory->isDir()) {
                        $path = base_path($directory);
                        system("powershell -ExecutionPolicy Bypass -File \"" . $formatPath . " --progress --write '{$path}\**\*.blade.php'\"");
                    }
                }*/
            } else {
                $this->info('system() is not available, so formatters did not run.');
            }

            // all done!
            $this->info("\nChanges complete.");
            $this->warn('Ensure all changes are committed to git before merging in v3.');
            $this->info("Consider using 'git merge lorekeeper/release/v3.0.0 -Xignore-all-space' to reduce merge conflicts.");

            return 0;
        }
    }
}
