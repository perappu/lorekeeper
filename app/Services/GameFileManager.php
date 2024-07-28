<?php

namespace App\Services;

use Illuminate\Support\Facades\File;

class GameFileManager extends Service {
    /*
    |--------------------------------------------------------------------------
    | Game File Manager
    |--------------------------------------------------------------------------
    |
    | Handles uploading and manipulation of game files.
    | this is a lot of data duplication, but it avoids messing with the main filemanager
    |
    */

    /**
     * Creates a directory.
     *
     * @param string $dir
     *
     * @return bool
     */
    public function createDirectory($dir) {
        if (file_exists($dir)) {
            $this->setError('error', 'Folder already exists.');
        } else {
            // Create the directory.
            if (!mkdir($dir, 0755, true)) {
                $this->setError('error', 'Failed to create folder.');

                return false;
            }
            chmod($dir, 0755);
        }

        return true;
    }

    /**
     * Deletes a directory if it exists and doesn't contain files.
     *
     * @param string $dir
     *
     * @return bool
     */
    public function deleteDirectory($dir) {
        if (!file_exists($dir)) {
            $this->setError('error', 'Directory does not exist.');

            return false;
        }
        $files = array_diff(scandir($dir), ['.', '..']);
        if (count($files)) {
            $this->setError('error', 'Cannot delete a folder that contains files.');

            return false;
        }
        rmdir($dir);

        return true;
    }

    /**
     * Renames a directory.
     *
     * @param string $oldName
     * @param string $newName
     *
     * @return bool
     */
    public function renameDirectory($oldName, $newName) {
        if (!file_exists($oldName)) {
            $this->setError('error', 'Directory does not exist.');

            return false;
        }
        $files = array_diff(scandir($oldName), ['.', '..']);
        if (count($files)) {
            $this->setError('error', 'Cannot delete a folder that contains files.');

            return false;
        }
        rename($oldName, $newName);

        return true;
    }

    /**
     * Uploads a file.
     *
     * @param array  $file
     * @param string $dir
     * @param string $name
     * @param bool   $isFileManager
     *
     * @return bool
     */
    public function uploadFile($file, $dir, $name, $isFileManager = true) {
        $directory = public_path().'/'.$dir;
        if (!file_exists($directory)) {
            $this->setError('error', 'Folder does not exist.');
        }
        File::move($file, $directory.'/'.$name);
        chmod($directory.'/'.$name, 0755);

        return true;
    }

    /**
     * Deletes a file.
     *
     * @param string $path
     *
     * @return bool
     */
    public function deleteFile($path) {
        if (!file_exists($path)) {
            $this->setError('error', 'File does not exist.');

            return false;
        }
        unlink($path);

        return true;
    }

    /**
     * Moves a file.
     *
     * @param string $oldDir
     * @param string $newDir
     * @param string $name
     *
     * @return bool
     */
    public function moveFile($oldDir, $newDir, $name) {
        if (!file_exists($oldDir.'/'.$name)) {
            $this->setError('error', 'File does not exist.');

            return false;
        } elseif (!file_exists($newDir)) {
            $this->setError('error', 'Destination does not exist.');

            return false;
        }
        rename($oldDir.'/'.$name, $newDir.'/'.$name);

        return true;
    }

    /**
     * Renames a file.
     *
     * @param string $dir
     * @param string $oldName
     * @param string $newName
     *
     * @return bool
     */
    public function renameFile($dir, $oldName, $newName) {
        if (!file_exists($dir.'/'.$oldName)) {
            $this->setError('error', 'File does not exist.');

            return false;
        }
        rename($dir.'/'.$oldName, $dir.'/'.$newName);

        return true;
    }

    /**
     * Uploads a file to an arbitrary public path location.
     * added by Games Room extension so that we can upload to a directory outside of /files/ or /images/.
     *
     * @param mixed $file
     * @param mixed $dir
     *
     * @return bool
     */
    public function uploadFileFullPath($file, $dir) {
        $directory = public_path().$dir;
        if (!file_exists($directory)) {
            $this->setError('error', 'Folder does not exist.');
        }
        File::move($file, $directory);
        chmod($directory, 0755);

        return true;
    }
}
