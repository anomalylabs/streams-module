<?php namespace Anomaly\StreamsModule\Entry\Command;

use Anomaly\Streams\Platform\Stream\Contract\StreamInterface;
use Anomaly\Streams\Platform\Support\Hydrator;
use Anomaly\Streams\Platform\Ui\Form\FormBuilder;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Foundation\Bus\DispatchesJobs;

/**
 * Class GetConfiguredFormBuilder
 *
 * @link   http://pyrocms.com/
 * @author PyroCMS, Inc. <support@pyrocms.com>
 * @author Ryan Thompson <ryan@pyrocms.com>
 */
class GetConfiguredFormBuilder
{

    use DispatchesJobs;

    /**
     * The stream instance.
     *
     * @var StreamInterface
     */
    protected $stream;

    /**
     * Create a new GetConfiguredFormBuilder instance.
     *
     * @param StreamInterface $stream
     */
    public function __construct(StreamInterface $stream)
    {
        $this->stream = $stream;
    }

    /**
     * Handle the command.
     *
     * @param Hydrator $hydrator
     * @param Repository $config
     * @return FormBuilder
     */
    public function handle(Hydrator $hydrator, Repository $config)
    {
        $stream    = $this->stream->getSlug();
        $namespace = $this->stream->getNamespace();

        if (!$parameters = $config->get("anomaly.module.streams::{$namespace}.{$stream}.form")) {
            return null;
        }

        $builder = dispatch_sync(new GetDefaultFormBuilder($this->stream));

        $hydrator->hydrate($builder, $parameters);

        return $builder;
    }
}
