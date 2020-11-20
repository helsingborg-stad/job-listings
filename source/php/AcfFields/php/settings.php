<?php 

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
    'key' => 'group_5dd2a034a4f0c',
    'title' => __('Job Listing Sources', 'job-listings'),
    'fields' => array(
        0 => array(
            'key' => 'field_5dd2a0450405f',
            'label' => __('Job listing sources', 'job-listings'),
            'name' => 'job_listings_importers',
            'type' => 'flexible_content',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'layouts' => array(
                'layout_5dd2a0647e711' => array(
                    'key' => 'layout_5dd2a0647e711',
                    'name' => 'visma',
                    'label' => __('Visma', 'job-listings'),
                    'display' => 'block',
                    'sub_fields' => array(
                        0 => array(
                            'key' => 'field_5dd2a0d3149e2',
                            'label' => 'Url',
                            'name' => 'baseUrl',
                            'type' => 'url',
                            'instructions' => 'The basic url for the feed, without querystrings.',
                            'required' => 1,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '',
                                'class' => '',
                                'id' => '',
                            ),
                            'default_value' => 'https://recruit.visma.com/External/Feeds/AssignmentList.ashx',
                            'placeholder' => 'https://recruit.visma.com/External/Feeds/AssignmentList.ashx',
                        ),
                        1 => array(
                            'key' => 'field_5dd2a3b97c71c',
                            'label' => 'guidGroup',
                            'name' => 'guidGroup',
                            'type' => 'text',
                            'instructions' => '',
                            'required' => 1,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '',
                                'class' => '',
                                'id' => '',
                            ),
                            'default_value' => '67794f6d-af82-43a1-b5dc-bb414fd3eab1',
                            'placeholder' => '',
                            'prepend' => '?guidGroup=',
                            'append' => '',
                            'maxlength' => '',
                        ),
                        2 => array(
                            'key' => 'field_5dd3bf4985106',
                            'label' => 'Apply base link',
                            'name' => 'apply_base_link',
                            'type' => 'url',
                            'instructions' => 'The base url for external apply links',
                            'required' => 1,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '',
                                'class' => '',
                                'id' => '',
                            ),
                            'default_value' => '',
                            'placeholder' => '',
                        ),
                    ),
                    'min' => '',
                    'max' => '',
                ),
                'layout_5dd2a08204060' => array(
                    'key' => 'layout_5dd2a08204060',
                    'name' => 'reachmee',
                    'label' => __('ReachMee', 'job-listings'),
                    'display' => 'block',
                    'sub_fields' => array(
                        0 => array(
                            'key' => 'field_5dd2a119149e3',
                            'label' => 'Url',
                            'name' => 'baseUrl',
                            'type' => 'url',
                            'instructions' => 'The basic url for the feed, without querystrings.',
                            'required' => 1,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '',
                                'class' => '',
                                'id' => '',
                            ),
                            'default_value' => 'https://site106.reachmee.com/Public/rssfeed/external.ashx',
                            'placeholder' => 'https://site106.reachmee.com/Public/rssfeed/external.ashx',
                        ),
                        1 => array(
                            'key' => 'field_5dd2a256bc3ca',
                            'label' => 'Id',
                            'name' => 'id',
                            'type' => 'number',
                            'instructions' => '',
                            'required' => 1,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '25',
                                'class' => '',
                                'id' => '',
                            ),
                            'default_value' => 9,
                            'placeholder' => 9,
                            'prepend' => '?id=',
                            'append' => '',
                            'min' => '',
                            'max' => '',
                            'step' => '',
                        ),
                        2 => array(
                            'key' => 'field_5dd2a28fbc3cb',
                            'label' => 'InstallationID',
                            'name' => 'InstallationID',
                            'type' => 'text',
                            'instructions' => '',
                            'required' => 1,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '25',
                                'class' => '',
                                'id' => '',
                            ),
                            'default_value' => 'I017',
                            'placeholder' => '',
                            'prepend' => '&InstallationID=',
                            'append' => '',
                            'maxlength' => '',
                        ),
                        3 => array(
                            'key' => 'field_5dd2a2b8bc3cc',
                            'label' => 'CustomerName',
                            'name' => 'CustomerName',
                            'type' => 'text',
                            'instructions' => '',
                            'required' => 1,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '25',
                                'class' => '',
                                'id' => '',
                            ),
                            'default_value' => 'helsingborg',
                            'placeholder' => '',
                            'prepend' => '&CustomerName=',
                            'append' => '',
                            'maxlength' => '',
                        ),
                        4 => array(
                            'key' => 'field_5dd2a2ddbc3cd',
                            'label' => 'lang',
                            'name' => 'lang',
                            'type' => 'text',
                            'instructions' => '',
                            'required' => 1,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '25',
                                'class' => '',
                                'id' => '',
                            ),
                            'default_value' => 'SE',
                            'placeholder' => '',
                            'prepend' => '&lang=',
                            'append' => '',
                            'maxlength' => '',
                        ),
                        5 => array(
                            'key' => 'field_5fb7af2c741b7',
                            'label' => 'CustomerID',
                            'name' => 'customerID',
                            'type' => 'text',
                            'instructions' => 'Customer id for application iframe',
                            'required' => 1,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '50',
                                'class' => '',
                                'id' => '',
                            ),
                            'default_value' => 1118,
                            'placeholder' => '',
                            'prepend' => '',
                            'append' => '',
                            'maxlength' => '',
                        ),
                        6 => array(
                            'key' => 'field_5fb7af68741b8',
                            'label' => 'Application Iframe URL',
                            'name' => 'applicationIframeUrl',
                            'type' => 'url',
                            'instructions' => 'Url to use for application iframe',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '50',
                                'class' => '',
                                'id' => '',
                            ),
                            'default_value' => 'https://web103.reachmee.com/ext/',
                            'placeholder' => '',
                        ),
                    ),
                    'min' => '',
                    'max' => '',
                ),
            ),
            'button_label' => __('Add Source', 'job-listings'),
            'min' => '',
            'max' => '',
        ),
    ),
    'location' => array(
        0 => array(
            0 => array(
                'param' => 'options_page',
                'operator' => '==',
                'value' => 'acf-options-import-settings',
            ),
        ),
    ),
    'menu_order' => 0,
    'position' => 'normal',
    'style' => 'default',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'hide_on_screen' => '',
    'active' => true,
    'description' => '',
));
}