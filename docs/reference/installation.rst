.. index::
    single: Installation
    single: Configuration

Installation
============

Prerequisites
-------------

PHP ^7.2 and Symfony ^4.4 are needed to make this bundle work, there are
also some Sonata dependencies that need to be installed and configured beforehand.

Required dependencies:

* `SonataBlockBundle <https://sonata-project.org/bundles/block>`_

And the persistence bundle (currently, not all the implementations of the Sonata persistence bundles are available):

* `SonataDoctrineOrmAdminBundle <https://sonata-project.org/bundles/doctrine-orm-admin>`_

Follow also their configuration step; you will find everything you need in
their own installation chapter.

.. note::

    If a dependency is already installed somewhere in your project or in
    another dependency, you won't need to install it again.

Enable the Bundle
-----------------

Add ``SonataTimelineBundle`` via composer::

    composer require sonata-project/timeline-bundle

.. note::

    This will install the SpyTimelineBundle_, too.

Next, be sure to enable the bundles in your ``config/bundles.php`` file if they
are not already enabled::

    // config/bundles.php

    return [
        // ...
        Sonata\TimelineBundle\SonataTimelineBundle::class => ['all' => true],
        Spy\TimelineBundle\SpyTimelineBundle::class => ['all' => true],
    ];

Configuration
=============

SpyTimelineBundle Configuration
-------------------------------

.. code-block:: yaml

    # config/packages/spy_timeline.yaml

    spy_timeline:
        drivers:
            orm:
                object_manager: doctrine.orm.entity_manager
                classes:
                    query_builder: ~ # Spy\TimelineBundle\Driver\ORM\QueryBuilder\QueryBuilder
                    timeline: App\Entity\SonataTimelineTimeline
                    action: App\Entity\SonataTimelineAction
                    component: App\Entity\SonataTimelineComponent
                    action_component: App\Entity\SonataTimelineActionComponent

        filters:
            data_hydrator:
                priority: 20
                service: spy_timeline.filter.data_hydrator
                filter_unresolved: false
                locators:
                    - spy_timeline.filter.data_hydrator.locator.doctrine_orm

SonataTimelineBundle Configuration
----------------------------------

.. code-block:: yaml

    # config/packages/sonata_timeline.yaml

    sonata_timeline:
        manager_type: orm
        class:
            timeline: App\Entity\SonataTimelineTimeline
            action: App\Entity\SonataTimelineAction
            component: App\Entity\SonataTimelineComponent
            action_component: App\Entity\SonataTimelineActionComponent

Doctrine ORM Configuration
--------------------------

Add these bundles in the config mapping definition (or enable `auto_mapping`_)::

    # config/packages/doctrine.yaml

    doctrine:
        orm:
            entity_managers:
                default:
                    mappings:
                        SonataTimelineBundle: ~
                        SpyTimelineBundle: ~

And then create the corresponding entities, ``src/Entity/SonataTimelineTimeline``::

    // src/Entity/SonataTimelineTimeline.php

    use Doctrine\ORM\Mapping as ORM;
    use Sonata\TimelineBundle\Entity\Timeline;

    /**
     * @ORM\Entity
     * @ORM\Table(name="timeline__timeline")
     */
    class SonataTimelineTimeline extends Timeline
    {
        /**
         * @ORM\Id
         * @ORM\GeneratedValue
         * @ORM\Column(type="integer")
         */
        protected $id;
    }

``src/Entity/SonataTimelineAction``::

    // src/Entity/SonataTimelineAction.php

    use Doctrine\ORM\Mapping as ORM;
    use Sonata\TimelineBundle\Entity\Action;

    /**
     * @ORM\Entity
     * @ORM\Table(name="timeline__action")
     */
    class SonataTimelineAction extends Action
    {
        /**
         * @ORM\Id
         * @ORM\GeneratedValue
         * @ORM\Column(type="integer")
         */
        protected $id;
    }

``src/Entity/SonataTimelineComponent``::

    // src/Entity/SonataTimelineComponent.php

    use Doctrine\ORM\Mapping as ORM;
    use Sonata\TimelineBundle\Entity\Component;

    /**
     * @ORM\Entity
     * @ORM\Table(name="timeline__component")
     */
    class SonataTimelineComponent extends Component
    {
        /**
         * @ORM\Id
         * @ORM\GeneratedValue
         * @ORM\Column(type="integer")
         */
        protected $id;
    }

and ``src/Entity/SonataTimelineActionComponent``::

    // src/Entity/SonataTimelineActionComponent.php

    use Doctrine\ORM\Mapping as ORM;
    use Sonata\TimelineBundle\Entity\ActionComponent;

    /**
     * @ORM\Entity
     * @ORM\Table(name="timeline__action_component")
     */
    class SonataTimelineActionComponent extends ActionComponent
    {
        /**
         * @ORM\Id
         * @ORM\GeneratedValue
         * @ORM\Column(type="integer")
         */
        protected $id;
    }

The only thing left is to update your schema::

    bin/console doctrine:schema:update --force

Enable the Timeline Block
-------------------------

.. configuration-block::

    .. code-block:: yaml

        # config/packages/sonata_admin.yaml

        sonata_admin:
            dashboard:
                blocks:
                    - { position: center, type: sonata.timeline.block.timeline, settings: { context: SONATA_ADMIN, max_per_page: 25 }}

    .. code-block:: yaml

        # config/packages/sonata_block.yaml

        sonata_block:
            blocks:
                sonata.timeline.block.timeline:

Edit the Timeline Block
-----------------------

Create a new template file here, based on the default ``timeline.html.twig``

.. code-block:: bash

    src/Application/TimelineBundle/Resources/views/Block/timeline.html.twig

And then edit the sonata_admin definition here, adding the "template" option.

  .. code-block:: yaml

        # config/packages/sonata_admin.yaml

        sonata_admin:
            dashboard:
                blocks:
                    - { position: center, type: sonata.timeline.block.timeline, settings: { template: '@ApplicationTimeline/Block/timeline.html.twig', context: SONATA_ADMIN, max_per_page: 25 }}

And now, you're good to go !

Next Steps
----------

At this point, your Symfony installation should be fully functional, withouth errors
showing up from SonataTimelineBundle. If, at this point or during the installation,
you come across any errors, don't panic:

    - Read the error message carefully. Try to find out exactly which bundle is causing the error.
      Is it SonataTimelineBundle or one of the dependencies?
    - Make sure you followed all the instructions correctly, for both SonataTimelineBundle and its dependencies.
    - Still no luck? Try checking the project's `open issues on GitHub`_.

.. _`open issues on GitHub`: https://github.com/sonata-project/SonataTimelineBundle/issues
.. _SpyTimelineBundle: https://github.com/stephpy/timeline-bundle
.. _`auto_mapping`: http://symfony.com/doc/4.4/reference/configuration/doctrine.html#configuration-overviews
