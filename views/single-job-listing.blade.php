@extends('templates.single')

@section('above')
    <div class="nav-helper">
        @includeIf('partials.navigation.breadcrumb')
        @includeIf('partials.navigation.accessibility')
    </div>

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
@stop

@section('sidebar-left')

@stop

@section('content')
    <article class="c-article" id="article">

        @typography(["element" => "h1"])
            {{the_title() }}
        @endtypography

                @if(!$isExpired)
                    @if($applyLink === '#job-listings-modal')

                        @button([
                            'color' => 'primary',
                            'style' => 'filled',
                            'id' => 'job-listings-apply',
                            'classList' => ['js-job-listings-apply'],
                            'attributeList' => [
                                'data-open' => 'job-listings-modal',
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
                        'content' =>  $legal,
                        'classList' => [
                            'c-card--panel'
                        ]
                ])
                @endcard

            @endif
        @endif
    </article>
@stop

@section('sidebar-right')
    <!-- -Information -->
    <div class="o-grid">

        @typography([
            'element' => "h2",
            'variant' => "h2"
        ])
            {{__('Information', 'job-listings')}}
        @endtypography

        <div class="o-grid-12">
            @card([
                'classList' => [
                    'c-card--panel'
                ]
            ])
                @collection([
                    'list' => $preparedListData['employeList']
                ])
                @endcollection

            @endcard
        </div>
        <!-- -Contact -->
        @if($contacts)

                @typography([
                    'element' => "h2",
                    'variant' => "h2"
                ])
                    {{__('Contact', 'job-listings')}}
                @endtypography

                @foreach($preparedListData['contacts'] as $contact)
                    <div class="o-grid-12">
                        {{--//@TODO: Change to contact card--}}

                        @card([
                            'classList' => [
                                'c-card--panel'
                            ]
                        ])

                            @collection([])

                                @if( isset($contact['contactPerson']) && !empty($contact['contactPerson']) )

                                    @collection__item()
                                        @typography([
                                            'element' => "h4"
                                        ])
                                            {{ $contact['contactPerson'] }}
                                        @endtypography

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

                                    @endcollection__item
                                @endif

                            @endcollection
                        @endcard
                    </div>
                @endforeach
        @endif

        @if($isExpired)
            <div class="o-grid-12">

                @button([
                    'style' => 'filled',
                    'attributeList' => ['disabled' => 'true']

                ])
                    {{_e('The application period has ended', 'job-listings')}}
                @endbutton

            </div>
        @else

                @if($applyLink === '#job-listings-modal')
                    <div class="o-grid-12">
                        @button([
                            'color' => 'primary',
                            'style' => 'filled',
                            'id' => 'job-listings-apply',
                            'classList' => ['c-button--margin-top', 'u-margin__right--1',  'js-job-listings-apply'],
                            'attributeList' => [
                                'data-open' => 'job-listings-modal',
                            ]
                        ])
                            {{_e('Apply here','job-listings')}} ({{ $daysLeft }} {{_e('days left','job-listings')}})
                        @endbutton
                    </div>

                @else
                    <div class="o-grid-12">
                        @button([
                            'color' => 'primary',
                            'style' => 'filled',
                            'href' => $applyLink,
                            'classList' => ['c-button--margin-top', 'u-margin__right--1']
                        ])
                            {{_e('Apply here','job-listings')}} ({{ $daysLeft }} {{_e('days left','job-listings')}})
                        @endbutton
                    </div>
                @endif


                @if($sourceSystem == "reachmee")
                    <div class="o-grid-12">
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
                    </div>
                @endif

        @endif

    </div>
@stop

@section('below')
    @includeIf('partials.sidebar', ['id' => 'content-area-bottom', 'classes' => ['o-grid']])

    @if($sourceSystem == "reachmee")

        @modal([
            'isPanel' => false,
            'id' => 'job-listings-modal',
            'overlay' => 'dark',
            'animation' => 'scale-up',
            'size' => 'lg'
        ])
        @endmodal

    @endif

@stop