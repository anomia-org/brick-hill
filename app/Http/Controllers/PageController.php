<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Carbon\Carbon;

use App\Models\User\{
    User,
    AwardType
};
use App\Models\Set\ClientBuild;

use App\Helpers\Event;

use App\Models\Item\Item;

class PageController extends Controller
{
    public function fallback(Event $event)
    {
        // error handler will automatically return a proper response based on what is wanted
        throw new NotFoundHttpException;
    }

    public function download()
    {
        return view('pages.download')->with([
            'latestVersion' => ClientBuild::where([['is_release', 1], ['is_installer', 0]])->orderBy('id', 'DESC')->first()->tag ?? "None"
        ]);
    }

    public function blog()
    {
        return redirect('https://blog.brick-hill.com');
    }

    public function index()
    {
        if (Auth::check())
            return redirect('dashboard');

        return view('pages.index')->with([
            'user_count' => cache()->remember('total_user_count', 60 * 10, fn () => User::count())
        ]);
    }

    public function privacy()
    {
        return view('statics.privacy');
    }

    public function tos()
    {
        return view('statics.tos');
    }

    public function rules()
    {
        return view('statics.rules');
    }

    public function staff($page = 1)
    {
        $users = User::where('power', '>', \App\Constants\AdminPower::VISIBLE_ADMIN)
            ->orderBy('power', 'DESC')
            ->orderBy('id', 'ASC')
            ->offset((25 * $page) - 25)
            ->limit(25)
            ->get();
        $count = ceil(User::where('power', '>', \App\Constants\AdminPower::VISIBLE_ADMIN)->count() / 25);
        return view('pages.staff')->with([
            'users' => $users,
            'pages' => \App\Helpers\Helper::paginate(25, $page, 12, $count),
            'online' => false
        ]);
    }

    public function awards()
    {
        $realAwards = collect(AwardType::all());

        $awards = $realAwards->keyBy('id');

        $categories = [
            'Membership' => [6, 7, 8, 4],
            'Community' => [1, 2, 3],
            'Excellence' => [5]
        ];

        $colors = [
            'Membership' => 'red',
            'Community' => 'green',
            'Excellence' => 'orange'
        ];

        $finished = [];

        foreach ($categories as $key => $category) {
            $finished[$key] = [];
            foreach ($category as $award) {
                $award = $awards[$award];

                $finished[$key][] = $award;
            }
        }

        return view('pages.awards')->with([
            'categories' => $finished,
            'colors' => $colors
        ]);
    }

    public function searchPage($search = '')
    {
        request()->request->set('limit', 25);
        $query = User::search(
            '',
            function (\OpenSearch\Client $opensearch, string $query, array $options) use ($search) {
                if ($search) {
                    $options['body']['query']['simple_query_string'] = [
                        'fields' => ['name^5', 'past_usernames'],
                        'query' => "*$search*",
                        'analyzer' => 'simple',
                        'default_operator' => 'AND',
                        'analyze_wildcard' => true
                    ];
                } else {
                    unset($options['body']['query']);
                }

                $options['body']['sort'] = [
                    'id' => 'asc'
                ];

                return $opensearch->search($options);
            }
        );

        return view('pages.search')->with([
            'data' => $query->cursorPaginate()->data(),
            'search' => $search,
            'online' => false
        ]);
    }

    public function searchPageOnline($search = '')
    {
        request()->request->set('limit', 25);
        $query = User::search(
            '',
            function (\OpenSearch\Client $opensearch, string $query, array $options) use ($search) {
                if ($search) {
                    $options['body']['query']['bool']['must']['simple_query_string'] = [
                        'fields' => ['name^5', 'past_usernames'],
                        'query' => "*$search*",
                        'analyzer' => 'simple',
                        'default_operator' => 'AND',
                        'analyze_wildcard' => true
                    ];
                } else {
                    unset($options['body']['query']);
                }

                $options['body']['sort'] = [
                    'id' => 'asc'
                ];

                $options['body']['query']['bool']['filter'][]['range'] = [
                    'last_online' => [
                        'gte' => 'now-3m'
                    ]
                ];

                return $opensearch->search($options);
            }
        );

        return view('pages.search')->with([
            'data' => $query->cursorPaginate()->data(),
            'search' => $search,
            'online' => true
        ]);
    }
}
