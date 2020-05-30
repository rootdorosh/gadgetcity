@extends('front.layouts.main')

@section('content')
<div class="l-col1">
    <section class="all-makes">
        <h1 class="section-name_2">{{ t('select.the.car.make') }} </h1>
        <ul>
        @foreach ($makes as $index => $make)
            <li><a href="{{ r('wheels.make', [$make['slug']]) }}">{{ $make['title'] }}</a></li>
            @if (($index + 1) % 7 == 0)
                </ul><ul>
            @endif            
        @endforeach    
        </ul>
    </section>	
    
    @if (!empty($banners['horizontal1']))
    <div class="banner-horizontal">
        {!! $banners['horizontal1'] !!}
    </div>
    @endif
    
    @if (!empty(FrontPage::getHeaderText()))
    <section class="seo-text">
        {!! FrontPage::getHeaderText() !!}
    </section>
    @endif

    <section class="options">
        <h2 class="section-name_2">{{ t('select.a.car') }}</h2>
        <div class="options__block">
            <div class="options__item">
                <strong>{{ t('year') }}</strong>
                <select id="home-filter-year">
                    <option value="">-{{ t('select') }}</option>
                    @foreach ($years as $year)
                    <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <div class="options__item">
                <strong>{{ t('make') }}</strong>
                <select id="home-filter-make">
                    <option value="">-{{ t('select') }}</option>
                </select>
            </div>
            <div class="options__item">
                <strong>{{ t('model') }}</strong>
                <select id="home-filter-model">
                    <option value="">-{{ t('select') }}</option>
                </select>
            </div>
            <a href="#" data-url="{{ FrontPage::getSeoParamByPage('wheels.make.model', 'url') }}" class="btn btn_options hidden" id="home-filter-btn-">{{ t('go') }}</a
        </div>
    </section>

    @if (!empty($banners['horizontal2']))
    <div class="banner-horizontal">
        {!! $banners['horizontal2'] !!}
    </div>
    @endif
    
    <section class="most-visited">
        <h2 class="section-name_2">{{ t('most.visited.models') }}</h2>
        <table>
            <tr>
            @foreach ($mostVisitedModels as $index => $item)    
                <?php /* {{ r('wheels.make.model', [$item['make_slug'], $item['model_slug']]) }} */ ?>
                <td>
                    <a href="#">
                        <img src="{{ $item['model_image'] }}" 
                             alt="{{ $item['make_title'] }} {{ $item['model_title'] }}"
                             title="{{ $item['make_title'] }} {{ $item['model_title'] }}"
                        >
                    </a>
                </td>
                @if (($index + 1) % 3 == 0)
                    </tr><tr>
                @endif            
            @endforeach
            </tr>
        </table>
    </section>

</div>

<div class="l-col2">
    
    @if (!empty($banners['vertical1']))
    <div class="banner-ver">
        {!! $banners['vertical1'] !!}
    </div>
    @endif
   
    <section class="right-block">
        <h2 class="section-name_2">{{ t('car.specs.and.dimensions') }}</h2>
        <table class="right-block__specs-list">
            <tbody>
                <tr>
                    <td>
                        <!--<a class="rim" href="{{ r('wheels') }}">{{ t('wheels') }}</a>-->
                    </td>						
                </tr>
            </tbody>
        </table>
    </section>

    @if (!empty($banners['vertical2']))
    <div class="banner-ver">
        {!! $banners['vertical2'] !!}
    </div>
    @endif

</div>
@endsection