<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\TimelineBundle\Tests\DependencyInjection;

use Sonata\TimelineBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;

/**
 * Class ConfigurationTest.
 */
class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testBCCode()
    {
        $processor = new Processor();
        $configuration = $processor->processConfiguration(new Configuration(), array(array(
            'class' => array('actionComponent' => 'stdClass'),
        )));

        $expected = array(
            'class' => array(
                'action_component' => 'stdClass',
                'component' => '%spy_timeline.class.component%',
                'action' => '%spy_timeline.class.action%',
                'timeline' => '%spy_timeline.class.timeline%',
            ),
            'manager_type' => 'orm',
        );

        $this->assertEquals($expected, $configuration);
    }
}
