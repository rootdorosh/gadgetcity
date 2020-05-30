@extends('front.layouts.main')

@section('content')
<div class="l-col1">
    <section class="times clearfix">
        <h2 class="section-name pb18">{!! $seo['h1'] !!}</h2>
        
        @if (!empty($banners['horizontal1']))
        <div class="google_links f_left p_rel">
            {!! $banners['horizontal1'] !!}
        </div>
        @endif
        
        @if (!empty($seo['header_text']))
        <div class="text_size">
            {!! $seo['header_text'] !!}
        </div>
        @endif
        
    </section>
    
    <section class="make">
        <h2 class="section-name_2">{{ t('make.models.list', [$make['title']]) }}</h2>
        
            <ul class="make__vehicle">
            @foreach ($models as $item)    
            <?php /* {{ r('wheels.make.model', [$make['slug'], $item['slug']]) }} */ ?>
            <li>
                @if (!empty($item['image']))
                <div class="make__vehicle-image">
                    <a title="{{ t('make.model.wheels', [$make['title'], $item['title']]) }}" 
                       href="#">
                        <img src="{{ $item['image'] }}" alt="{{ t('make.model.wheels', [$make['title'], $item['title']]) }}"> 
                    </a>
                </div>
                @endif
                <h3>
                    <a title="{{ t('make.model.wheels', [$make['title'], $item['title']]) }}" 
                       href="#">{{ t('make.model.wheels', [$make['title'], $item['title']]) }}</a>
                </h3>
                <ul class="make__vehicle-specs">		
                    <li>{{ t('count_years.models.by.year', [$item['count_years']]) }}, {{ t('count_wheel.wheel.sizes', [$item['count_wheel']]) }}</li>
                    <li>{{ t('the.latest.year.is.year.with.count_wheels.wheel.sets', [$item['last_year'], $item['last_year_count_wheel']]) }}</li>
                </ul>
            </li>
            @endforeach
        </ul>
        
        @if (!empty($banners['horizontal1']))
        <div class="banner-horizontal">
            {{ $banners['horizontal1'] }}
        </div>	
        @endif

    </section>

</div>

<div class="l-col2">

    @if (!empty($banners['vertical1']))
    <div class="banner-ver">
        {!! $banners['vertical1'] !!}
    </div>
    @endif

    <section class="right-block">
        <h2 class="section-name_2">{{ t('make.specs.and.dimensions', [$make['title']]) }}</h2>
        <table class="right-block__specs-list">
            <tbody>				
                <tr>
                    <td>
                        <a class="rim" href="{{ r('wheels.make', [$make['slug']]) }}">{{ t('make.wheels', [$make['title']]) }}</a>
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