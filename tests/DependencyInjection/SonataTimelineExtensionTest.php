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

namespace Sonata\TimelineBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Sonata\TimelineBundle\DependencyInjection\SonataTimelineExtension;

class SonataTimelineExtensionTest extends AbstractExtensionTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->container->setParameter('kernel.bundles', ['SonataDoctrineBundle' => true]);
    }

    public function testLoadDefault(): void
    {
        $this->load(['class' => [
            'user' => 'App\Entity\User',
        ]]);

        $this->assertContainerBuilderHasServiceDefinitionWithArgument('sonata.timeline.admin.extension', 1);
        $this->assertContainerBuilderHasServiceDefinitionWithArgument('sonata.timeline.block.timeline', 4);
    }

    protected function getContainerExtensions(): array
    {
        return [
            new SonataTimelineExtension(),
        ];
    }
}
