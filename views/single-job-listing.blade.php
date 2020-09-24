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

                <div class="grid--columns">

                    @card([
                        'heading' => __('Information', 'job-listings')
                    ])
                        @listing([
                            'list' => $preparedListData['employeList'],
                            'elementType' => "ul",
                            'classList' => ['unlist', 'job-listing-sidenav', 'job-listing-employees']
                        ])
                        @endlisting
                    @endcard

                    <div class="gutter gutter-top">
                    @if($contacts)
                            @foreach($preparedListData['contacts'] as $contact)
                                @card([
                                    'heading' => __('Contact', 'job-listings')
                                ])

                                    @if( isset($contact['contactPerson']) && !empty($contact['contactPerson']) )

                                        @typography([
                                            'element' => "h4"
                                        ])
                                            {{ $contact['contactPerson'] }}
                                        @endtypography

                                    @endif

                                    @if( isset($contact['contactPosition']) && !empty($contact['contactPosition']) )

                                        @typography([
                                            'variant' => "meta",
                                            'element' => "span"
                                        ])
                                            {{ $contact['contactPosition'] }}
                                        @endtypography

                                    @endif

                                    @if( isset($contact['contactPhone']) && !empty($contact['contactPhone']) )

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

                                    @endif

                                @endcard
                            @endforeach

                    @endif
                    </div>
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
                                        'classList' => ['u-display--block'],
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
                                        'classList' => ['u-display--block']
                                    ])
                                        {{_e('Apply here','job-listings')}} ({{ $daysLeft }} {{_e('days left','job-listings')}})
                                    @endbutton
                                @endif

                            @if($sourceSystem == "reachmee")

                                <br /><br />
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
                                    'classList' => ['u-display--block']
                                ])
                                @endbutton

                            @endif
                        </div>
                    @endif
                
                    
                </div>

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

