# Streams Module

*anomaly.module.streams*

#### Build custom streams in the control panel.

The Streams Module provides a control panel interface for building and managing custom streams without writing code.

## Features

- Visual stream builder
- Field management interface
- Assignment configuration
- Control panel integration
- Stream permissions
- Entry management
- Export/import functionality

## Usage

### Creating Streams

Navigate to **Streams > Streams** in the control panel to create new streams with custom fields and assignments.

### Accessing Stream Entries

```php
use Anomaly\Streams\Platform\Entry\EntryModel;

// Get stream model
$model = app('streams')->entries('your_stream_slug');

// Query entries
$entries = $model->all();
$entry = $model->find(1);
```

### In Twig

```twig
{# Get entries from a stream #}
{% set entries = entries('your_stream_slug').get() %}

{% for entry in entries %}
    <h3>{{ entry.title }}</h3>
    <p>{{ entry.description }}</p>
{% endfor %}
```

### Building Forms

```twig
{# Create entry form #}
{{ form('your_stream_slug', 'create')|raw }}

{# Edit entry form #}
{{ form('your_stream_slug', 'edit', entry)|raw }}
```

## Requirements

- Streams Platform ^1.10
- PyroCMS 3.10+
- Icon Field Type ^1.2+
- Users Module ^2.6+

## License

The Streams Module is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
