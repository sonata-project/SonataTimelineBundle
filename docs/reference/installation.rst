.. index::
    single: Installation
    single: Configuration

Installation
============

The easiest way to install ``SonataTimelineBundle`` is to require it with Composer:

.. code-block:: bash

    $ php composer.phar require sonata-project/timeline-bundle

Alternatively, you could add a dependency into your ``composer.json`` file directly.

.. note::

    This will install the SpyTimelineBundle_, too.

Now, enable the bundle in the kernel:

.. code-block:: php

    <?php
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

To use the ``BlockBundle``, add the following lines to your application configuration file:

.. configuration-block::

    .. code-block:: yaml

        # app/config/config.yml

        spy_timeline:
            drivers:
                orm:
                    object_manager: doctrine.orm.entity_manager
                    classes:
                        query_builder: ~ # Spy\TimelineBundle\Driver\ORM\QueryBuilder\QueryBuilder
                        timeline:         Application\Sonata\TimelineBundle\Entity\Timeline
                        action:           Application\Sonata\TimelineBundle\Entity\Action
                        component:        Application\Sonata\TimelineBundle\Entity\Component
                        action_component: Application\Sonata\TimelineBundle\Entity\ActionComponent

            filters:
                data_hydrator:
                    priority:             20
                    service:              spy_timeline.filter.data_hydrator
                    filter_unresolved:    false
                    locators:
                        - spy_timeline.filter.data_hydrator.locator.doctrine_orm

    .. code-block:: yaml

        # app/config/config.yml

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

    $ php app/console sonata:easy-extends:generate SonataTimelineBundle --dest=src

If you don't specify the ``--dest`` parameter, the files are generated in ``app/Application/Sonata/...```.

.. note::

    The command will generate domain objects in an ``Application`` namespace.
    So you can point entities associations to a global and common namespace.
    This will make entities sharing very easily as your models are accessible
    through a global namespace. For instance the action will be
    ``Application\Sonata\TimelineBundle\Entity\Action``.

Enable the extended Bundle
^^^^^^^^^^^^^^^^^^^^^^^^^^

.. code-block:: php

    <?php
    // app/AppKernel.php

    public function registerBundles()
    {
        return array(
            // ...

            // Application Bundles
            new Application\Sonata\TimelineBundle\ApplicationSonataTimelineBundle(),

            // ...
        );
    }

Update the Database Schema
^^^^^^^^^^^^^^^^^^^^^^^^^^

.. code-block:: bash

    $ app/console doctrine:schema:update --force

Enable the Timeline Block
-------------------------

.. configuration-block::

    .. code-block:: yaml

        # app/config/config.yml

        sonata_block:
            blocks:
                # ...
                sonata.timeline.block.timeline:

    .. code-block:: yaml

        # app/config/config.yml

        sonata_admin:
            dashboard:
                blocks:
                    # ...
                    - { position: center, type: sonata.timeline.block.timeline, settings: { context: SONATA_ADMIN, max_per_page: 25 }}


Edit the Timeline Block
-----------------------

.. configuration-block::

Create a new template file here, based on the default ``timeline.html.twig``

.. code-block:: bash

    src/Application/TimelineBundle/Resources/views/Block/timeline.html.twig

And then edit the sonata_admin definition here, adding the "template" option.

  .. code-block:: yaml

        # app/config/config.yml

        sonata_admin:
            dashboard:
                blocks:
                    # ...
                    - { position: center, type: sonata.timeline.block.timeline, settings: { template: 'ApplicationTimelineBundle::Block:timeline.html.twig', context: SONATA_ADMIN, max_per_page: 25 }}

And now, you're good to go !

.. _SpyTimelineBundle: https://github.com/stephpy/timeline-bundle
