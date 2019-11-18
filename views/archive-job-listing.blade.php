@extends('templates.master')

@section('content')

    @if (get_field('archive_' . sanitize_title($postType) . '_filter_position', 'option') == 'top')
        @include('partials.archive-filters')
    @endif

    <div class="container main-container job-listings">
        @include('partials.breadcrumbs')

        <div class="grid">
            @if (get_field('archive_' . sanitize_title($postType) . '_show_sidebar_navigation', 'option'))
                @include('partials.sidebar-left')
            @endif

            <?php
            $cols = 'grid-md-12';
            if (is_active_sidebar('right-sidebar') && get_field('archive_' . sanitize_title($postType) . '_show_sidebar_navigation',
                    'option')) {
                $cols = 'grid-md-8 grid-lg-6';
            } elseif (is_active_sidebar('right-sidebar') || get_field('archive_' . sanitize_title($postType) . '_show_sidebar_navigation',
                    'option')) {
                $cols = 'grid-md-8 grid-lg-9';
            }
            ?>
            <div class="{{ $cols }}">

                @if (get_field('archive_' . sanitize_title($postType) . '_title', 'option') || is_category() || is_date())
                    <div class="grid">
                        <div class="grid-md-12">
                            @if (get_field('archive_' . sanitize_title($postType) . '_title', 'option'))
                                @if (is_category())
                                    <h3>{{ get_field('archive_' . sanitize_title($postType) . '_title', 'option') }}
                                        : {{ single_cat_title() }}</h3>
                                    {!! category_description() !!}
                                @elseif (is_date())
                                    <h3>{{ get_field('archive_' . sanitize_title($postType) . '_title', 'option') }}
                                        : {{ the_archive_title() }}</h3>
                                @else
                                    <h3>{{ get_field('archive_' . sanitize_title($postType) . '_title', 'option') }}</h3>
                                @endif
                            @else
                                @if (is_category())
                                    <h3>{{ single_cat_title() }}</h3>
                                    {!! category_description() !!}
                                @elseif (is_date())
                                    <h3>{{ the_archive_title() }}</h3>
                                @endif
                            @endif

                            @if (!empty(apply_filters('accessibility_items', array())))
                                <div class="u-mb-3">
                                    @include('partials.accessibility-menu')
                                </div>
                            @endif
                        </div>
                    </div>
                @elseif (!empty(apply_filters('accessibility_items', array())))
                    <div class="grid">
                        <div class="grid-xs-12 u-mb-3">
                            @include('partials.accessibility-menu')
                        </div>
                    </div>
                @endif

                @if (is_active_sidebar('content-area-top'))
                    <div class="grid grid--columns sidebar-content-area sidebar-content-area-top">
                        <?php dynamic_sidebar('content-area-top'); ?>
                    </div>
                @endif

                @if (in_array($template, array('list')))
                    @include('partials.blog.type.post-' . $template)
                @else
                    @if (get_field('archive_' . sanitize_title($postType) . '_filter_position', 'option') == 'content')
                        <div class="grid filter-content">
                            @include('partials.archive-filters')
                        </div>
                    @endif

                        @if (have_posts())
                            <table class="table table-striped table-hover">
                                <tr>
                                    <th><?php _e('Position', 'job-listings'); ?></th>
                                    <th class="hidden-sm hidden-xs"><?php _e('Published', 'job-listings'); ?></th>
                                    <th class="hidden-sm hidden-xs"><?php _e('Apply by', 'job-listings'); ?></th>
                                    <th class="hidden-sm hidden-xs"><?php _e('Category', 'job-listings'); ?></th>
                                </tr>

                                @while(have_posts())
                                    {!! the_post() !!}
                                    <?php
                                    $postMeta = get_post_meta(get_the_ID());
                                    ?>
                                    <tr>
                                        @if (in_array($template, array('full', 'compressed', 'collapsed', 'horizontal-cards')))
                                            <td>
                                                <a href="{{ the_permalink() }}" class="box box-news box-news-horizontal">
                                                    {{ the_title() }}
                                                    <br/>
                                                    <span class="hidden-lg hidden-md text-sm display-block">
                                                    <?php _e('Published on', 'job-listings'); ?> <strong>{{ $postMeta['publish_start_date'][0] }}</strong> <?php _e('to', 'job-listings'); ?> <strong>{{ $postMeta['application_end_date'][0] }}</strong> <?php _e('in', 'job-listings'); ?> <strong>{{ $postMeta['occupationclassifications'][0] }}</strong>.
                                                    </span>
                                                </a>
                                            <td class="hidden-sm hidden-xs">
                                                @if(isset($postMeta['publish_start_date'][0]) && !empty($postMeta['publish_start_date'][0]))
                                                    {{ $postMeta['publish_start_date'][0] }}
                                                @endif
                                            </td>
                                            </td>
                                            <td class="hidden-sm hidden-xs">
                                                @if(isset($postMeta['application_end_date'][0]) && !empty($postMeta['application_end_date'][0]))
                                                    @if(isset($postMeta['has_expired'][0]))
                                                        
                                                        @if ($postMeta['has_expired'][0] === '1')
                                                            <?php _e('Expired', 'job-listings'); ?>
                                                        @endif

                                                        @if ($postMeta['has_expired'][0] === '0')
                                                            {{ $postMeta['application_end_date'][0] }}
                                                        @endif

                                                    @endif
                                                @endif
                                            </td>
                                            <td class="hidden-sm hidden-xs">
                                                @if (isset($postMeta['occupationclassifications'][0]))
                                                    {{ $postMeta['occupationclassifications'][0] }}
                                                @endif
                                            </td>
                                        @endif
                                    </tr>
                                @endwhile
                            </table>
                        @else
                            <div class="grid-xs-12">
                                <div class="notice info pricon pricon-info-o pricon-space-right"><?php _e('No posts to show',
                                        'municipio'); ?>…
                                </div>
                            </div>
                        @endif
          

                @endif

                @if (is_active_sidebar('content-area'))
                    <div class="grid grid--columns sidebar-content-area sidebar-content-area-bottom">
                        <?php dynamic_sidebar('content-area'); ?>
                    </div>
                @endif

                <div class="grid">
                    <div class="grid-sm-12 text-center u-mb-7">
                        {!!
                            paginate_links(array(
                                'type' => 'list'
                            ))
                        !!}
                    </div>
                </div>
            </div>

            @include('partials.sidebar-right')
        </div>
    </div>

@stop
