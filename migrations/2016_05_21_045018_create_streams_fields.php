<?php

use Anomaly\Streams\Platform\Database\Migration\Migration;

/**
 * Class CreateStreamsFields
 *
 * @link   http://pyrocms.com/
 * @author PyroCMS, Inc. <support@pyrocms.com>
 * @author Ryan Thompson <ryan@pyrocms.com>
 */
class CreateStreamsFields extends Migration
{

    /**
     * The field namespace.
     *
     * @var string
     */
    protected $namespace = 'streams_utilities';

    /**
     * The addon fields.
     *
     * @var array
     */
    protected $fields = [
        'name'        => 'anomaly.field_type.text',
        'description' => 'anomaly.field_type.textarea',
        'slug'        => [
            'type'   => 'anomaly.field_type.slug',
            'config' => [
                'slugify' => 'name',
            ],
        ],
    ];
}
