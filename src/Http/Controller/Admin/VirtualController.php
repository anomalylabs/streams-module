<?php namespace Anomaly\StreamsModule\Http\Controller\Admin;

use Anomaly\Streams\Platform\Http\Controller\AdminController;
use Anomaly\Streams\Platform\Stream\Contract\StreamInterface;
use Anomaly\Streams\Platform\Stream\Contract\StreamRepositoryInterface;
use Anomaly\StreamsModule\Entry\Command\AddDefaultFormPermissions;
use Anomaly\StreamsModule\Entry\Command\AddDefaultTablePermissions;
use Anomaly\StreamsModule\Entry\Command\GetEntryFormBuilder;
use Anomaly\StreamsModule\Entry\Command\GetEntryTableBuilder;
use Anomaly\StreamsModule\Group\Contract\GroupInterface;
use Anomaly\StreamsModule\Group\Contract\GroupRepositoryInterface;
use Anomaly\UsersModule\Http\Middleware\AuthorizeModuleAccess;

/**
 * Class VirtualController
 *
 * @link          http://pyrocms.com/
 * @author        PyroCMS, Inc. <support@pyrocms.com>
 * @author        Ryan Thompson <ryan@pyrocms.com>
 */
class VirtualController extends AdminController
{

    /**
     * Create a new VirtualController instance.
     */
    public function __construct()
    {
        parent::__construct();

        /**
         * Disable the module authorization since
         * these routes depend on the streams module
         * which is not technically the behavior here.
         */
        $this->disableMiddleware(AuthorizeModuleAccess::class);
    }

    /**
     * Return an index of existing entries.
     *
     * @param GroupRepositoryInterface $groups
     * @param  StreamRepositoryInterface $streams
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(
        GroupRepositoryInterface $groups,
        StreamRepositoryInterface $streams
    ) {
        /* @var GroupInterface $group */
        if (!$group = $groups->find($this->route->getAction('anomaly.module.streams::group.id'))) {
            abort(404);
        }

        /* @var StreamInterface $stream */
        if (!$stream = $streams->find($this->route->getAction('anomaly.module.streams::stream.id'))) {
            abort(404);
        }

        $builder = dispatch_sync(new GetEntryTableBuilder($stream));

        dispatch_sync(new AddDefaultTablePermissions($builder, $group, $stream));

        return $builder->render();
    }

    /**
     * Create a new entry.
     *
     * @param  StreamRepositoryInterface $streams
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function create(
        GroupRepositoryInterface $groups,
        StreamRepositoryInterface $streams
    ) {
        /* @var GroupInterface $group */
        if (!$group = $groups->find($this->route->getAction('anomaly.module.streams::group.id'))) {
            abort(404);
        }

        /* @var StreamInterface $stream */
        if (!$stream = $streams->find($this->route->getAction('anomaly.module.streams::stream.id'))) {
            abort(404);
        }

        $builder = dispatch_sync(new GetEntryFormBuilder($stream));

        dispatch_sync(new AddDefaultFormPermissions($builder, $group, $stream));

        return $builder->render();
    }

    /**
     * Edit an existing entry.
     *
     * @param GroupRepositoryInterface $groups
     * @param  StreamRepositoryInterface $streams
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit(
        GroupRepositoryInterface $groups,
        StreamRepositoryInterface $streams
    ) {
        /* @var GroupInterface $group */
        if (!$group = $groups->find($this->route->getAction('anomaly.module.streams::group.id'))) {
            abort(404);
        }

        /* @var StreamInterface $stream */
        if (!$stream = $streams->find($this->route->getAction('anomaly.module.streams::stream.id'))) {
            abort(404);
        }

        $builder = dispatch_sync(new GetEntryFormBuilder($stream));

        $builder->setOption(
            'permission',
            $builder->getOption(
                'permission',
                'anomaly.module.' . $group->getSlug() . '::' . $stream->getSlug() . '.write'
            )
        );

        return $builder->render($this->route->parameter('id'));
    }

}
