<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <title>{{ FrontPage::getTitle() }}</title>
        <meta name="keywords" content="{{ FrontPage::getKeywords() }}" />
        <meta name="description" content="{{ FrontPage::getDescription() }}" />

        <meta name="viewport" content="initial-scale=1"/>
        <link rel="stylesheet" media="screen" href="/front/css/screen.css" >
        <link rel="stylesheet" href="/front/css/responsive.css" />
        <link rel="stylesheet" href="/front/css/bulbicons.css" />
        <link rel="stylesheet" href="/front/css/app.css" />
        <!--[if IE]><script src="/js/html5shiv.js"></script><![endif]-->
    </head>
    <body class="l">
        <header>
            <a href="/" class="logo" title="{{ FrontPage::getSeoParamByPage('home', 'title') }}">{{ FrontPage::getDomain()->alias }}</a>
        </header>
        <nav>
            <ul class="menu-list">
                <li class="is-active"><a href="{{ r('home') }}">{{ t('all.cars') }}</a></li>
                <!--<li><a href="{{ r('wheels') }}">{{ t('wheels') }}</a></li>-->
            </ul>
        </nav>

        @if (!empty(FrontPage::getBreadcrumbs()))
        <ul class="breadcrumb">
			<li>
				<a href="/" title="{{ FrontPage::getSeoParamByPage('home', 'breadc_title') }}">{{ FrontPage::getSeoParamByPage('home', 'breadc_label') }}</a><span>→</span>
			</li>
            @foreach (FrontPage::getBreadcrumbs() as $item)
                @if(!$loop->last)
                    <li>
                        <a href="{{ $item['url'] }}" title="{{ $item['title'] }}">{{ $item['label'] }}</a><span>→</span>
                    </li>
                @else
                    <li>
                        <a href="#" title="{{ $item['title'] }}">{{ $item['label'] }}</a>
                    </li>
                @endif
            @endforeach
        </ul>
        @endif

        <main>
            @yield('content')
        </main>

        <footer>
            <section class="footer__copyright">
                <div>
                    <h4>GadgetCity</h4>
                    @foreach ((new App\Modules\Core\Services\Fetch\DomainFetchService)->getData() as $domain)
                        @if (FrontPage::getDomain()->id !== $domain->id)
                            <a title="{{ $domain->title }}" href="//{{ FrontPage::getCurrentUrlByDomain($domain)}}">
                                <img class="flag-icon" src="/front/img/flags/{{ $domain->code }}.png">
                            </a>
                        @endif
                    @endforeach
                </div>
                &copy 2014-{{ date('Y') }} {{ FrontPage::getDomain()->alias }} {{ t('all.rights.reserved') }}
                <!--<a rel="nofollow" href="/about.html">About us</a>-->
            </section>
        </footer>

        <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
        <script src="/front/js/app.js"></script>
    </body>
</html>

