<?php

namespace App\Http\Controllers\Admin\Data;

use App\Http\Controllers\Controller;
use App\Models\Game\Game;
use App\Services\GameFileManager;
use Illuminate\Http\Request;

class GameFileController extends Controller {
    /**
     * Shows the files index.
     *
     * @param mixed      $id
     * @param mixed|null $folderName
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getGameFileIndex($id, $folderName = null) {
        $game = Game::find($id);
        if (!$game) {
            abort(404);
        }

        $filesDirectory = public_path().$game->filesDirectory;

        // Create the files directory if it doesn't already exist.
        if (!file_exists($filesDirectory)) {
            // Create the directory.
            if (!mkdir($filesDirectory, 0755, true)) {
                $this->abort(500);

                return false;
            }
            chmod($filesDirectory, 0755);
        }
        if ($folderName && !file_exists($filesDirectory.'/'.$folderName)) {
            abort(404);
        }
        $dir = $filesDirectory.($folderName ? '/'.$folderName : '');
        $files = scandir($dir);
        $fileList = [];
        foreach ($files as $file) {
            if (is_file($dir.'/'.$file)) {
                $fileList[] = $file;
            }
        }

        if ($folderName != null) {
            $folder = $game->filesDirectory.'/'.$folderName;
            $isRoot = false;
        } else {
            $folder = $game->filesDirectory;
            $isRoot = true;
        }

        return view('admin.games.game_files', [
            'isRoot'     => $isRoot,
            'folder'     => $folder,
            'folderName' => $folderName,
            'folders'    => glob(public_path().$game->filesDirectory.'/*', GLOB_ONLYDIR),
            'files'      => $fileList,
            'game'       => $game,
        ]);
    }

    /**
     * Creates a new directory in the files directory.
     *
     * @param App\Services\GameFileManager $service
     * @param mixed                        $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateFolder($id, Request $request, GameFileManager $service) {
        $game = Game::find($id);
        if (!$game) {
            abort(404);
        }

        $request->validate(['name' => 'required|alpha_dash']);

        if ($service->createDirectory(public_path().$game->filesDirectory.'/'.$request->get('name'))) {
            flash('Folder created successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Moves a file in the files directory.
     *
     * @param App\Services\GameFileManager $service
     * @param mixed                        $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postMoveFile($id, Request $request, GameFileManager $service) {
        $game = Game::find($id);
        if (!$game) {
            abort(404);
        }

        $request->validate(['destination' => 'required']);
        $oldDir = $request->get('folder');
        $newDir = $request->get('destination');

        if ($service->moveFile(
            public_path().'/files'.($oldDir ? '/'.$oldDir : ''),
            public_path().'/files'.($newDir != 'root' ? '/'.$newDir : ''),
            $request->get('filename')
        )) {
            flash('File moved successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Renames a file in the files directory.
     *
     * @param App\Services\GameFileManager $service
     * @param mixed                        $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postRenameFile($id, Request $request, GameFileManager $service) {
        $game = Game::find($id);
        if (!$game) {
            abort(404);
        }

        $request->validate(['name' => 'required|regex:/^[a-z0-9\._-]+$/i']);
        $dir = $request->get('folder');
        $oldName = $request->get('filename');
        $newName = $request->get('name');

        if ($service->renameFile(public_path().'/files'.($dir ? '/'.$dir : ''), $oldName, $newName)) {
            flash('File renamed successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Deletes a file in the files directory.
     *
     * @param App\Services\GameFileManager $service
     * @param mixed                        $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteFile($id, Request $request, GameFileManager $service) {
        $game = Game::find($id);
        if (!$game) {
            abort(404);
        }

        $request->validate(['filename' => 'required']);
        $dir = $request->get('folder');
        $name = $request->get('filename');

        if ($service->deleteFile(public_path().'/files'.($dir ? '/'.$dir : '').'/'.$name)) {
            flash('File deleted successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Uploads a file to the files directory.
     *
     * @param App\Services\GameFileManager $service
     * @param mixed                        $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postUploadFile($id, Request $request, GameFileManager $service) {
        $game = Game::find($id);
        if (!$game) {
            abort(404);
        }

        $request->validate(['files.*' => 'file|required']);
        $dir = $request->get('folder');
        $files = $request->file('files');
        foreach ($files as $file) {
            if ($service->uploadFile($file, $dir, $file->getClientOriginalName())) {
                flash('File uploaded successfully.')->success();
            } else {
                foreach ($service->errors()->getMessages()['error'] as $error) {
                    flash($error)->error();
                }
            }
        }

        return redirect()->back();
    }

    /**
     * Renames a directory in the files directory.
     *
     * @param App\Services\GameFileManager $service
     * @param mixed                        $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postRenameFolder($id, Request $request, GameFileManager $service) {
        $game = Game::find($id);
        if (!$game) {
            abort(404);
        }

        $request->validate(['name' => 'required|regex:/^[a-z0-9\._-]+$/i']);
        $dir = public_path().'/files';
        $oldName = $request->get('folder');
        $newName = $request->get('name');

        if ($service->renameDirectory($dir, $oldName, $newName)) {
            flash('Folder renamed successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }

            return redirect()->back();
        }

        return redirect()->to('admin/files/'.$newName);
    }

    /**
     * Deletes a directory in the files directory.
     *
     * @param App\Services\GameFileManager $service
     * @param mixed                        $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteFolder($id, Request $request, GameFileManager $service) {
        $game = Game::find($id);
        if (!$game) {
            abort(404);
        }

        $request->validate(['folder' => 'required']);
        $dir = $request->get('folder');

        if ($service->deleteDirectory(public_path().'/files/'.$dir)) {
            flash('Folder deleted successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }

            return redirect()->back();
        }

        return redirect()->to('admin/files');
    }

    /**
     * Uploads a site image file.
     *
     * @param App\Services\GameFileManager $service
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postUploadImage(Request $request, GameFileManager $service) {
        $request->validate(['file' => 'required|file']);
        $file = $request->file('file');
        $key = $request->get('key');
        $filename = config('lorekeeper.image_files.'.$key)['filename'];

        if ($service->uploadFile($file, null, $filename, false)) {
            flash('Image uploaded successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }
}
