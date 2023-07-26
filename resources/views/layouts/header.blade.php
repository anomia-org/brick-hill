@php
    $user = Auth::user();

    $admin = ($user && $user->is_admin);

    $nav = [
        'Play' => '/play/',
        'Shop' => '/shop/',
        'Clans' => '/clans/',
        'Users' => '/search/',
        'Forum' => '/forum/',
        'Membership' => '/membership/'
    ];

    $navLoggedIn = [
    ];

    $navAdmin = [
        'Admin' => '/admin/'
    ];

    if($user) {
        $secondaryNav = [
            'Home' => '/dashboard/',
            'Settings' => '/settings/',
            'Avatar' => '/customize/',
            'Profile' => '/user/'.$user['id'].'/',
            'Download' => '/client/',
            'Trades' => '/trades/',
            // pages being removed until added after site release
            //'Invite' => '/invite/',
            //'Advertise' => '/advertise/',
            'Sets' => '/sets/',
            'Currency' => '/currency/',
            'Blog' => '/blog/'
        ];

        $nav = array_merge($navLoggedIn, $nav);
    }

    if($admin) {
        $nav = array_merge($nav, $navAdmin);
        $adminCount = $user->getCount('admin');
    }

    $defaultTheme = 'dark';

    $themeList = [
        1 => 'default',
        2 => 'dark',
        3 => 'default',
        4 => 'dark'
    ];
@endphp

<!DOCTYPE html>
<html lang="en"
    @auth
        @if(array_key_exists(auth()->user()->theme, $themeList))
            class="theme-{{ $themeList[auth()->user()->theme] }}"
        @endif
    @endauth
    @guest
        class="theme-{{ $defaultTheme }}"
    @endguest
>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @stack('meta')
    <meta name="theme-color" content="#00A9FE">
    <meta name="og:image" content="{{ asset('favicon.ico') }}">
    <meta name="og:site_name" content="Brick Hill">
    <meta name="og:description" content="A platform built on its community where you can customise your avatar, create and play games, or just socialise with others!">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    @auth
    <meta 
        name="user-data"
        data-authenticated="true"
        data-id="{{ auth()->id() }}"
        data-username="{{ auth()->user()->username }}"
        data-membership="{{ auth()->user()->membership->membership ?? false }}"
        data-bucks="{{ auth()->user()->bucks }}"
        data-bits="{{ auth()->user()->bits }}"
        data-tax-rate="{{ auth()->user()->membership_limits->tax_rate }}"
        data-admin="{{ auth()->user()->is_admin ? "true" : "false" }}"
    >
    @endauth
    @guest
    <meta
        name="user-data"
        data-authenticated="false"
    >
    @endguest

    @hasSection('title')
        <title>@yield('title') - {{ config('app.name', 'Brick Hill') }}</title>
        <meta name="og:title" content="@yield('title') - {{ config('app.name', 'Brick Hill') }}">
    @else
        <title>{{ config('app.name', 'Brick Hill') }}</title>
        <meta name="og:title" content="{{ config('app.name', 'Brick Hill') }}">
    @endif

    <link href="{{ asset('favicon.ico') }}" rel="icon">

    @auth
        @if(array_key_exists(auth()->user()->theme, $themeList))
        <link href="{{ mix($themeList[auth()->user()->theme].'.css') }}" rel="stylesheet" type="text/css">
        @else
        <link href="{{ mix('default.css') }}" rel="stylesheet" type="text/css">
        @endif
    @endauth
    @guest
    <link href="{{ mix("$defaultTheme.css") }}" rel="stylesheet" type="text/css">
    @endguest
    <link href="{{ mix('new_theme.css') }}" rel="stylesheet" type="text/css">
    <link
        rel="stylesheet"
        href="https://use.fontawesome.com/releases/v5.6.3/css/all.css"
        integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/"
        crossorigin="anonymous"
    >
    @stack('css')

    @if(app()->environment(['prod', 'production']))
    <script type="text/javascript" async="true">
        !function(){var e=document.createElement("script"),t=document.getElementsByTagName("script")[0],
        a="https://cmp.quantcast.com".concat("/choice/","CH96B6ycUs-aM","/","www.brick-hill.com","/choice.js?tag_version=V2"),
        s=0;e.async=!0,e.type="text/javascript",e.src=a,t.parentNode.insertBefore(e,t),
        !function e(){for(var t,a="__tcfapiLocator",s=[],i=window;i;){try{if(i.frames[a]){t=i;break}}catch(n){}if(i===window.top)break;
        i=i.parent}t||(!function e(){var t=i.document,s=!!i.frames[a];if(!s){if(t.body){var n=t.createElement("iframe");
        n.style.cssText="display:none",n.name=a,t.body.appendChild(n)}else setTimeout(e,5)}return!s}(),
        i.__tcfapi=function e(){var t,a=arguments;if(!a.length)return s;if("setGdprApplies"===a[0])
        a.length>3&&2===a[2]&&"boolean"==typeof a[3]&&(t=a[3],"function"==typeof a[2]&&a[2]("set",!0));
        else if("ping"===a[0]){var i={gdprApplies:t,cmpLoaded:!1,cmpStatus:"stub"};
        "function"==typeof a[2]&&a[2](i)}else"init"===a[0]&&"object"==typeof a[3]&&(a[3]=Object.assign(a[3],{tag_version:"V2"}))
        ,s.push(a)},i.addEventListener("message",function e(t){var a="string"==typeof t.data,s={};
        try{s=a?JSON.parse(t.data):t.data}catch(i){}var n=s.__tcfapiCall;n&&
        window.__tcfapi(n.command,n.version,function(e,s){var i={__tcfapiReturn:{returnValue:e,success:s,callId:n.callId}};
        a&&(i=JSON.stringify(i)),t&&t.source&&t.source.postMessage&&t.source.postMessage(i,"*")},n.parameter)},!1))}();
        var i=function(){var e=arguments;typeof window.__uspapi!==i&&setTimeout(function(){
        void 0!==window.__uspapi&&window.__uspapi.apply(window.__uspapi,e)},500)},
        n=function(){s++,window.__uspapi===i&&s<3?console.warn("USP is not accessible"):clearInterval(c)};
        if(void 0===window.__uspapi){window.__uspapi=i;var c=setInterval(n,6e3)}}();
    </script>
    <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
        ga('create', 'UA-122702268-1', 'auto');
        ga('send', 'pageview')
    </script>
    @endif

    <script src="{{ mix('runtime.js') }}"></script>
    <script src="{{ mix('register.js') }}"></script>
    <script src="{{ mix('vendor.js') }}"></script>
    @if(request()->root() == config('site.admin_url'))
    <script src="{{ mix('superadmin.js') }}"></script>
    @endif
    @if(auth()->check() && auth()->user()->is_admin)
    <script src="{{ mix('admin.js') }}"></script>
    @endif
    <script src="{{ mix('vue.js') }}"></script>
    <script src="{{ mix('legacy.js') }}"></script>
    @stack('scripts')

    <meta name="author" content="Brick Hill">
