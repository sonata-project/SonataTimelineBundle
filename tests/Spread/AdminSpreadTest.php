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

namespace Sonata\TimelineBundle\Tests\Spread;

use PHPUnit\Framework\TestCase;
use Sonata\TimelineBundle\Spread\AdminSpread;
use Spy\Timeline\Model\Action;
use Spy\Timeline\Spread\Entry\EntryCollection;

/**
 * Class FakeUserEntity.
 *
 * This is a fake entity class
 */
class FakeUserEntity
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @param $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}

/**
 * Class AdminSpreadTest.
 *
 * This is a unit test class for \Sonata\TimelineBundle\Spread\AdminSpread
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class AdminSpreadTest extends TestCase
{
    /**
     * AdminSpread supported verbs.
     *
     * @var array
     */
    protected $supportedVerbs = [
        'sonata.admin.create',
        'sonata.admin.update',
        'sonata.admin.delete',
    ];

    /**
     * Test supports() method with supported verbs and non-supported verbs.
     */
    public function testSupportsMethod(): void
    {
        $registryMock = $this->createMock('\Symfony\Bridge\Doctrine\RegistryInterface');
        $spread = new AdminSpread($registryMock, '\Sonata\TimelineBundle\Tests\Spread\FakeUserEntity');

        // Test non-supported verbs
        $action = new Action();
        $action->setVerb('a.not.supported.verb');

        $this->assertFalse($spread->supports($action), '"a.not.supported.verb" should not be supported');

        // Test supported verbs
        foreach ($this->supportedVerbs as $supportedVerb) {
            $action = new Action();
            $action->setVerb($supportedVerb);

            $this->assertTrue($spread->supports($action), sprintf('Verb "%s" should be supported', $action->getVerb()));
        }
    }

    /**
     * Test process() method in order to test that collection is well completed.
     */
    public function testProcessMethod(): void
    {
        $action = new Action();
        $action->setVerb('a.not.supported.verb');

        $spread = $this->getMockBuilder('\Sonata\TimelineBundle\Spread\AdminSpread')
            ->disableOriginalConstructor()
            ->setMethods(['getUsers'])
            ->getMock();
        $spread->expects($this->any())->method('getUsers')->will($this->returnValue($this->getFakeUsers()));

        $collection = new EntryCollection();
        $spread->process($action, $collection);

        $this->assertCount(2, $collection->getIterator(), 'Should return 2');

        $usersCount = 0;

        foreach ($collection->getIterator() as $users) {
            foreach ($users as $entry) {
                ++$usersCount;

                $this->assertInstanceOf('Spy\Timeline\Spread\Entry\EntryUnaware', $entry, 'Should return an instance of EntryUnaware');
                $this->assertSame('Sonata\TimelineBundle\Tests\Spread\FakeUserEntity', $entry->getSubjectModel());
            }
        }

        $this->assertEquals(5, $usersCount / 2, 'Should return 5 users for 2 iterations');
    }

    /**
     * Returns fake users.
     *
     * @return array
     */
    protected function getFakeUsers()
    {
        $users = [];

        for ($i = 1; $i < 6; ++$i) {
            $user = new FakeUserEntity();
            $user->setId($i);

            $users[] = [$user];
        }

        return $users;
    }
}
