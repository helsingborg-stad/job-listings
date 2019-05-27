@extends('templates.master')

@section('content')

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
                        <?php

                        //Fetching Postmeta
                        // Diffing dates
                        $postMeta = get_post_meta(get_the_ID());
                        global $post;

                        if (isset($postMeta['application_end_date'][0]) && !empty($postMeta['application_end_date'][0])) {
                            $todaysDate = date_create(date('Y-m-d'));
                            $endDate = date_create(substr($postMeta['application_end_date'][0], 0,
                                strpos($postMeta['application_end_date'][0], "T")));
                            $diff = date_diff($todaysDate, $endDate);

                            $days = (int)$diff->format("%r%a");

                        }


                        ?>
                        <div class="grid">
                            <div class="grid-xs-12">
                                <div class="post post-single">

                                    @include('partials.blog.post-header')
                                    @if (isset($postMeta['application_end_date'][0]) && !empty($postMeta['application_end_date'][0]))
                                        @if ($postMeta['application_end_date'][0] < date('Y-m-d'))
                                            <button class="btn btn-lg btn-contrasted disabled btn-block">
                                                <?php _e('The application period has ended', 'job-listings'); ?>
                                            </button>
                                        @else

                                            <a target="_blank"
                                               class="btn btn-lg btn-block btn-primary btn-outline"
                                               href="{{get_field('apply_button_url','option')}}{{$postMeta['guid'][0]}}"><?php _e('Apply here',
                                                    'job-listings'); ?> ({{$diff->format("%r%a")}} <?php _e('days left',
                                                    'job-listings'); ?>)
                                            </a>
                                        @endif
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
            <aside class="grid-lg-3 grid-md-12 sidebar-left-sidebar">

                <div class="grid--columns">
                    <div class="box box-card">
                        <div class="box-content">
                            <ul class="unlist job-listing-sidenav">
                                @if (isset($postMeta['application_end_date'][0]) && !empty($postMeta['application_end_date'][0]))
                                    <li><b><?php _e('Deadline for applications:', 'job-listings'); ?></b><br/>
                                        @if ($postMeta['application_end_date'][0] > date('Y-m-d'))
                                            {{substr($postMeta['application_end_date'][0], 0,
                                                        strpos($postMeta['application_end_date'][0], "T"))}}
                                            <span class="text-sm">({{$diff->format("%r%a")}} <?php _e('days left',
                                                    'job-listings'); ?>)</span>
                                        @else
                                            {{substr($postMeta['application_end_date'][0], 0,
                                                        strpos($postMeta['application_end_date'][0], "T"))}}
                                        @endif

                                    </li>
                                @endif

                                @if (isset($postMeta['ad_reference_nbr'][0]) && !empty($postMeta['ad_reference_nbr'][0]))
                                    <li>
                                        <b><?php _e('Ref nr:', 'job-listings'); ?></b><br/>
                                        {{$postMeta['ad_reference_nbr'][0]}}

                                    </li>
                                @endif

                                @if (isset($postMeta['publish_start_date'][0]) && !empty($postMeta['publish_start_date'][0]))
                                    <li>
                                        <b><?php _e('Published:', 'job-listings'); ?></b><br/>
                                        {{substr($postMeta['publish_start_date'][0], 0,
                                                  strpos($postMeta['publish_start_date'][0], "T"))}}

                                    </li>
                                @endif

                                @if (isset($postMeta['number_of_positions'][0]) && !empty($postMeta['number_of_positions'][0]))
                                    <li>
                                        <b><?php _e('Number of applicants:', 'job-listings'); ?></b><br/>
                                        {{$postMeta['number_of_positions'][0]}}

                                    </li>
                                @endif

                                @if (isset($postMeta['work_experience'][0]) && !empty($postMeta['work_experience'][0]))
                                    <li>
                                        <b><?php _e('Experience:', 'job-listings'); ?></b><br/>
                                        {{$postMeta['work_experience'][0]}}

                                    </li>
                                @endif

                                @if (isset($postMeta['employment_type'][0]) && !empty($postMeta['employment_type'][0]))
                                    <li>
                                        <b><?php _e('Employee:', 'job-listings'); ?></b><br/>
                                        {{$postMeta['employment_type'][0]}}

                                    </li>
                                @endif

                                @if (isset($postMeta['employment_grade'][0]) && !empty($postMeta['employment_grade'][0]))
                                    <li>
                                        <b><?php _e('Extent:', 'job-listings'); ?></b><br/>
                                        {{$postMeta['employment_grade'][0]}}

                                    </li>
                                @endif

                                @if (isset($postMeta['employment_grade'][0]) && !empty($postMeta['employment_grade'][0]))
                                    <li><b><?php _e('Municipality:', 'job-listings'); ?></b><br/>
                                        {{$postMeta['location_name'][0]}}</li>
                                    
                                @endif

                                @if(isset($postMeta['departments'][0]) && !empty($postMeta['departments'][0]))
                                    <li>
                                        <b><?php _e('Company:', 'job-listings'); ?></b><br/>
                                        {{ucfirst(mb_strtolower($postMeta['departments'][0]))}}
                                    </li>
                                @endif

                            </ul>
                        </div>

                    </div>
                    @if(isset($postMeta['application_end_date'][0]) && !empty($postMeta['application_end_date'][0]))

                        @if ($postMeta['application_end_date'][0] < date('Y-m-d'))
                            <button class="btn btn-lg btn-contrasted disabled btn-block">
                                <?php _e('The application period has ended', 'job-listings'); ?>
                            </button>
                        @else

                            <a target="_blank"
                               class="btn btn-lg btn-block btn-primary btn-outline"
                               href="{{get_field('apply_button_url','option')}}{{$postMeta['guid'][0]}}"><?php _e('Apply here',
                                    'job-listings'); ?> ({{$diff->format("%r%a")}} <?php _e('days left',
                                    'job-listings'); ?>)
                            </a>
                        @endif

                    @endif

                </div>
            </aside>
        </div>
    </div>

@stop

