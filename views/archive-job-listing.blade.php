@extends('templates.master')

@section('content')

    @if (get_field('archive_' . sanitize_title($postType) . '_filter_position', 'option') == 'top')
        @include('partials.archive-filters')
    @endif

    <div class="container main-container">
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
                        <div class="grid-xs-12">
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
                    <div class="grid grid--columns">
                        @if (have_posts())
                            <?php $postNum = 0;?>
                            <table class="table table-striped table-hover">
                                <tr>
                                    <th>{{__('Position', 'municipio')}}</th>
                                    <th>{{__('Published', 'municipio')}}</th>
                                    <th>{{__('Apply by', 'municipio')}}</th>
                                </tr>

                                @while(have_posts())
                                    <tr>
                                        {!! the_post() !!}
                                        <?php $postMeta = get_post_meta(get_the_ID()); ?>
                                        @if (in_array($template, array('full', 'compressed', 'collapsed', 'horizontal-cards')))
                                            <td>
                                                <a href="{{ the_permalink() }}"
                                                   class="box box-news box-news-horizontal">
                                                    <h3 class="text-highlight">{{ the_title() }}</h3>
                                                </a>
                                            <td><?php if ($postMeta['publish_start_date']) {
                                                    echo substr($postMeta['publish_start_date'][0], 0,
                                                        strpos($postMeta['publish_start_date'][0], "T"));
                                                } ?></td>
                                            </td>
                                            <td>
                                                <?php if ($postMeta['application_end_date']) {
                                                    echo substr($postMeta['application_end_date'][0], 0,
                                                        strpos($postMeta['application_end_date'][0], "T"));
                                                } ?>
                                            </td>

                                    </tr>

                                    @else
                                        @include('partials.blog.type.post-' . $template)
                                    @endif

                                    <?php $postNum++; ?>
                                @endwhile
                            </table>
                        @else
                            <div class="grid-xs-12">
                                <div class="notice info pricon pricon-info-o pricon-space-right"><?php _e('No posts to show',
                                        'municipio'); ?>…
                                </div>
                            </div>
                        @endif
                    </div>

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