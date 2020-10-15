@extends('templates.master')

@section('content')

    <div class="container main-container job-listings">
        @include('partials.navigation.breadcrumb')

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
                                @notice([
                                    'type' => 'warning',
                                        'message' => [
                                        'text' => _("The application period for this reqruitment has ended."),
                                        'size' => 'md'
                                    ],
                                    'icon' => [
                                        'name' => 'report',
                                        'size' => 'md',
                                        'color' => 'white'
                                    ]
                                ])
                                @endnotice

                            </div>
                        @endif

                        <div class="grid">
                            <div class="grid-xs-12">
                                <div class="post post-single">

                                    <article class="u-mb-5" id="article">

                                                @if(!$isExpired)
                                                    @if($applyLink === '#job-listings-modal')

                                                        @button([
                                                            'color' => 'primary',
                                                            'style' => 'filled',
                                                            'id' => 'job-listings-apply',
                                                            'attributeList' => [
                                                                'data-open' => 'job-listings-modal',
                                                                'js-trigger-btn-id' => 'true'
                                                            ]
                                                        ])
                                                            {{__('Apply now', 'job-listings')}}
                                                        @endbutton

                                                    @else

                                                        @button([
                                                            'text' => __('Apply now', 'job-listings'),
                                                            'color' => 'primary',
                                                            'style' => 'filled',
                                                            'href' => $applyLink,
                                                            'size' => 'lg'
                                                        ])
                                                        @endbutton

                                                    @endif
                                                @endif

                                                @if (post_password_required(get_post()))
                                                    {!! get_the_password_form() !!}
                                                @else

                                                @if(isset($preamble) && !empty($preamble))

                                                    @typography([
                                                        'element' => "p",
                                                        'classList' => ['lead']
                                                    ])
                                                        {{ $preamble }}
                                                    @endtypography

                                                @endif

                                                {!! $content !!}

                                            @if(isset($legal) && !empty($legal))

                                                @card([
                                                    'heading' => '',
                                                    'subHeading' => ''
                                                ])
                                                    @typography([
                                                    'element' => "h5",
                                                    'classList' => ['legal']
                                                    ])
                                                        {{ $legal }}
                                                    @endtypography

                                                @endcard

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
                    </div>
                </div>
            </div>

            <aside class="grid-lg-3 grid-md-12 sidebar-left-sidebar">
                <!-- -Information -->
                @typography([
                    'element' => "h3"
                ])
                    {{__('Information', 'job-listings')}}
                @endtypography

                @card([
                    'classList' => [
                        'c-card--panel'
                    ]
                ])
                    <div class="c-card__body">
                        @collection([
                            'list' => $preparedListData['employeList']
                        ])
                        @endcollection
                    </div>

                @endcard

                <!-- -Contact -->
                @if($contacts)
                    @typography([
                        'element' => "h3"
                    ])
                        {{__('Contact', 'job-listings')}}
                    @endtypography

                    @foreach($preparedListData['contacts'] as $contact)
                        @card([
                            'classList' => [
                                'c-card--panel'
                            ]
                        ])
                            <div class="c-card__body">
                                @collection([])

                                    @if( isset($contact['contactPerson']) && !empty($contact['contactPerson']) )
                                         @collection__item()
                                            @typography([
                                                'element' => "h4"
                                            ])
                                                {{ $contact['contactPerson'] }}
                                            @endtypography
                                        @endcollection__item
                                    @endif

                                    @if( isset($contact['contactPosition']) && !empty($contact['contactPosition']) )
                                        @collection__item()
                                            @typography([
                                                'variant' => "meta",
                                                'element' => "span"
                                            ])
                                                {{ $contact['contactPosition'] }}
                                            @endtypography
                                        @endcollection__item
                                    @endif

                                    @if( isset($contact['contactPhone']) && !empty($contact['contactPhone']) )
                                        @collection__item()
                                            @typography([
                                                'element' => "p"
                                            ])
                                                @icon([
                                                    'icon' => 'phone',
                                                    'size' => 'sm',
                                                    'color' => 'primary'
                                                ])
                                                @endicon
                                                {!! $contact['contactPhone'] !!}
                                                    @endtypography
                                        @endcollection__item
                                    @endif

                                @endcollection
                            </div>
                        @endcard

                    @endforeach
                @endif

                @if($isExpired)
                    <div class="gutter gutter-top">

                        @button([
                            'style' => 'filled',
                            'attributeList' => ['disabled' => 'true']

                        ])
                            {{_e('The application period has ended', 'job-listings')}}
                        @endbutton

                    </div>
                @else
                    <div class="gutter gutter-top">

                            @if($applyLink === '#job-listings-modal')

                                @button([
                                    'color' => 'primary',
                                    'style' => 'filled',
                                    'id' => 'job-listings-apply',
                                    'classList' => ['c-button--margin-top'],
                                    'attributeList' => [
                                        'data-open' => 'job-listings-modal',
                                        'js-trigger-btn-id' => 'true'
                                    ]
                                ])
                                    {{_e('Apply here','job-listings')}} ({{ $daysLeft }} {{_e('days left','job-listings')}})
                                @endbutton

                            @else

                                @button([
                                    'color' => 'primary',
                                    'style' => 'filled',
                                    'href' => $applyLink,
                                    'classList' => ['c-button--margin-top']
                                ])
                                    {{_e('Apply here','job-listings')}} ({{ $daysLeft }} {{_e('days left','job-listings')}})
                                @endbutton

                            @endif

                        @if($sourceSystem == "reachmee")

                            @button([
                                'icon' => 'assignment_ind',
                                'reversePositions' => true,
                                'text' => __('Log in'),
                                'style' => 'filled',
                                'id' => 'job-listings-login',
                                'attributeList' => [
                                    'data-open' => 'job-listings-modal',
                                    'js-trigger-btn-id' => 'true'
                                ],
                                'classList' => ['c-button--margin-top']
                            ])
                            @endbutton

                        @endif
                    </div>
                @endif

            </aside>
        </div>
    </div>

    @if($sourceSystem == "reachmee")

        @modal([
            'isPanel' => false,
            'id' => 'job-listings-modal',
            'overlay' => 'dark',
            'animation' => 'scale-up',
        ])
        @endmodal

    @endif


@stop

