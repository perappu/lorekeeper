<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Mail\ModMail;
use App\Models\Mail\UserMail;
use App\Models\User\User;
use App\Services\MailService;
use Auth;
use Illuminate\Http\Request;

class MailController extends Controller {
    /**
     * Shows the mail index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex() {
        if (!Auth::check()) {
            abort(404);
        }

        return view('home.mail.index', [
            'modMail'  => ModMail::where('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->get(),
            'inbox'    => UserMail::where('recipient_id', Auth::user()->id)->orderBy('created_at', 'desc')->get(),
            'outbox'   => UserMail::where('sender_id', Auth::user()->id)->orderBy('created_at', 'desc')->get(),
        ]);
    }

    /**
     * Shows a specific mod mail.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getModMail($id) {
        if (!Auth::check()) {
            abort(404);
        }
        $mail = ModMail::findOrFail($id);

        if (!$mail->seen && $mail->user_id == Auth::user()->id) {
            $mail->update(['seen' => 1]);
        }

        return view('home.mail.mod_mail', [
            'mail' => $mail,
        ]);
    }

    /**
     * Shows a specific user mail.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getUserMail($id) {
        if (!Auth::check() || !config('lorekeeper.mod_mail.allow_user_mail')) {
            abort(404);
        }
        $mail = UserMail::findOrFail($id);

        if (Auth::user()->id != $mail->sender_id && Auth::user()->id != $mail->recipient_id) {
            abort(403);
        }

        if (!$mail->seen && $mail->recipient_id == Auth::user()->id) {
            $mail->update(['seen' => 1]);
        }

        return view('home.mail.user_mail', [
            'mail' => $mail,
        ]);
    }

    /**
     * Shows the create user mail page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateUserMail() {
        if (!config('lorekeeper.mod_mail.allow_user_mail')) {
            abort(404);
        }

        return view('home.mail.create_user_mail', [
            'mail'  => new UserMail,
            'users' => User::orderBy('id')->where('id', '!=', Auth::user()->id)->pluck('name', 'id')->toArray(),
        ]);
    }

    /**
     * Sends mail from one user to another.
     *
     * @param mixed|null $mail_id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateUserMail(Request $request, MailService $service, $mail_id = null) {
        if (!config('lorekeeper.mod_mail.allow_user_mail')) {
            abort(404);
        }
        $data = $request->only(['recipient_id', 'subject', 'message', 'parent_id']);
        $mail = $mail_id ? UserMail::findOrFail($mail_id) : null;
        if ($mail) {
            $data['recipient_id'] = $mail->sender_id;
            $data['subject'] = 'Re: '.$mail->subject;
            $data['parent_id'] = $mail->id;
        } else {
            $request->validate(UserMail::$createRules);
        }

        if (!$mail = $service->createUserMail($data, Auth::user())) {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }

            return redirect()->to('mail');
        } else {
            flash('Message sent successfully.')->success();
        }

        return redirect()->to('mail/view/'.$mail->id);
    }
}