</head>
<body>
    <nav>
        <div class="primary">
            <div class="grid">
                <div class="push-left">
                    <ul>
                        @foreach ($nav as $name => $loc)
                        <li>
                            <a href="{{ $loc }}">
                                {{ $name }}
                                @if ($name == 'Admin' && $adminCount > 0)
                                    <span class="nav-notif">{{ $adminCount }}</span>
                                @endif
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
                <div class="nav-user push-right" id="info">
                    @if (Auth::check())
                    <div class="info">
                        <a href="/currency" class="header-data" title="{{ $user->bucks }}">
                            <span class="bucks-icon img-white"></span>
                            {{ Helper::numAbbr($user->bucks) }}
                        </a>
                        <a href="/currency" class="header-data" title="{{ $user->bits }}">
                            <span class="bits-icon img-white"></span>
                            {{ Helper::numAbbr($user->bits) }}
                        </a>
                        <a href="/messages" class="header-data">
                            <span class="messages-icon img-white"></span>
                            {{ Helper::numAbbr($user->receivedMessages()->unread()->count()) }}
                        </a>
                        <a href="/friends" class="header-data">
                            <span class="friends-icon img-white"></span>
                            {{ Helper::numAbbr($user->friendRequests()->count()) }}
                        </a>
                    </div>
                    @endif
                    @auth
                    <div class="username ellipsis">
                        <div id="username-bar">
                            <div class="username-holder ellipsis inline unselectable">{{ $user['username'] }}</div>
                            <i class="arrow-down img-white"></i>
                        </div>
                    </div>
                    @endauth
                    @guest
                    <div class="username login-buttons">
                        <a href="/login" class="login-button">Login</a>
                        <a href="/register" class="register-button">Register</a>
                    </div>
                    @endguest
                   
                </div>
            </div>
        </div>
        @auth
        <div class="secondary">
            <div class="grid">
                <div class="bottom-bar">
                    <ul>
                        @foreach ($secondaryNav as $name => $loc)
                        <li>
                            <a href="{{ $loc }}" id="p{{ $name }}">
                                {{ $name }}
                                @if ($name == 'Trades' && ($tradeCount = $user->trades()->count()) > 0)
                                    <span class="nav-notif">{{ $tradeCount }}</span>
                                @endif
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endauth
    </nav>
    @auth
    <dropdown id="dropdown-v" class="dropdown" activator="username-bar" contentclass="logout-dropdown">
        <ul>
            <li>
                <a onclick="document.getElementById('logout').submit()">Logout</a>
            </li>
        </ul>
        <form method="POST" action="{{ route('logout') }}" id="logout">
            @csrf
        </form>
    </dropdown>
    @endauth

    @ads
    <div class="side-ad" id="100128-4">
        <script src="//ads.themoneytizer.com/s/gen.js?type=4"></script>
        <script src="//ads.themoneytizer.com/s/requestform.js?siteId=100128&formatId=4"></script>
    </div>
    <div class="side-ad" style="right:0;" id="100128-20">
        <script src="//ads.themoneytizer.com/s/gen.js?type=20"></script>
        <script src="//ads.themoneytizer.com/s/requestform.js?siteId=100128&formatId=20"></script>
    </div>
    @endads

    @hasSection('content-no-grid')
        @yield('content-no-grid')
    @endif

    @if($event_seen ?? false)
    <mover id="mover-v" event_key="{{ session('event_key') }}" event_type="{{ $event_seen }}"></mover>
    @endif

    @hasSection('content')
    <div class="main-holder grid">
        @if($site_banner ?? false)
        @banner
        <div class="col-10-12 push-1-12">
            <div class="alert warning">
                @if($site_banner_url ?? false)
                <a href="{{ $site_banner_url }}">
                    {{ $site_banner }}
                </a>
                @else
                {{ $site_banner }}
                @endif
            </div>
        </div>
        @endbanner
        @endif
        <notification id="notification-v" class="notification"></notification>
        @auth
        @if(($email_sent ?? false) && !session('denied_email'))
        <div class="col-10-12 push-1-12">
            <div class="alert success">
                You need to verify your email!
                <a href="{{ route('sendEmail') }}" class="button small red" style="margin-right:15px;margin-left:10px;">Send Email</a>
                <a href="{{ route('cancelEmail') }}" class="button small red">No thanks</a>
            </div>
        </div>
        @endif
        @if($email_verified ?? false)
        <div class="col-10-12 push-1-12">
            <div class="alert success">
                Verify your email {{ $email_verified }}
                <a href="{{ route('settings') }}" class="button small red">Change Email</a>
            </div>
        </div>
        @endif
        @if(($needs_email ?? false) && !session('denied_add'))
        <div class="col-10-12 push-1-12">
            <div class="alert error">
                You don't have an email attached to your account!
                <a href="/settings" class="button small green" style="margin-right:15px;margin-left:10px;">Add One</a>
                <a href="{{ route('cancelEmailAdd') }}" class="button small red">No thanks</a>
            </div>
        </div>
        @endif
        @if(session('no_recovery_codes'))
        <div class="col-10-12 push-1-12">
            <div class="alert error">
                <a href="{{ route('settings') }}">
                You have no 2FA recovery codes left! Generate more on the settings page
                </a>
            </div>
        </div>
        @endif
        @endauth
        @if(($errors ?? false) && (!$errors->isEmpty() || $errors->has('errors') || session('success')))
            <div class="col-10-12 push-1-12">
                @switch((!$errors->isEmpty() || $errors->has('errors')) ? 'error' : (session('success') ? 'success' : ''))
                    @case('error')
                        <div class="alert error">
                        @break
                    @case('success')
                        <div class="alert success">
                        @break
                    @default
                        <div class="alert warning">
                @endswitch
                    {{ ($errors->first('errors')) ?: ($errors->first() ?: session('success')) }}
                </div>
            </div>
        @endif
        @yield('content')
        @ads
        <div class="col-10-12 push-1-12">
            <div style="text-align:center;margin-top:20px;padding-bottom:25px;">
                <div id="100128-28">
                    <script src="//ads.themoneytizer.com/s/gen.js?type=28"></script>
                    <script src="//ads.themoneytizer.com/s/requestform.js?siteId=100128&formatId=28"></script>
                </div>
            </div>
        </div>
        @endads
    </div>
    @endif
    <main-footer id="mainfooter-v"></main-footer>
</body>
</html>
