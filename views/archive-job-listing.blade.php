@extends('templates.master')

@section('before-layout')
@stop

@section('above')
    <div class="nav-helper">
        @includeIf('partials.navigation.breadcrumb')
        @includeIf('partials.navigation.accessibility')
    </div>
@stop
@section('content')

    @if (get_field('archive_' . sanitize_title($postType) . '_filter_position', 'option') == 'top')
        @include('partials.archive.archive-filters')
    @endif

    <div class="container main-container job-listings">


        <div class="grid" class="jobb-listings">
            @if (get_field('archive_' . sanitize_title($postType) . '_show_sidebar_navigation', 'option'))
                @include('partials.sidebar')
            @endif

            <?php
            $cols = 'o-grid-12@md';
            if (is_active_sidebar('right-sidebar') && get_field('archive_' . sanitize_title($postType) . '_show_sidebar_navigation',
                    'option')) {
                $cols = 'o-grid-8@md o-grid-6@lg';
            } elseif (is_active_sidebar('right-sidebar') || get_field('archive_' . sanitize_title($postType) . '_show_sidebar_navigation',
                    'option')) {
                $cols = 'o-grid-8@md o-grid-9@lg';
            }
            ?>

            <div class="{{ $cols }}">

                @if (get_field('archive_' . sanitize_title($postType) . '_title', 'option') || is_category() || is_date())

                    <div class="o-grid">
                        <div class="o-grid-12@md">
                            @if (get_field('archive_' . sanitize_title($postType) . '_title', 'option'))

                                @if (is_category())

                                    @typography([
                                        'element' => "h3",
                                    ])
                                        {{ get_field('archive_' . sanitize_title($postType) . '_title', 'option') }} : {{ single_cat_title() }}
                                    @endtypography

                                    @typography([
                                        'element' => "p",
                                    ])
                                        {!! category_description() !!}
                                    @endtypography


                                @elseif (is_date())

                                    @typography([
                                        'element' => "h3",
                                    ])
                                        {{ get_field('archive_' . sanitize_title($postType) . '_title', 'option') }}: {{ the_archive_title() }}
                                    @endtypography

                                @else

                                    @typography([
                                        'element' => "h3",
                                    ])
                                        {{ get_field('archive_' . sanitize_title($postType) . '_title', 'option') }}
                                    @endtypography

                                @endif

                            @else
                                @if (is_category())

                                    @typography([
                                        'element' => "h3",
                                    ])
                                        {{ single_cat_title() }}
                                    @endtypography

                                    @typography([
                                        'element' => "p",
                                    ])
                                        {!! category_description() !!}
                                    @endtypography


                                @elseif (is_date())

                                    @typography([
                                        'element' => "h3",
                                    ])
                                        {{ the_archive_title() }}
                                    @endtypography
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
                    <div class="o-grid">
                        <div class="o-grid-12@xs u-mb-3">
                            @include('partials.accessibility-menu')
                        </div>
                    </div>
                @endif

                @if (is_active_sidebar('content-area-top'))
                    <div class="o-grid sidebar-content-area sidebar-content-area-top">
                        <?php dynamic_sidebar('content-area-top'); ?>
                    </div>
                @endif


                @if (get_field('archive_' . sanitize_title($postType) . '_filter_position', 'option') == 'content')
                    <div class="o-grid filter-content">
                        @include('partials.archive.archive-filters')
                    </div>
                @endif

                @if(isset($tableData) && !empty($tableData))
                    @table([
                        'headings'      => [
                            __('Position', 'job-listings'),
                            __('Published', 'job-listings'),
                            __('Apply by', 'job-listings'),
                            __('Category', 'job-listings')],
                        'showFooter'    => true,
                        'filterable'    => false,
                        'sortable'      => false,
                        'pagination'    => 10,
                        'list'          => $tableData
                    ])
                    @endtable
                @endif

                @if (is_active_sidebar('content-area'))
                    <div class="o-grid sidebar-content-area sidebar-content-area-bottom">
                        <?php dynamic_sidebar('content-area'); ?>
                    </div>
                @endif

            </div>

        </div>
    </div>

@stop
