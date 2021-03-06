<?php namespace Anomaly\StreamsModule;

use Anomaly\Streams\Platform\Addon\AddonCollection;
use Anomaly\Streams\Platform\Addon\AddonServiceProvider;
use Anomaly\Streams\Platform\Addon\Event\AddonsHaveRegistered;
use Anomaly\Streams\Platform\Addon\Module\Module;
use Anomaly\Streams\Platform\Assignment\AssignmentRouter;
use Anomaly\Streams\Platform\Field\FieldRouter;
use Anomaly\Streams\Platform\Model\StreamsUtilities\StreamsUtilitiesGroupsEntryModel;
use Anomaly\Streams\Platform\Stream\Contract\StreamInterface;
use Anomaly\Streams\Platform\Ui\ControlPanel\Component\Navigation\Event\GatherNavigation;
use Anomaly\Streams\Platform\Ui\ControlPanel\Component\Section\Event\GatherSections;
use Anomaly\StreamsModule\Group\Contract\GroupInterface;
use Anomaly\StreamsModule\Group\Contract\GroupRepositoryInterface;
use Anomaly\StreamsModule\Group\GroupModel;
use Anomaly\StreamsModule\Group\GroupRepository;
use Anomaly\StreamsModule\Group\Listener\AddVirtualizedNavigation;
use Anomaly\StreamsModule\Group\Listener\AddVirtualizedSections;
use Anomaly\StreamsModule\Group\Listener\LocateVirtualizedModels;
use Anomaly\StreamsModule\Http\Controller\Admin\AssignmentsController;
use Anomaly\StreamsModule\Http\Controller\Admin\FieldsController;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Routing\Router;

/**
 * Class StreamsModuleServiceProvider
 *
 * @link          http://pyrocms.com/
 * @author        PyroCMS, Inc. <support@pyrocms.com>
 * @author        Ryan Thompson <ryan@pyrocms.com>
 * @package       Anomaly\StreamsModule
 */
class StreamsModuleServiceProvider extends AddonServiceProvider
{

    /**
     * The addon listeners.
     *
     * @var array
     */
    protected $listeners = [
        GatherSections::class       => [
            AddVirtualizedSections::class,
        ],
        GatherNavigation::class     => [
            AddVirtualizedNavigation::class,
        ],
        AddonsHaveRegistered::class => [
            LocateVirtualizedModels::class,
        ],
    ];

    /**
     * The addon routes.
     *
     * @var array
     */
    protected $routes = [
        'admin/streams'                            => 'Anomaly\StreamsModule\Http\Controller\Admin\StreamsController@index',
        'admin/streams/create'                     => 'Anomaly\StreamsModule\Http\Controller\Admin\StreamsController@create',
        'admin/streams/edit/{id}'                  => 'Anomaly\StreamsModule\Http\Controller\Admin\StreamsController@edit',
        'admin/streams/entries/choose'             => 'Anomaly\StreamsModule\Http\Controller\Admin\EntriesController@choose',
        'admin/streams/entries/{stream}'           => 'Anomaly\StreamsModule\Http\Controller\Admin\EntriesController@index',
        'admin/streams/entries/{stream}/create'    => 'Anomaly\StreamsModule\Http\Controller\Admin\EntriesController@create',
        'admin/streams/entries/{stream}/edit/{id}' => 'Anomaly\StreamsModule\Http\Controller\Admin\EntriesController@edit',
        'admin/streams/namespaces'                 => 'Anomaly\StreamsModule\Http\Controller\Admin\GroupsController@index',
        'admin/streams/namespaces/create'          => 'Anomaly\StreamsModule\Http\Controller\Admin\GroupsController@create',
        'admin/streams/namespaces/change'          => 'Anomaly\StreamsModule\Http\Controller\Admin\GroupsController@change',
        'admin/streams/namespaces/edit/{id}'       => 'Anomaly\StreamsModule\Http\Controller\Admin\GroupsController@edit',
    ];

    /**
     * The addon bindings.
     *
     * @var array
     */
    protected $bindings = [
        StreamsUtilitiesGroupsEntryModel::class => GroupModel::class,
    ];

    /**
     * The addon singletons.
     *
     * @var array
     */
    protected $singletons = [
        GroupRepositoryInterface::class => GroupRepository::class,
    ];

