<?php

namespace App\Http\Controllers\User;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

use Carbon\Carbon;

use App\Models\User\{
    User,
    Crate,
    Avatar,
    Status,
    Friend,
    Message,
    IPAddress,
    Email\Email
};

use App\Http\Requests\User\{
    Register
};

class CreateController extends Controller
{
    public function registerPage()
    {
        return view('pages.auth.register');
    }

    public function register(Register $request)
    {
        $user = User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'avatar_hash' => ''
        ]);

        if ($request->filled('email')) {
            do {
                $key = str_random(60);
                $check = Email::where('verify_code', $key)->count();
            } while ($check > 0);

            $email = Email::create([
                'user_id' => $user->id,
                'email' => $request->email,
                'verify_code' => $key,
                'verified' => 0
            ]);
        }

        // DO NOT CHANGE [bhstartmsg] IT CHANGES IT TO THE CORRECT VALUE ON THE PAGE
        Message::create([
            'author_id' => config('site.main_account_id'),
            'recipient_id' => $user->id,
            'title' => 'Welcome To Brick Hill!',
            'message' => '[bhstartmsg]',
            'read' => 0
        ]);

        $tshirt = rand(1, 8);
        Crate::create([
            'user_id' => $user->id,
            'crateable_id' => $tshirt,
            'crateable_type' => 1
        ]);

        $torsoColors = array('c60000', '3292d3', '85ad00', 'e58700');
        $legColors = array('650013', '1c4399', '1d6a19', '76603f');
        $torso = $torsoColors[rand(0, 3)];
        $leg = $legColors[rand(0, 3)];

        Avatar::create([
            'user_id' => $user->id,
            'items' => [
                'hats' => [0, 0, 0, 0, 0],
                'face' => 0,
                'tool' => 0,
                'head' => 0,
                'figure' => 0,
                'shirt' => 0,
                'pants' => 0,
                'tshirt' => $tshirt
            ],
            'variations' => [],
            'colors' => [
                'head' => 'f3b700',
                'torso' => $torso,
                'right_arm' => 'f3b700',
                'left_arm' => 'f3b700',
                'right_leg' => $leg,
                'left_leg' => $leg
            ]
        ]);

        // login broke and it requires a new user request to add it ???
        $getUserLogin = User::find($user->id);

        Auth::login($getUserLogin);

        IPAddress::create([
            'user_id' => $user->id,
            'ip' => request()->ip()
        ]);

        return redirect('dashboard');
    }

    public function newStatus()
    {
        $status = request('status');

        $rules = [
            'status' => 'min:1|max:124'
        ];

        $validator = validator(request()->all(), $rules);

        if ($validator->fails())
            return redirect()
                ->route('dashboard')
                ->withInput()
                ->withErrors($validator->messages());

        $id = Auth::id();

        $lastStatus = Status::where([
            ['owner_id', $id],
            ['date', '>=', Carbon::now()->subSeconds(15)]
        ]);


        if ($lastStatus->count() > 0 && Auth::user()->power == 0) {
            $time = Carbon::now()->diffInSeconds($lastStatus->first()->date);
            return redirect()
                ->route('dashboard')
                ->withInput()
                ->withErrors(['errors' => 'You need to wait ' . (15 - $time) . ' ' . str_plural('second', 15 - $time) . ' to make a new status']);
        }
        $newStatus = Status::create([
            'owner_id' => $id,
            'body' => $status
        ]);

        return redirect()->route('dashboard')->with('success', 'Status changed');
    }

    public function createMessage($userId)
    {
        $user = User::find($userId);

        if (!$user || $user->id == Auth::id())
            return redirect('messages');

        return view('pages.user.messages.newmessage')->with([
            'user' => $user
        ]);
    }

    public function postMessage()
    {
        $user = Auth::user();

        if (!Carbon::parse(auth()->user()->created_at)->addDay()->isPast())
            return error('Your account must be at least one day old to send messages');

        $id = request('msgId');

        $rules = [
            'reply' => 'required|string|max:10000|min:3',
            'title' => 'required|string|max:255|min:3'
        ];

        $validator = validator(request()->all(), $rules);

        if ($validator->fails())
            return error($validator->messages()->first());

        $lastMsg = Message::where([
            ['author_id', $user->id],
            ['created_at', '>=', Carbon::now()->subSeconds(30)]
        ])->count();

        if ($lastMsg > 0)
            return error('You can only send one message every 30 seconds');

        $reply = request('reply');
        $title = request('title');
        $recipient = request('recipient');

        if (isset($id)) {
            $msg = Message::find($id);
            if ($msg['recipient_id'] != $user['id'])
                return back();

            $date = \Carbon\Carbon::parse($msg['created_at'])->format('Y-d-m');
            $author = $msg->sender->username;
            $message = $msg->message;

            $prev = "\n\n------------------------$author at $date------------------------\n\n$message";
            if (strlen($reply) + strlen($prev) < 10000) {
                $reply = $reply . $prev;
            }

            if (strlen($title) + 4 < 255) {
                $title = 'RE: ' . $title;
            }

            $recipient = $msg['author_id'];
        }

        $recUser = User::find($recipient);

        if (!$recUser)
            return back();

        $message = Message::create([
            'author_id' => $user['id'],
            'recipient_id' => $recipient,
            'title' => $title,
            'message' => $reply,
            'read' => 0
        ]);

        return redirect('messages')
            ->with('success', 'Message sent!');
    }

    public function postFriends()
    {
        $user = Auth::user();
        $type = request('type');

        $id = request('userId');

        $attUser = User::find($id);

        if (!$attUser || $attUser['id'] == $user['id'])
            return redirect('friends');

        switch ($type) {
            case 'send':
                // check to find if they are already friends
                $friend = $user->friends()->userId($id)->first();
                if ($friend) {
                    if ($friend->is_accepted || $friend->is_pending)
                        return redirect('friends');
                    $friend->from_id = $user->id;
                    $friend->to_id = $id;
                    $friend->is_pending = 1;
                    $friend->save();
                } else {
                    Friend::create([
                        'from_id' => $user->id,
                        'to_id' => $id
                    ]);
                }
                return back();
            case 'cancel':
                $friend = Friend::where([['to_id', $id], ['from_id', $user['id']], ['is_pending', true]])
                    ->first();
                if (!$friend)
                    return redirect('friends');
                $friend->is_pending = 0;
                $friend->is_accepted = 0;
                $friend->save();
                return back();
            case 'remove':
                $friend = $user->friends()->userId($id)->where('is_accepted', true)->first();
                if (!$friend)
                    return back();
                $friend->is_pending = 0;
                $friend->is_accepted = 0;
                $friend->save();

                return back();
            case 'decline':
                $friend = $user->friendRequests()->userId($id)->isPending()->first();
                if (!$friend)
                    return back();
                $friend->is_pending = 0;
                $friend->is_accepted = 0;
                $friend->save();
                return back();
            case 'accept':
                $friend = $user->friendRequests()->userId($id)->isPending()->first();
                if (!$friend)
                    return back();
                $friend->is_pending = 0;
                $friend->is_accepted = 1;
                $friend->save();
                return back();
            default:
                return back();
        }
    }
}
