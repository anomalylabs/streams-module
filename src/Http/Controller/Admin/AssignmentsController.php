<?php namespace Anomaly\StreamsModule\Http\Controller\Admin;

use Anomaly\Streams\Platform\Assignment\Form\AssignmentFormBuilder;
use Anomaly\Streams\Platform\Assignment\Table\AssignmentTableBuilder;
use Anomaly\Streams\Platform\Field\Contract\FieldRepositoryInterface;
use Anomaly\Streams\Platform\Http\Controller\AdminController;
use Anomaly\Streams\Platform\Stream\Contract\StreamInterface;
use Anomaly\Streams\Platform\Stream\Contract\StreamRepositoryInterface;
use Anomaly\StreamsModule\Http\Middleware\AuthorizeNamespace;
use Anomaly\StreamsModule\Http\Middleware\SetCheckNamespace;
use Illuminate\Session\Store;

/**
 * Class AssignmentsController
 *
 * @link          http://pyrocms.com/
 * @author        PyroCMS, Inc. <support@pyrocms.com>
 * @author        Ryan Thompson <ryan@pyrocms.com>
 * @package       Anomaly\StreamsModule\Http\Controller\Admin
 */
class AssignmentsController extends AdminController
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
        $this->namespace = $session->get('anomaly.module.streams::namespace', 'streams');

        $this->middleware(SetCheckNamespace::class);
        $this->middleware(AuthorizeNamespace::class);

        parent::__construct();
    }

    /**
     * Return an index of existing assignments.
     *
     * @param AssignmentTableBuilder    $table
     * @param StreamRepositoryInterface $streams
     * @param                           $stream
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(AssignmentTableBuilder $table, StreamRepositoryInterface $streams, $stream)
    {
        return $table
            ->setStream($streams->find($stream))
            ->setOption('heading', 'module::admin/groups/heading')
            ->render();
    }

    /**
     * Return the modal for choosing a field to assign.
     *
     * @param FieldRepositoryInterface  $fields
     * @param StreamRepositoryInterface $streams
     * @param                           $stream
     * @return \Illuminate\Contracts\View\View|mixed
     */
    public function choose(FieldRepositoryInterface $fields, StreamRepositoryInterface $streams, $stream)
    {
        $fields = $fields
            ->findAllByNamespace($this->namespace)
            ->notAssignedTo($streams->find($stream))
            ->unlocked();

        return $this->view->make('module::admin/assignments/choose', compact('fields', 'stream'));
    }

    /**
     * Create a new assignment.
     *
     * @param AssignmentFormBuilder     $builder
     * @param StreamRepositoryInterface $streams
     * @param FieldRepositoryInterface  $fields
     * @param                           $stream
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function create(
        AssignmentFormBuilder $builder,
        StreamRepositoryInterface $streams,
        FieldRepositoryInterface $fields,
        $stream
    ) {
        $stream = $streams->find($stream);

        $builder
            ->setOption('redirect', 'admin/streams/assignments/' . $stream->getId())
            ->setField($fields->find($this->request->get('field')))
            ->setOption('heading', 'module::admin/groups/heading')
            ->setStream($stream);

        return $builder->render();
    }

    /**
     * Edit an existing assignment.
     *
     * @param AssignmentFormBuilder     $builder
     * @param StreamRepositoryInterface $streams
     * @param                           $stream
     * @param                           $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit(
        AssignmentFormBuilder $builder,
        StreamRepositoryInterface $streams,
        $stream,
        $id
    ) {
        /* @var StreamInterface $stream */
        $stream = $streams->find($stream);

        $builder
            ->setOption('redirect', 'admin/streams/assignments/' . $stream->getId())
            ->setOption('heading', 'module::admin/groups/heading')
            ->setStream($stream);

        return $builder->render($id);
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
