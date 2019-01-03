.. index::
    single: Installation
    single: Configuration

Installation
============

The easiest way to install ``SonataTimelineBundle`` is to require it with Composer:

.. code-block:: bash

    $ composer require sonata-project/timeline-bundle

Alternatively, you could add a dependency into your ``composer.json`` file directly.

.. note::

    This will install the SpyTimelineBundle_, too.

Now, enable the bundle in ``bundles.php`` file::

    // config/bundles.php

    return [
        //...
        Sonata\CoreBundle\SonataCoreBundle::class => ['all' => true],
        Sonata\TimelineBundle\SonataTimelineBundle::class => ['all' => true],
        Spy\TimelineBundle\SpyTimelineBundle::class => ['all' => true],
    ];

.. note::

    If you are not using Symfony Flex, you should enable bundles in your
    ``AppKernel.php``.

.. code-block:: php

    // app/AppKernel.php

    public function registerBundles()
    {
        return array(
            // ...
            new Sonata\CoreBundle\SonataCoreBundle(),
            // ...
            new Sonata\TimelineBundle\SonataTimelineBundle(),
            new Spy\TimelineBundle\SpyTimelineBundle(),
            // ...
        );
    }

Configuration
-------------

.. note::

    If you are not using Symfony Flex, all configuration in this section should
    be added to ``app/config/config.yml``.

SpyTimelineBundle Configuration
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. code-block:: yaml

    # config/packages/spy_timeline.yaml

    spy_timeline:
        drivers:
            orm:
                object_manager: doctrine.orm.entity_manager
                classes:
                    query_builder: ~ # Spy\TimelineBundle\Driver\ORM\QueryBuilder\QueryBuilder
                    timeline:         App\Application\Sonata\TimelineBundle\Entity\Timeline
                    action:           App\Application\Sonata\TimelineBundle\Entity\Action
                    component:        App\Application\Sonata\TimelineBundle\Entity\Component
                    action_component: App\Application\Sonata\TimelineBundle\Entity\ActionComponent

        filters:
            data_hydrator:
                priority:          20
                service:           spy_timeline.filter.data_hydrator
                filter_unresolved: false
                locators:
                    - spy_timeline.filter.data_hydrator.locator.doctrine_orm

.. note::

    If you are not using Symfony Flex, add classes without the ``App\``
    part.

SonataTimelineBundle Configuration
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. code-block:: yaml

    # config/packages/sonata_timeline.yaml

    sonata_timeline:
        manager_type:         orm
        class:
            timeline:         "%spy_timeline.class.timeline%"
            action:           "%spy_timeline.class.action%"
            component:        "%spy_timeline.class.component%"
            action_component: "%spy_timeline.class.action_component%"

Extend the Bundle
-----------------

At this point, the bundle is usable, but not quite ready yet. You need to
generate the correct entities for the timeline:

.. code-block:: bash

    $ bin/console sonata:easy-extends:generate SonataTimelineBundle --dest=src --namespace_prefix=App

.. note::

    If you are not using Symfony Flex, use command without ``--namespace_prefix=App``.

With provided parameters, the files are generated in ``src/Application/Sonata/TimelineBundle``.

.. note::

    The command will generate domain objects in an ``App\Application`` namespace.
    So you can point entities associations to a global and common namespace.
    This will make entities sharing very easily as your models are accessible
    through a global namespace. For instance the action will be
    ``App\Application\Sonata\TimelineBundle\Entity\Action``.

.. note::

    If you are not using Symfony Flex, the namespace will be ``Application\Sonata\TimelineBundle\Entity``.


Now, add the new ``Application`` Bundle into the ``bundles.php``::

    // config/bundles.php

    return [
        //...
        App\Application\Sonata\TimelineBundle\ApplicationSonataTimelineBundle::class => ['all' => true],
    ];

.. note::

    If you are not using Symfony Flex, add the new ``Application`` Bundle into your
    ``AppKernel.php``.

.. code-block:: php

    // app/AppKernel.php

    class AppKernel {
        public function registerbundles()
        {
            return array(
                // Application Bundles
                // ...
                new Application\Sonata\TimelineBundle\ApplicationSonataTimelineBundle(),
                // ...

            )
        }
    }

Update the Database Schema
~~~~~~~~~~~~~~~~~~~~~~~~~~

.. code-block:: bash

    $ bin/console doctrine:schema:update --force

Enable the Timeline Block
-------------------------

.. configuration-block::

    .. code-block:: yaml

        # config/packages/sonata_block.yaml

        sonata_block:
            blocks:
                # ...
                sonata.timeline.block.timeline:

    .. code-block:: yaml

        # config/packages/sonata_admin.yaml

        sonata_admin:
            dashboard:
                blocks:
                    # ...
                    - { position: center, type: sonata.timeline.block.timeline, settings: { context: SONATA_ADMIN, max_per_page: 25 }}

.. note::

    If you are not using Symfony Flex, this configuration should be added
    to ``app/config/config.yml``.

Edit the Timeline Block
-----------------------

.. configuration-block::

Create a new template file here, based on the default ``timeline.html.twig``

.. code-block:: bash

    src/Application/TimelineBundle/Resources/views/Block/timeline.html.twig

And then edit the sonata_admin definition here, adding the "template" option.

  .. code-block:: yaml

        # config/packages/sonata_admin.yaml

        sonata_admin:
            dashboard:
                blocks:
                    # ...
                    - { position: center, type: sonata.timeline.block.timeline, settings: { template: '@ApplicationTimeline/Block/timeline.html.twig', context: SONATA_ADMIN, max_per_page: 25 }}

.. note::

    If you are not using Symfony Flex, this configuration should be added
    to ``app/config/config.yml``.

And now, you're good to go !

.. _SpyTimelineBundle: https://github.com/stephpy/timeline-bundle
