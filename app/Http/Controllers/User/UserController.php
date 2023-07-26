<?php

namespace App\Http\Controllers\User;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

use Carbon\Carbon;

use App\Models\Set\Set;
use App\Models\User\{
    Ban,
    User,
    Status,
    Message,
    Admin\Report
};
use App\Models\Clan\ClanMember;
use App\Models\Item\{
    SpecialSeller,
    BuyRequest
};

class UserController extends Controller
{
    public function currencyPage()
    {
        $items = SpecialSeller::userId(Auth::id())->active()->get();
        $requests = BuyRequest::userId(Auth::id())->active()->get();

        return view('pages.user.currency')->with([
            'items_on_sale' => $items,
            'buy_requests' => $requests
        ]);
    }

    public function friends($page = 1)
    {
        $user = Auth::user();

        $friends = $user->friendRequests()
            ->limit(25)
            ->offset(25 * ($page - 1))
            ->orderBy('id', 'DESC')
            ->get();
        $pageCount = ceil($user->friendRequests->count() / 25);

        return view('pages.user.friends')->with([
            'friends' => $friends,
            'page' => \App\Helpers\Helper::paginate(25, $page, 6, $pageCount, '/friends/')
        ]);
    }

    public function viewMessage($id)
    {
        $user = Auth::user();

        $message = Message::find($id);

        if (!$message || !($message['recipient_id'] == $user['id'] || $message['author_id'] == $user['id'])) {
            if ($user->power == 0 || !$message)
                return redirect('messages');
            $report = Report::type(7)
                ->assetid($message->id)
                ->notseen()
                ->count();
            if ($report == 0)
                return redirect('messages');
        }

        if ($message['recipient_id'] == $user['id'] && $message['read'] == 0) {
            $message->read = 1;
            $message->timestamps = false;
            $message->save();
        }

        return view('pages.user.messages.message')->with([
            'message' => $message
        ]);
    }

    public function messages($page = 1)
    {
        $user = Auth::user();

        $messages = Message::where('recipient_id', $user['id'])
            ->with(['sender', 'receiver'])
            ->orderBy('id', 'DESC')
            ->limit(25)
            ->offset((25 * $page) - 25)
            ->select(['id', 'title', 'read', 'author_id', 'recipient_id', 'updated_at'])
            ->get();
        $pageCount = ceil(Message::where('recipient_id', $user['id'])->count() / 25);

        return view('pages.user.messages.messages')->with([
            'messages' => $messages,
            'page' => \App\Helpers\Helper::paginate(25, $page, 6, $pageCount)
        ]);
    }

    public function outbox($page = 1)
    {
        $user = Auth::user();

        $messages = Message::where('author_id', $user['id'])
            ->with(['sender', 'receiver'])
            ->orderBy('id', 'DESC')
            ->limit(25)
            ->offset((25 * $page) - 25)
            ->select(['id', 'title', 'read', 'author_id', 'recipient_id', 'updated_at'])
            ->get();
        $pageCount = ceil(Message::where('author_id', $user['id'])->count() / 25);

        return view('pages.user.messages.outbox')->with([
            'messages' => $messages,
            'page' => \App\Helpers\Helper::paginate(25, $page, 6, $pageCount)
        ]);
    }

    public function setsPage()
    {
        $userSets = Set::where('creator_id', Auth::id())
            ->with('server', 'latestAsset')
            ->get();

        return view('pages.user.sets')->with([
            'sets' => $userSets
        ]);
    }

    public function dash()
    {
        $user = Auth::user();

        return view('pages.user.dashboard')->with([
            'feed' => $user->feed,
            'friends' => $user->friends()->orderBy('id', 'desc')->isAccepted()->limit(4)->get(),
            'friend_count' => $user->friends()->isAccepted()->count(),
            'streak' => $user->streak
        ]);
    }

    public function profilePage($id)
    {
        $user = User::findOrFail($id);
        $status = Status::where('owner_id', $id)
            ->whereNull('clan_id')
            ->orderBy('id', 'DESC')
            ->first();
        $clans = ClanMember::where([['user_id', $id], ['status', 'accepted']])
            ->limit(6)
            ->with('clan')
            ->get();
        $friended = (Auth::check()) ? Auth::user()->friends()->userId($id)->orderBy('id', 'DESC')->first() : false;

        if ($friended && $friended->is_pending) {
            if ($friended->from_id != Auth::id()) {
                $friendedVals = ['accept', 'green', 'ACCEPT FRIEND'];
            } else {
                $friendedVals = ['cancel', 'red', 'CANCEL FRIEND'];
            }
        } else if ($friended && $friended->is_accepted) {
            $friendedVals = ['remove', 'red', 'REMOVE FRIEND'];
        } else {
            $friendedVals = ['send', 'blue', 'FRIEND'];
        }

        return view('pages.user.profile')->with([
            'user' => $user,
            'status' => $status,
            'friends' => $user->friends()->isAccepted()->limit(6)->get(),
            'clans' => $clans,
            'friended' => $friendedVals,
            'totalVisits' => $user->visits,
            'awards' => $user->awards,
            'banned' => $user->bans()->active()->exists()
        ]);
    }

    public function setsAPI($id)
    {
        $user = User::findOrFail($id);
        $sets = Set::where('creator_id', $id)->with('thumbnailAsset')
            ->get()->each->append('thumbnail');

        return [
            'data' => $sets
        ];
    }

    public function banned()
    {
        $checkForBan = Ban::userId(Auth::id())
            ->active()
            ->first();
        if (!$checkForBan)
            return back();

        $isBanPast = Carbon::parse($checkForBan->created_at)->addMinutes($checkForBan->length)->isPast();

        return view('pages.user.banned')->with([
            'ban' => $checkForBan,
            'past' => $isBanPast
        ]);
    }

    public function attemptUnban()
    {
        $checkForBan = Ban::userId(Auth::id())
            ->active()
            ->first();
        if (!$checkForBan)
            return back();
        $isBanPast = Carbon::parse($checkForBan->created_at)->addMinutes($checkForBan->length)->isPast();
        if (!$isBanPast)
            return redirect()
                ->route('banned');

        $checkForBan->active = 0;
        $checkForBan->save();
        return redirect('/dashboard');
    }

    public function userFriends($id, $page = 1)
    {
        $user = User::findOrFail($id);

        $friends = $user->friends()->isAccepted();
        $pageCount = ceil($friends->count() / 25);

        return view('pages.user.userfriends')->with([
            'friends' => $friends->limit(25)->offset(25 * ($page - 1))->get(),
            'page' => \App\Helpers\Helper::paginate(25, $page, 6, $pageCount, "/user/$id/friends/")
        ]);
    }

    public function userClans($id)
    {
        $user = User::findOrFail($id);
        $clans = ClanMember::where([['user_id', $id], ['status', 'accepted']])
            ->get()
            ->each
            ->append('clan');

        return view('pages.user.clans')->with([
            'user' => $user,
            'clans' => $clans
        ]);
    }
}
