<?php namespace Anomaly\StreamsModule\Http\Controller\Admin;

use Anomaly\Streams\Platform\Entry\Contract\EntryInterface;
use Anomaly\Streams\Platform\Entry\EntryRepository;
use Anomaly\Streams\Platform\Http\Controller\AdminController;
use Anomaly\Streams\Platform\Stream\Contract\StreamInterface;
use Anomaly\Streams\Platform\Stream\Contract\StreamRepositoryInterface;
use Anomaly\Streams\Platform\Stream\StreamModel;
use Anomaly\StreamsModule\Entry\Command\GetEntryFormBuilder;
use Anomaly\StreamsModule\Entry\Command\GetEntryTableBuilder;
use Anomaly\StreamsModule\Http\Middleware\SetCheckNamespace;
use Illuminate\Session\Store;

/**
 * Class EntriesController
 *
 * @link          http://pyrocms.com/
 * @author        PyroCMS, Inc. <support@pyrocms.com>
 * @author        Ryan Thompson <ryan@pyrocms.com>
 */
class EntriesController extends AdminController
{

    /**
     * The working namespace.
     *
     * @var string
     */
    protected $namespace;

    /**
     * Create a new StreamsController instance.
     *
     * @param Store $session
     */
    public function __construct(Store $session)
    {
        $this->middleware(SetCheckNamespace::class);

        $this->namespace = $session->get('anomaly.module.streams::namespace');

        parent::__construct();
    }

    /**
     * Return an index of existing entries.
     *
     * @param  StreamRepositoryInterface $streams
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(StreamRepositoryInterface $streams)
    {
        /* @var StreamInterface $stream */
        $stream = $streams->find($this->route->parameter('stream'));

        $builder = dispatch_sync(new GetEntryTableBuilder($stream));

        return $builder->render();
    }

    /**
     * Return the modal for choosing a stream.
     *
     * @param  StreamRepositoryInterface $streams
     * @return \Illuminate\View\View
     */
    public function choose(StreamRepositoryInterface $streams)
    {
        $streams = $streams->findAllByNamespace($this->getNamespace());

        if ($this->getNamespace() == 'streams') {
            $streams = $streams->filter(
                function (StreamInterface $stream) {
                    return !in_array($stream->getSlug(), ['groups']);
                }
            );
        }

        return $this->view->make(
            'module::admin/entries/choose',
            [
                'streams' => $streams,
            ]
        );
    }

    /**
     * Create a new entry.
     *
     * @param  StreamRepositoryInterface $streams
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function create(StreamRepositoryInterface $streams)
    {
        /* @var StreamInterface $stream */
        $stream = $streams->find($this->route->parameter('stream'));

        $builder = dispatch_sync(new GetEntryFormBuilder($stream));

        return $builder->render();
    }

    /**
     * Edit an existing entry.
     *
     * @param  StreamRepositoryInterface $streams
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit(StreamRepositoryInterface $streams)
    {
        /* @var StreamInterface $stream */
        $stream = $streams->find($this->route->parameter('stream'));

        $builder = dispatch_sync(new GetEntryFormBuilder($stream));

        return $builder->render($this->route->parameter('id'));
    }

    /**
     * View an existing entry in the stream.
     *
     * @param StreamRepositoryInterface $streams
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function view(StreamRepositoryInterface $streams, $stream, $id)
    {
        /* @var StreamModel $stream */
        $stream = $streams->find($stream);

        $entries = (new EntryRepository())->setModel($stream->getEntryModel());

        /* @var EntryInterface $entry */
        $entry = $entries->find($id);

        return $this->redirect->to($entry->route('view'));
    }

    /**
     * Get the namespace.
     *
     * @return string
     */
    protected function getNamespace()
    {
        return $this->namespace;
    }
}
