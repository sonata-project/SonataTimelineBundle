.. index::
    single: Configuration

Configuration
=============

Full Configuration Options
--------------------------

.. configuration-block::

    .. code-block:: yaml

        # Default configuration for extension with alias: "sonata_timeline"
        sonata_timeline:
            manager_type:         orm
            class:
                component:            '%spy_timeline.class.component%'
                actionComponent:      ~
                action_component:     '%spy_timeline.class.action_component%'
                action:               '%spy_timeline.class.action%'
                timeline:             '%spy_timeline.class.timeline%'

Customize the Timeline Block
----------------------------

You can customize the ``title`` (default: ``Latest Actions``) of the block by using these config options:

.. configuration-block::

    .. code-block:: yaml

        # app/config/config.yml

        sonata_admin:
            dashboard:
                blocks:
                    # ...
                    - { position: center, type: sonata.timeline.block.timeline, settings: { context: SONATA_ADMIN, max_per_page: 25, title: "My Timeline Block" }}

You can customize the ``icon`` (default: ``<i class="fa fa-clock-o fa-fw"></i>``) of the block by using these config options:

.. configuration-block::

    .. code-block:: yaml

        # app/config/config.yml

        sonata_admin:
            dashboard:
                blocks:
                    # ...
                    - { position: center, type: sonata.timeline.block.timeline, settings: { context: SONATA_ADMIN, max_per_page: 25, icon: '<i class="fa fa-flag-o fa-fw"></i>' }}

