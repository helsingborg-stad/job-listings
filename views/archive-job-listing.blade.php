@extends('templates.archive')

@section('sidebar-left')
@stop

@section('content')
    <div class="archive s-archive s-archive-template-{{sanitize_title($template)}}  s-{{sanitize_title($postType)}}-archive" @if (apply_filters('archive_equal_container', false, $postType, $template))  @endif>
        {!! $hook->loopStart !!}
            @include('partials.archive.archive-title')

            @if (get_field('archive_' . sanitize_title($postType) . '_filter_position', 'option') == 'content')
                @include("partials.archive.archive-filters")
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
            @else
                @notice([
                    'type' => 'info',
                    'message' => [
                        'text' => $lang->noResult,
                        'size' => 'md'
                    ]
                ])
                @endnotice
            @endif
        {!! $hook->loopEnd !!}
    </div>
@stop
