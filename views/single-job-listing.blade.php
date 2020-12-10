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

                        @if($isExpired)
                            <div class="gutter gutter-bottom">
                                <div class="notice warning">
                                    <i class="pricon pricon-notice-warning"></i> <?php _e("The application period for this reqruitment has ended."); ?>
                                </div>
                            </div>
                        @endif

                        <div class="grid">
                            <div class="grid-xs-12">
                                <div class="post post-single">

                                    <article class="u-mb-5" id="article">

                                        <div class="box box-card">
                                            <div class="box-content">
                                                @if(!$isExpired)
                                                    <a class="btn btn-lg btn-primary btn-floating-application job-listings-application"
                                                        href="{{ $applyLink }}">
                                                        <?php _e('Apply now', 'job-listings'); ?>
                                                    </a>
                                                @endif

                                                @include('partials.blog.post-header')

                                                @if (post_password_required(get_post()))
                                                    {!! get_the_password_form() !!}
                                                @else

                                                @isset($preamble)
                                                    <p class="lead">
                                                        {{ $preamble }}
                                                    </p>
                                                @endisset

                                                {!! $content !!}

                                            </div>
                                        </div>

                                            @isset($legal)
                                                <div class="box box-card">
                                                    <div class="box-content">
                                                        <div class="small">
                                                            {{ $legal }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @endisset
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
                                
                                @if($endDate && !$isExpired)
                                    <li>
                                        <b><?php _e('Deadline for applications:', 'job-listings'); ?></b>
                                        <br/>
                                        {{ $endDate }}
                                        <span class="text-sm">
                                            ({{ $daysLeft }} <?php _e('days left','job-listings'); ?>)
                                        </span>
                                    </li>
                                @endif

                                @if($projectNr)
                                    <li class="gutter gutter-top">
                                        <b><?php _e('Reference:', 'job-listings'); ?></b>
                                        <br/>
                                        {{ $projectNr }}                                     </li>
                                @endif

                                @if($startDate)
                                    <li class="gutter gutter-top">
                                        <b><?php _e('Published:', 'job-listings'); ?></b>
                                        <br/>
                                        {{ $startDate }}
                                    </li>
                                @endif

                                @if($numberOfPositions)
                                    <li class="gutter gutter-top">
                                        <b><?php _e('Number of positions:', 'job-listings'); ?></b>
                                        <br/>
                                        {{ $numberOfPositions }}
                                    </li>
                                @endif

                                @if($expreience)
                                    <li class="gutter gutter-top">
                                        <b><?php _e('Experience:', 'job-listings'); ?></b>
                                        <br/>
                                        {{ $expreience }}
                                    </li>
                                @endif

                                @if($employmentType)
                                    <li class="gutter gutter-top">
                                        <b><?php _e('Employment type:', 'job-listings'); ?></b>
                                        <br/>
                                        {{ $employmentType }}
                                    </li>
                                @endif

                                @if($employmentGrade)
                                    <li class="gutter gutter-top">
                                        <b><?php _e('Extent:', 'job-listings'); ?></b>
                                        <br/>
                                        {{ $employmentGrade }}
                                    </li>
                                @endif

                                @if($location)
                                    <li class="gutter gutter-top">
                                        <b><?php _e('Location:', 'job-listings'); ?></b>
                                        <br/>
                                        {{ $location }}
                                    </li>
                                @endif

                                @if($department)
                                    <li class="gutter gutter-top">
                                        <b><?php _e('Company:', 'job-listings'); ?></b>
                                        <br/>
                                        {{ $department }}
                                    </li>
                                @endif

                            </ul>
                        </div>

                    </div>

                    @if($contacts)
                        @foreach($contacts as $contact)
                            <div class="box box-card">
                                <div class="box-content">

                                    <h3><?php _e('Contact', 'job-listings'); ?></h3>
                                    <ul class="unlist job-listing-sidenav">

                                        @if ($contact->name)
                                            <li class="strong">{{$contact->name}}</li>
                                        @endif

                                        @if ($contact->position)
                                            <li class="small gutter gutter-bottom">{{$contact->position}}</li>
                                        @endif

                                        @if ($contact->phone && $contact->phone_sanitized)
                                            <li class="link-item link-item-phone">
                                                <a href="tel:{{ $contact->phone_sanitized }}">
                                                    {{ $contact->phone }}
                                                </a>
                                            </li>
                                        @endif

                                        @if ($contact->email)
                                            <li class="link-item link-item-email">
                                                <a href="mail:{{ $contact->email }}">
                                                    {{ $contact->email }}
                                                </a>
                                            </li>
                                        @endif

                                    </ul>
                                </div>
                            </div>
                        @endforeach
                    @endif

                    @if($isExpired)
                        <div class="gutter gutter-top">
                            <button class="btn btn-lg btn-contrasted disabled btn-block">
                                <?php _e('The application period has ended', 'job-listings'); ?>
                            </button>
                        </div>
                    @else
                        <div class="gutter gutter-top">
                            <a class="btn btn-lg btn-block btn-primary btn-outline job-listings-application"
                            href="{{ $applyLink }}"><?php _e('Apply here',
                                    'job-listings'); ?> ({{ $daysLeft }} <?php _e('days left',
                                    'job-listings'); ?>)
                            </a>
                            <?php if($sourceSystem == "reachmee") { ?> 
                                <a id="job-listings-login" class="btn btn-lg btn-block btn-primary btn-outline"
                                href="#job-listings-modal"><?php _e('Log in'); ?>
                                </a>
                            <?php } ?>
                        </div>
                    @endif
                
                    
                </div>

            </aside>
        </div>
    </div>

    @if($sourceSystem == "reachmee") {
        <!-- Modal -->
        <div id="job-listings-modal" class="modal modal-backdrop-4 modal-small" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-content material-shadow-lg">
                <div id="job-listings-modal-body">
                </div>
                <div class="modal-footer">
                    <a href="#close" class="btn btn-default"><?php _e('Close', 'job-listings') ?></a>
                </div>
            </div>
            <a href="#close" class="backdrop"></a>
        </div>
        <!-- /modal -->
    @endif

@stop

