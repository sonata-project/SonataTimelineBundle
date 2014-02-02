<?php

/*
 * This file is part of sonata-project.
 *
 * (c) 2010 Thomas Rabaix
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\TimelineBundle\Twig\Extension;

use Sonata\AdminBundle\Admin\Pool;
use Spy\Timeline\Model\ComponentInterface;

class SonataTimelineExtension extends \Twig_Extension
{
    /**
     * @var Pool
     */
    protected $pool;

    /**
     * @param Pool $pool
     */
    public function __construct(Pool $pool = null)
    {
        $this->pool = $pool;
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('sonata_timeline_generate_link', array($this, 'generateLink'), array('is_safe' => array('html'))),
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'sonata_timeline';
    }

    /**
     * @param ComponentInterface $component
     *
     * @return string
     */
    public function generateLink(ComponentInterface $component)
    {
        if (!$this->pool) {
            return $component->getHash();
        }

        $admin = $this->pool->getAdminByClass($component->getModel());

        if (!$admin) {
            return $component->getHash();
        }

        return sprintf('<a href="%s">%s</a>',
            $admin->generateObjectUrl('edit', $component->getData()),
            $admin->toString($component->getData())
        );
    }
}
