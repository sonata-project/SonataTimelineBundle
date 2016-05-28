<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\TimelineBundle\Tests\Twig\Extension;

use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Admin\Pool;
use Sonata\TimelineBundle\Twig\Extension\SonataTimelineExtension;
use Spy\TimelineBundle\Document\Action;
use Spy\TimelineBundle\Document\Component;

class SonataTimelineExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SonataTimelineExtension
     */
    private $twigExtension;

    /**
     * @var Pool
     */
    private $pool;

    /**
     * @var AdminInterface
     */
    private $admin;

    public function setUp()
    {
        $this->admin = $this->getMock('Sonata\AdminBundle\Admin\AdminInterface');

        $this->pool = $this->getMockBuilder('Sonata\AdminBundle\Admin\Pool')->disableOriginalConstructor()->getMock();
        $this->pool->expects($this->any())
            ->method('getAdminByClass')
            ->with($this->equalTo('Acme\DemoBundle\Model\Demo'))
            ->will($this->returnValue($this->admin));

        $this->twigExtension = new SonataTimelineExtension($this->pool);
    }

    public function testGenerateLink()
    {
        $component = new Component();
        $component->setModel('Acme\DemoBundle\Model\Demo');
        $component->setIdentifier(array('2'));

        $action = new Action();

        $this->admin->expects($this->once())
            ->method('hasRoute')
            ->with($this->equalTo('edit'))
            ->will($this->returnValue(true));
        $this->admin->expects($this->once())
            ->method('isGranted')
            ->with($this->equalTo('EDIT'))
            ->will($this->returnValue(true));

        $this->admin->expects($this->once())
            ->method('generateObjectUrl')
            ->with($this->equalTo('edit'), $this->anything())
            ->will($this->returnValue('acme/demo/2/edit'));

        $this->admin->expects($this->once())
            ->method('toString')
            ->with($this->anything())
            ->will($this->returnValue('Text'));

        $this->assertEquals('<a href="acme/demo/2/edit">Text</a>', $this->twigExtension->generateLink($component, $action));
    }

    public function testGenerateLinkDisabledEdit()
    {
        $component = new Component();
        $component->setModel('Acme\DemoBundle\Model\Demo');
        $component->setIdentifier(array('2'));

        $action = new Action();

        $this->admin->expects($this->at(0))
            ->method('hasRoute')
            ->with($this->equalTo('edit'))
            ->will($this->returnValue(false));
        $this->admin->expects($this->at(1))
            ->method('hasRoute')
            ->with($this->equalTo('show'))
            ->will($this->returnValue(true));
        $this->admin->expects($this->at(2))
            ->method('isGranted')
            ->with($this->equalTo('SHOW'))
            ->will($this->returnValue(true));

        $this->admin->expects($this->once())
            ->method('generateObjectUrl')
            ->with($this->equalTo('show'), $this->anything())
            ->will($this->returnValue('acme/demo/2/show'));

        $this->admin->expects($this->once())
            ->method('toString')
            ->with($this->anything())
            ->will($this->returnValue('Text'));

        $this->assertEquals('<a href="acme/demo/2/show">Text</a>', $this->twigExtension->generateLink($component, $action));
    }

    public function testGenerateLinkNoEditPermission()
    {
        $component = new Component();
        $component->setModel('Acme\DemoBundle\Model\Demo');
        $component->setIdentifier(array('2'));

        $action = new Action();

        $this->admin->expects($this->at(0))
            ->method('hasRoute')
            ->with($this->equalTo('edit'))
            ->will($this->returnValue(true));
        $this->admin->expects($this->at(1))
            ->method('isGranted')
            ->with($this->equalTo('EDIT'))
            ->will($this->returnValue(false));
        $this->admin->expects($this->at(2))
            ->method('hasRoute')
            ->with($this->equalTo('show'))
            ->will($this->returnValue(true));
        $this->admin->expects($this->at(3))
            ->method('isGranted')
            ->with($this->equalTo('SHOW'))
            ->will($this->returnValue(true));

        $this->admin->expects($this->once())
            ->method('generateObjectUrl')
            ->with($this->equalTo('show'), $this->anything())
            ->will($this->returnValue('acme/demo/2/show'));

        $this->admin->expects($this->once())
            ->method('toString')
            ->with($this->anything())
            ->will($this->returnValue('Text'));

        $this->assertEquals('<a href="acme/demo/2/show">Text</a>', $this->twigExtension->generateLink($component, $action));
    }

    public function testGenerateLinkDisabledEditAndShow()
    {
        $component = new Component();
        $component->setModel('Acme\DemoBundle\Model\Demo');
        $component->setIdentifier('2');

        $action = new Action();

        $this->admin->expects($this->at(0))
            ->method('hasRoute')
            ->with($this->equalTo('edit'))
            ->will($this->returnValue(false));
        $this->admin->expects($this->at(1))
            ->method('hasRoute')
            ->with($this->equalTo('show'))
            ->will($this->returnValue(false));

        $this->admin->expects($this->once())
            ->method('toString')
            ->with($this->anything())
            ->will($this->returnValue('Text'));

        $this->assertEquals('Text', $this->twigExtension->generateLink($component, $action));
    }
}
