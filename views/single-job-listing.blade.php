@extends('templates.master')

@section('content')

    <div class="container main-container">
        @include('partials.breadcrumbs')

        <div class="grid  grid--columns">
            <div class="grid-md-12 grid-lg-9">
                @if (is_single() && is_active_sidebar('content-area-top'))
                    <div class="grid grid--columns sidebar-content-area sidebar-content-area-top">
                        <?php dynamic_sidebar('content-area-top'); ?>
                    </div>
                @endif

                <div class="grid">
                    <div class="grid-sm-12">
                        {!! the_post() !!}
                        <?php

                        $postMeta = get_post_meta(get_the_ID());
                        global $post;

                        $todaysDate = date_create(date('Y-m-d'));
                        $endDate = date_create(substr($postMeta['application_end_date'][0], 0,
                            strpos($postMeta['application_end_date'][0], "T")));
                        $diff = date_diff($todaysDate, $endDate);
                        
                        ?>
                        <div class="grid">
                            <div class="grid-xs-12">
                                <div class="post post-single">

                                    @include('partials.blog.post-header')
                                    @if ($postMeta['application_end_date'][0] < date('Y-m-d'))
                                        <button class="btn btn-lg btn-contrasted disabled btn-block">
                                            Ansökningstiden har gått ut
                                        </button>
                                    @else
                                        <a class="btn btn-lg btn-block btn-primary btn-outline" href="{{get_field('apply_button_url',
                                            'option')}}{{$postMeta['guid'][0]}}">Ansök här ({{$diff->format("%r%a")}}
                                            dagar kvar) </a>
                                    @endif
                                    <br/>
                                    <article class="u-mb-5" id="article">
                                        @if (post_password_required($post))
                                            {!! get_the_password_form() !!}
                                        @else

                                            {!! apply_filters('the_content', $post->post_content) !!}

                                        @endif
                                    </article>

                                    @if (is_single() && is_active_sidebar('content-area'))
                                        <div class="grid grid--columns sidebar-content-area sidebar-content-area-bottom">
                                            <?php dynamic_sidebar('content-area'); ?>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @include('partials.blog.post-footer')
                    </div>
                </div>
            </div>
            <aside class="grid-lg-3 grid-md-12 sidebar-right-sidebar hidden-xs hidden-sm hidden-md">

                <div class="grid grid--columns">
                    <div class="box box-card">
                        <div class="box-content">

                            <b>Sista ansökningsdatum:</b><br/>
                            {{substr($postMeta['application_end_date'][0], 0,
                                        strpos($postMeta['application_end_date'][0], "T"))}}
                            <span class="text-sm">({{$diff->format("%r%a")}} dagar kvar)</span>
                            <br/><br/>
                            <b>Ref nr:</b><br/>
                            {{$postMeta['ad_reference_nbr'][0]}}
                            <br/><br/>
                            <b>Publicerad:</b><br/>
                            {{substr($postMeta['publish_start_date'][0], 0,
                                      strpos($postMeta['publish_start_date'][0], "T"))}}
                            <br/><br/>

                            <b>Antal sökande:</b><br/>
                            {{$postMeta['number_of_positions'][0]}}

                            <br/><br/>
                            <b>Erfarenhet:</b><br/>
                            {{$postMeta['work_experience'][0]}}

                            <br/><br/>
                            <b>Anställningstyp:</b><br/>
                            {{$postMeta['employment_type'][0]}}

                            <br/><br/>
                            <b>Omfattning:</b><br/>
                            {{$postMeta['employment_grade'][0]}}

                            <br/><br/>
                            <b>Kommun:</b><br/>
                            {{$postMeta['location_name'][0]}}
                            <br/><br/>
                            @if ($postMeta['application_end_date'][0] < date('Y-m-d'))
                                <button class="btn btn-lg btn-contrasted disabled btn-block">Ansökningstiden har gått
                                    ut
                                </button>
                            @else

                                <a target="_blank"
                                   class="btn btn-lg btn-block btn-primary btn-outline"
                                   href="{{get_field('apply_button_url','option')}}{{$postMeta['guid'][0]}}">Ansök här
                                    ({{$diff->format("%r%a")}} dagar kvar)
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </div>

@stop

