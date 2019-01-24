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

use PHPUnit\Framework\TestCase;
use Sonata\TimelineBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;

/**
 * Class ConfigurationTest.
 */
class ConfigurationTest extends TestCase
{
    public function testBCCode(): void
    {
        $processor = new Processor();
        $configuration = $processor->processConfiguration(new Configuration(), [[
            'class' => ['actionComponent' => 'stdClass'],
        ]]);

        $expected = [
            'class' => [
                'action_component' => 'stdClass',
                'component' => '%spy_timeline.class.component%',
                'action' => '%spy_timeline.class.action%',
                'timeline' => '%spy_timeline.class.timeline%',
            ],
            'manager_type' => 'orm',
        ];

        $this->assertSame($expected, $configuration);
    }
}
