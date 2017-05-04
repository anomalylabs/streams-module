<?php namespace Anomaly\StreamsModule;

use Anomaly\Streams\Platform\Addon\Module\Module;

/**
 * Class StreamsModule
 *
 * @link          http://pyrocms.com/
 * @author        PyroCMS, Inc. <support@pyrocms.com>
 * @author        Ryan Thompson <ryan@pyrocms.com>
 * @package       Anomaly\StreamsModule
 */
class StreamsModule extends Module
{

    /**
     * The addon icon.
     *
     * @var string
     */
    protected $icon = 'database';

    /**
     * The module sections.
     *
     * @var array
     */
    protected $sections = [
        'streams'    => [
            'buttons'  => [
                'new_stream',
                'change_namespace' => [
                    'type'        => 'info',
                    'data-toggle' => 'modal',
                    'data-target' => '#modal',
                    'icon'        => 'fa fa-random',
                    'href'        => 'admin/streams/namespaces/change',
                ],
            ],
            'sections' => [
                'assignments' => [
                    'hidden'  => true,
                    'href'    => 'admin/streams/assignments/{request.route.parameters.stream}',
                    'buttons' => [
                        'assign_fields' => [
                            'data-toggle' => 'modal',
                            'data-target' => '#modal',
                            'href'        => 'admin/streams/assignments/{request.route.parameters.stream}/choose',
                        ],
                    ],
                ],
            ],
        ],
        'entries'    => [
            'slug'        => 'entries',
            'data-toggle' => 'modal',
            'data-target' => '#modal',
            'data-href'   => 'admin/streams/entries/{request.route.parameters.stream}',
            'href'        => 'admin/streams/entries/choose',
            'buttons'     => [
                'new_entry'        => [
                    'href' => 'admin/streams/entries/{request.route.parameters.stream}/create',
                ],
            ],
        ],
        'fields'     => [
            'buttons' => [
                'new_field'        => [
                    'data-toggle' => 'modal',
                    'data-target' => '#modal',
                    'href'        => 'admin/streams/fields/choose',
                ],
                'change_namespace' => [
                    'type'        => 'info',
                    'data-toggle' => 'modal',
                    'data-target' => '#modal',
                    'icon'        => 'fa fa-random',
                    'href'        => 'admin/streams/namespaces/change',
                ],
            ],
        ],
        'namespaces' => [
            'buttons' => [
                'new_namespace',
            ],
        ],
    ];
}
