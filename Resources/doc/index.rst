Timeline Bundle
===============

The ``SonataTimelineBundle`` integrates the ``SpyTimelineBundle`` into ``SonataAdmin``. Timeline allows to create an action wall
for a user or a group of users. The current implementation integrate the basic features of the ``SpyTimelineBundle``:

 - doctrine's models with ``SonataEasyExtends``
 - a TimelineBlock for admin action only
 - an admin extension to catch event and create timeline entry with the ``AdminSpread``

.. note::

    Only users with role ``ROLE_SUPER_ADMIN`` can have timeline entry.

.. note::

    This is a work in progress, change may occurs in order to tweak performance issues, configuration options, or the
    rendering workflow.

Reference Guide
---------------

.. toctree::
   :maxdepth: 1
   :numbered:

   reference/installation
   reference/configuration
