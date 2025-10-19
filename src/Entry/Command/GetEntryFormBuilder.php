<?php namespace Anomaly\StreamsModule\Entry\Command;

use Anomaly\Streams\Platform\Stream\Contract\StreamInterface;
use Anomaly\Streams\Platform\Ui\Form\FormBuilder;
use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\Bus\DispatchesJobs;

/**
 * Class GetEntryFormBuilder
 *
 * @link   http://pyrocms.com/
 * @author PyroCMS, Inc. <support@pyrocms.com>
 * @author Ryan Thompson <ryan@pyrocms.com>
 */
class GetEntryFormBuilder
{

    use DispatchesJobs;

    /**
     * The stream instance.
     *
     * @var StreamInterface
     */
    protected $stream;

    /**
     * Create a new GetEntryFormBuilder instance.
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
     * @param Container $container
     * @return FormBuilder
     */
    public function handle(Container $container)
    {
        $stream    = $this->stream->getSlug();
        $namespace = $this->stream->getNamespace();

        /**
         * Try resolving the form builder.
         *
         * @var FormBuilder $builder
         */
        try {
            $builder = $container
                ->make("anomaly.module.streams::{$namespace}.{$stream}.form")
                ->setModel($this->stream->getEntryModel());
        } catch (\Exception $e) {
            if (!$builder = dispatch_sync(new GetConfiguredFormBuilder($this->stream))) {
                $builder = dispatch_sync(new GetDefaultFormBuilder($this->stream));
            }
        }

        /**
         * Add our default heading.
         */
        if (!$builder->getOption('heading')) {
            $builder->setOption('heading', 'module::admin/groups/heading');
        }

        return $builder;
    }
}
