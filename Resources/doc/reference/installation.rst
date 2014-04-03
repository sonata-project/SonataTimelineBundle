Installation
============

* Add SonataNewsBundle to your vendor/bundles dir via composer

.. code-block:: json

    //composer.json
    "require": {
    //...
        "sonata-project/timeline-bundle": "~2.2@dev",
    //...
    }


* Add SonataNewsBundle to your application kernel:

.. code-block:: php

    <?php

    // app/AppKernel.php
    public function registerBundles()
    {
        return array(
            // ...
            new Spy\TimelineBundle\SpyTimelineBundle(),
            new Sonata\CoreBundle\SonataCoreBundle(),
            new Sonata\TimelineBundle\SonataTimelineBundle(),
            // ...
        );
    }

* Create a configuration file : ``sonata_timeline.yml``:

.. code-block:: yaml

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

    sonata_timeline:
        manager_type: orm

* import the ``sonata_timeline.yml`` file in the ``config.yml`` file:

.. code-block:: yaml

    imports:
        #...
        - { resource: sonata_timeline.yml }


* Run the easy-extends command:

.. code-block:: bash

    php app/console sonata:easy-extends:generate SonataTimelineBundle -d src

* Enable the new bundle:

.. code-block:: php

    // app/AppKernel.php
    public function registerBundles()
    {
        return array(
            // ...
            new Application\Sonata\TimelineBundle\ApplicationSonataTimelineBundle() // easy extends integration
            // ...
        );
    }


* enable the block in the admin bundle:

.. code-block:: yaml

    sonata_block:
        # ... other configuration options

        blocks:
            # ... other blocks

            sonata.timeline.block.timeline:

    sonata_admin:
        # ... other configuration options

        dashboard:
            blocks:
                # ... other blocks

                - { position: center, type: sonata.timeline.block.timeline, settings: { context: SONATA_ADMIN, max_per_page: 25 }}
