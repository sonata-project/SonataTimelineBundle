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

namespace Sonata\TimelineBundle\Tests\Twig\Extension;

use PHPUnit\Framework\TestCase;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Admin\Pool;
use Sonata\TimelineBundle\Twig\Extension\SonataTimelineExtension;
use Spy\TimelineBundle\Document\Action;
use Spy\TimelineBundle\Document\Component;

class SonataTimelineExtensionTest extends TestCase
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

    protected function setUp(): void
    {
        $this->admin = $this->createMock(AdminInterface::class);

        $this->pool = $this->createMock(Pool::class);
        $this->pool
            ->method('getAdminByClass')
            ->with(static::equalTo('Acme\DemoBundle\Model\Demo'))
            ->willReturn($this->admin);

        $this->twigExtension = new SonataTimelineExtension($this->pool);
    }

    public function testGenerateLink(): void
    {
        $component = new Component();
        $component->setModel('Acme\DemoBundle\Model\Demo');
        $component->setIdentifier(['2']);

        $action = new Action();

        $this->admin->expects(static::once())
            ->method('hasRoute')
            ->with(static::equalTo('edit'))
            ->willReturn(true);
        $this->admin->expects(static::once())
            ->method('isGranted')
            ->with(static::equalTo('EDIT'))
            ->willReturn(true);

        $this->admin->expects(static::once())
            ->method('generateObjectUrl')
            ->with(static::equalTo('edit'), static::anything())
            ->willReturn('acme/demo/2/edit');

        $this->admin->expects(static::once())
            ->method('toString')
            ->with(static::anything())
            ->willReturn('Text');

        static::assertSame('<a href="acme/demo/2/edit">Text</a>', $this->twigExtension->generateLink($component, $action));
    }

    public function testGenerateLinkDisabledEdit(): void
    {
        $component = new Component();
        $component->setModel('Acme\DemoBundle\Model\Demo');
        $component->setIdentifier(['2']);

        $action = new Action();

        $this->admin
            ->method('hasRoute')
            ->withConsecutive(
                ['edit'],
                ['show']
            )
            ->willReturnOnConsecutiveCalls(
                false,
                true
            );

        $this->admin->expects(static::once())
            ->method('isGranted')
            ->with(static::equalTo('SHOW'))
            ->willReturn(true);

        $this->admin->expects(static::once())
            ->method('generateObjectUrl')
            ->with(static::equalTo('show'), static::anything())
            ->willReturn('acme/demo/2/show');

        $this->admin->expects(static::once())
            ->method('toString')
            ->with(static::anything())
            ->willReturn('Text');

        static::assertSame('<a href="acme/demo/2/show">Text</a>', $this->twigExtension->generateLink($component, $action));
    }

    public function testGenerateLinkNoEditPermission(): void
    {
        $component = new Component();
        $component->setModel('Acme\DemoBundle\Model\Demo');
        $component->setIdentifier(['2']);

        $action = new Action();

        $this->admin
            ->method('hasRoute')
            ->withConsecutive(
                ['edit'],
                ['show']
            )
            ->willReturnOnConsecutiveCalls(
                true,
                true
            );

        $this->admin
            ->method('isGranted')
            ->withConsecutive(
                ['EDIT'],
                ['SHOW']
            )
            ->willReturnOnConsecutiveCalls(
                false,
                true
            );

        $this->admin->expects(static::once())
            ->method('generateObjectUrl')
            ->with(static::equalTo('show'), static::anything())
            ->willReturn('acme/demo/2/show');

        $this->admin->expects(static::once())
            ->method('toString')
            ->with(static::anything())
            ->willReturn('Text');

        static::assertSame('<a href="acme/demo/2/show">Text</a>', $this->twigExtension->generateLink($component, $action));
    }

    public function testGenerateLinkDisabledEditAndShow(): void
    {
        $component = new Component();
        $component->setModel('Acme\DemoBundle\Model\Demo');
        $component->setIdentifier('2');

        $action = new Action();

        $this->admin
            ->method('hasRoute')
            ->withConsecutive(
                ['edit'],
                ['show']
            )
            ->willReturnOnConsecutiveCalls(
                false,
                false
            );

        $this->admin->expects(static::once())
            ->method('toString')
            ->with(static::anything())
            ->willReturn('Text');

        static::assertSame('Text', $this->twigExtension->generateLink($component, $action));
    }
}