    /**
     * Map the addon.
     *
     * @param Router $router
     * @param Repository $config
     * @param FieldRouter $fields
     * @param AddonCollection $addons
     * @param AssignmentRouter $assignments
     * @param GroupRepositoryInterface $groups
     * @internal param Request $request
     */
    public function map(
        Router $router,
        Repository $config,
        FieldRouter $fields,
        AddonCollection $addons,
        AssignmentRouter $assignments,
        GroupRepositoryInterface $groups
    ) {
        $fields->route($this->addon, FieldsController::class);
        $assignments->route($this->addon, AssignmentsController::class);

        /* @var GroupInterface $group */
        foreach ($groups->virtualized() as $group) {

            $uri = 'admin/' . $group->getSlug();

            $namespace = $group->getSlug();

            /* @var StreamInterface $stream */
            foreach ($group->getStreams() as $k => $stream) {

                $slug = $stream->getSlug();

                $defaults = [
                    'streams::addon'                    => 'anomaly.module.' . $namespace,
                    'anomaly.module.streams::stream.id' => $stream->getId(),
                    'anomaly.module.streams::group.id'  => $group->getId(),
                ];

                $index = array_merge(
                    ['uses' => 'Anomaly\StreamsModule\Http\Controller\Admin\VirtualController@index'],
                    $config->get("anomaly.module.streams::{$namespace}.{$slug}.routes.index", []),
                    $defaults
                );

                $create = array_merge(
                    ['uses' => 'Anomaly\StreamsModule\Http\Controller\Admin\VirtualController@create'],
                    $config->get("anomaly.module.streams::{$namespace}.{$slug}.routes.create", []),
                    $defaults
                );

                $edit = array_merge(
                    ['uses' => 'Anomaly\StreamsModule\Http\Controller\Admin\VirtualController@edit'],
                    $config->get("anomaly.module.streams::{$namespace}.{$slug}.routes.edit", []),
                    $defaults
                );

                $router->any($uri . ($k == 0 ? '' : '/' . $slug), $index);
                $router->any($uri . ($k == 0 ? '' : '/' . $slug) . '/create', $create);
                $router->any($uri . ($k == 0 ? '' : '/' . $slug) . '/edit/{id}', $edit);
            }

            $addons->put(
                'anomaly.module.' . $namespace,
                (new Module())
                    ->setType('module')
                    ->setSlug($namespace)
                    ->setVendor('anomaly')
                    ->setDescription($group->getDescription())
                    ->setName($group->getName() . ' ' . trans('streams::addon.module'))
            );
        }

        $addons->disperse();
    }

    /**
     * Boot the addon.
     *
     * @param GroupRepositoryInterface $groups
     * @param Repository $config
     */
    public function boot(GroupRepositoryInterface $groups, Repository $config)
    {

        $permissions = $config->get('anomaly.module.users::config.permissions');

        /* @var GroupInterface $group */
        foreach ($groups->all() as $group) {

            if ($group->isVirtualized()) {
                $permissions['anomaly.module.' . $group->getSlug()]['title']       = $group->getName();
                $permissions['anomaly.module.' . $group->getSlug()]['description'] = $group->getDescription();
            }

            /* @var StreamInterface $stream */
            foreach ($group->getStreams() as $stream) {

                $namespace = 'anomaly.module.' . ($group->isVirtualized() ? $group->getSlug() : 'streams');

                $model = $stream->getEntryModel();

                $model->bind(
                    '__locate',
                    function () use ($namespace) {
                        return $namespace;
                    }
                );

                $config->set(
                    $namespace . '::permissions.' . $stream->getSlug(),
                    [
                        'read',
                        'write',
                        'delete',
                    ]
                );

                $permissions[$namespace]['permissions'][$stream->getSlug()] = [
                    'label'     => $stream->getName(),
                    'available' => [
                        'anomaly.module.' . $group->getSlug() . '::' . $stream->getSlug(
                        ) . '.read'                                                                   => 'anomaly.module.streams::permission.entries.option.read',
                        'anomaly.module.' . $group->getSlug() . '::' . $stream->getSlug(
                        ) . '.write'                                                                  => 'anomaly.module.streams::permission.entries.option.write',
                        'anomaly.module.' . $group->getSlug() . '::' . $stream->getSlug(
                        ) . '.delete'                                                                 => 'anomaly.module.streams::permission.entries.option.delete',
                    ],
                ];
            }
        }

        $config->set('anomaly.module.users::config.permissions', $permissions);
    }
}
