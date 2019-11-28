<?php

use Anomaly\Streams\Platform\Database\Migration\Migration;

/**
 * Class CreateConfigurationsFields
 *
 * @link   http://pyrocms.com/
 * @author PyroCMS, Inc. <support@pyrocms.com>
 * @author Ryan Thompson <ryan@pyrocms.com>
 */
class CreateConfigurationsFields extends Migration
{

    /**
     * The field definitions.
     *
     * @var array
     */
    protected $fields = [
        'related'        => [
            'type'   => 'anomaly.field_type.relationship',
            'config' => [
                'related' => 'Anomaly\Streams\Platform\Stream\StreamModel',
                'mode'    => 'lookup',
            ],
        ],
        'index_route'    => 'anomaly.field_type.text',
        'index_template' => [
            'type'   => 'anomaly.field_type.editor',
            'config' => [
                'default_value' => '{% extends "theme::layouts/default" %}

{% block content %}

	{% for entry in entries(stream.slug, stream.namespace).get() %}
		<a href="{{ entry.route(\'view\') }}">{{ entry.title }}</a><br>
	{% endfor %}

{% endblock %}',
                'mode'          => 'twig',
            ],
        ],
        'view_route'     => 'anomaly.field_type.text',
        'view_template'  => [
            'type'   => 'anomaly.field_type.editor',
            'config' => [
                'default_value' => '{% extends "theme::layouts/default" %}

{% block content %}

    {% set entry = entries(stream.slug, stream.namespace).find(request_segments()|last) %}

	<h1>{{ entry.title }}</h1>

	<a href="{{ entry.route(\'index\') }}">View All</a>

{% endblock %}',
                'mode'          => 'twig',
            ],
        ],
    ];
}
