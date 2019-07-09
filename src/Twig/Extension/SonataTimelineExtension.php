<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\TimelineBundle\Twig\Extension;

use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Admin\Pool;
use Spy\Timeline\Model\ActionInterface;
use Spy\Timeline\Model\ComponentInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SonataTimelineExtension extends AbstractExtension
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
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('sonata_timeline_generate_link', [$this, 'generateLink'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sonata_timeline';
    }

    /**
     * @return string
     */
    public function generateLink(ComponentInterface $component, ActionInterface $action = null)
    {
        if (!$this->pool) {
            return $component->getHash();
        }

        $admin = $this->getAdmin($component, $action);

        if (!$admin) {
            return $component->getHash();
        }

        foreach (['edit', 'show'] as $mode) {
            if ($admin->hasRoute($mode) && $admin->isGranted(strtoupper($mode))) {
                return sprintf(
                    '<a href="%s">%s</a>',
                    $admin->generateObjectUrl($mode, $component->getData()),
                    $admin->toString($component->getData())
                );
            }
        }

        return $admin->toString($component->getData());
    }

    /**
     * @return AdminInterface
     */
    protected function getAdmin(ComponentInterface $component, ActionInterface $action = null)
    {
        if ($action && $adminComponent = $action->getComponent('admin_code')) {
            return $this->pool->getAdminByAdminCode($adminComponent);
        }

        try {
            return $this->pool->getAdminByClass($component->getModel());
        } catch (\RuntimeException $e) {
        }

        return false;
    }
}
