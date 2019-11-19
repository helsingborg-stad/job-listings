@extends('templates.master')

@section('content')

    <?php global $post; ?>

    <div class="container main-container job-listings">
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
                        <?php $postMeta = get_post_meta(get_the_ID());?>

                        @if(isset($postMeta['has_expired'][0]))
                            @if ($postMeta['has_expired'][0] === '1')
                            <div class="gutter gutter-bottom">
                                <div class="notice warning">
                                    <i class="pricon pricon-notice-warning"></i> <?php _e("The application period for this reqruitment has ended."); ?>
                                </div>
                            </div>
                            @endif
                        @endif

                        <div class="grid">
                            <div class="grid-xs-12">
                                <div class="post post-single">

                                    <article class="u-mb-5" id="article">

                                        <div class="box box-card">
                                            <div class="box-content">

                                                @if(isset($postMeta['has_expired'][0]))
                                                    @if ($postMeta['has_expired'][0] === '0')
                                                        <a target="_blank"
                                                        class="btn btn-lg btn-primary btn-floating-application"
                                                        href="{{$postMeta['external_url'][0]}}">
                                                            <?php _e('Apply now', 'job-listings'); ?>
                                                        </a>
                                                    @endif
                                                @endif

                                                @include('partials.blog.post-header')
                                            
                                                @if (post_password_required(get_post()))
                                                    {!! get_the_password_form() !!}
                                                @else

                                                @if (isset($postMeta['preamble'][0]) && !empty($postMeta['preamble'][0]))
                                                    <p class="lead">
                                                        {{$postMeta['preamble'][0]}}
                                                    </p>
                                                @endif

                                                {!! apply_filters('the_content', get_post()->post_content) !!}

                                            </div>
                                        </div>

                                            @if (isset($postMeta['legal_details'][0]) && !empty($postMeta['legal_details'][0]))
                                                <div class="box box-card">
                                                    <div class="box-content">
                                                        <div class="small">
                                                            {{$postMeta['legal_details'][0]}}
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
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
            <aside class="grid-lg-3 grid-md-12 sidebar-left-sidebar">

                <div class="grid--columns">
                    <div class="box box-card">
                        <div class="box-content">
                            <ul class="unlist job-listing-sidenav">
                                @if (isset($postMeta['application_end_date'][0]) && !empty($postMeta['application_end_date'][0]))
                                    <li><b><?php _e('Deadline for applications:', 'job-listings'); ?></b><br/>
                                        @if ($postMeta['has_expired'][0] === '1')
                                            <button class="btn btn-lg btn-contrasted disabled btn-block">
                                                <?php _e('The application period has ended', 'job-listings'); ?>
                                            </button>
                                        @else
                                            {{ $postMeta['application_end_date'][0] }}
                                            <span class="text-sm">
                                                ({{ $postMeta['number_of_days_left'][0] }} 
                                                <?php _e('days left','job-listings'); ?>)
                                            </span>
                                        @endif

                                    </li>
                                @endif

                                @if (isset($postMeta['ad_reference_nbr'][0]) && !empty($postMeta['ad_reference_nbr'][0]))
                                    <li class="gutter gutter-top">
                                        <b><?php _e('Reference:', 'job-listings'); ?></b><br/>
                                        {{$postMeta['ad_reference_nbr'][0]}}

                                    </li>
                                @endif

                                @if (isset($postMeta['publish_start_date'][0]) && !empty($postMeta['publish_start_date'][0]))
                                    <li class="gutter gutter-top">
                                        <b><?php _e('Published:', 'job-listings'); ?></b><br/>
                                        {{ $postMeta['publish_start_date'][0] }}
                                        {{substr($postMeta['publish_start_date'][0], 0,
                                                  strpos($postMeta['publish_start_date'][0], "T"))}}

                                    </li>
                                @endif

                                @if (isset($postMeta['number_of_positions'][0]) && !empty($postMeta['number_of_positions'][0]))
                                    <li class="gutter gutter-top">
                                        <b><?php _e('Number of positions:', 'job-listings'); ?></b><br/>
                                        {{$postMeta['number_of_positions'][0]}}

                                    </li>
                                @endif

                                @if (isset($postMeta['work_experience'][0]) && !empty($postMeta['work_experience'][0]))
                                    <li class="gutter gutter-top">
                                        <b><?php _e('Experience:', 'job-listings'); ?></b><br/>
                                        {{$postMeta['work_experience'][0]}}

                                    </li>
                                @endif

                                @if (isset($postMeta['employment_type'][0]) && !empty($postMeta['employment_type'][0]))
                                    <li class="gutter gutter-top">
                                        <b><?php _e('Employment type:', 'job-listings'); ?></b><br/>
                                        {{$postMeta['employment_type'][0]}}

                                    </li>
                                @endif

                                @if (isset($postMeta['employment_grade'][0]) && !empty($postMeta['employment_grade'][0]))
                                    <li class="gutter gutter-top">
                                        <b><?php _e('Extent:', 'job-listings'); ?></b><br/>
                                        {{$postMeta['employment_grade'][0]}}

                                    </li>
                                @endif

                                @if (isset($postMeta['employment_grade'][0]) && !empty($postMeta['employment_grade'][0]))
                                    <li class="gutter gutter-top"><b><?php _e('Location:', 'job-listings'); ?></b><br/>
                                        {{$postMeta['location_name'][0]}}</li>

                                @endif

                                @if(isset($postMeta['departments'][0]) && !empty($postMeta['departments'][0]))
                                    <li class="gutter gutter-top">
                                        <b><?php _e('Company:', 'job-listings'); ?></b><br/>
                                        {{ucfirst(mb_strtolower($postMeta['departments'][0]))}}
                                    </li>
                                @endif

                            </ul>
                        </div>

                    </div>

                    @if(isset($postMeta['contact'][0]) && !empty($postMeta['contact'][0]))
                        @foreach(unserialize($postMeta['contact'][0]) as $person)
                            <div class="box box-card">
                                <div class="box-content">

                                    <h3><?php _e('Contact', 'job-listings'); ?></h3>
                                    <ul class="unlist job-listing-sidenav">

                                        @if (isset($person['name']) && !empty($person['name']))
                                            <li class="strong">{{$person['name']}}</li>
                                        @endif

                                        @if (isset($person['position']) && !empty($person['position']))
                                            <li class="small gutter gutter-bottom">{{$person['position']}}</li>
                                        @endif

                                        @if (isset($person['phone']) && !empty($person['phone']))
                                            <li class="link-item link-item-phone"><a href="tel:{{$person['phone_sanitized']}}">{{$person['phone']}}</a></li>
                                        @endif

                                    </ul>
                                </div>
                            </div>
                        @endforeach
                    @endif

                    @if(isset($postMeta['has_expired'][0]))
                        @if ($postMeta['has_expired'][0] === '1')
                            <div class="gutter gutter-top">
                                <button class="btn btn-lg btn-contrasted disabled btn-block">
                                    <?php _e('The application period has ended', 'job-listings'); ?>
                                </button>
                            </div>
                        @else
                            <div class="gutter gutter-top">
                                <a target="_blank"
                                class="btn btn-lg btn-block btn-primary btn-outline"
                                href="{{$postMeta['external_url'][0]}}"><?php _e('Apply here',
                                        'job-listings'); ?> ({{ $postMeta['number_of_days_left'][0] }} <?php _e('days left',
                                        'job-listings'); ?>)
                                </a>
                            </div>
                        @endif
                    @endif

            </aside>
        </div>
    </div>

@stop

