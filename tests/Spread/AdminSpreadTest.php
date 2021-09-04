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

use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use Sonata\TimelineBundle\Spread\AdminSpread;
use Spy\Timeline\Model\Action;
use Spy\Timeline\Spread\Entry\EntryCollection;
use Spy\Timeline\Spread\Entry\EntryInterface;
use Spy\Timeline\Spread\Entry\EntryUnaware;

class FakeUserEntity
{
    /**
     * @var int
     */
    protected $id;

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }
}

/**
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class AdminSpreadTest extends TestCase
{
    /**
     * @var array
     */
    protected $supportedVerbs = [
        'sonata.admin.create',
        'sonata.admin.update',
        'sonata.admin.delete',
    ];

    public function testSupportsMethod(): void
    {
        $registryMock = $this->createMock(ManagerRegistry::class);
        $spread = new AdminSpread($registryMock, FakeUserEntity::class);

        // Test non-supported verbs
        $action = new Action();
        $action->setVerb('a.not.supported.verb');

        static::assertFalse($spread->supports($action), '"a.not.supported.verb" should not be supported');

        // Test supported verbs
        foreach ($this->supportedVerbs as $supportedVerb) {
            $action = new Action();
            $action->setVerb($supportedVerb);

            static::assertTrue($spread->supports($action), sprintf('Verb "%s" should be supported', $action->getVerb()));
        }
    }

    public function testProcessMethod(): void
    {
        $action = new Action();
        $action->setVerb('a.not.supported.verb');

        $spread = $this->getMockBuilder(AdminSpread::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUsers'])
            ->getMock();
        $spread->method('getUsers')->willReturn($this->getFakeUsers());

        $collection = new EntryCollection();
        $spread->process($action, $collection);

        static::assertCount(2, $collection->getIterator(), 'Should return 2');

        $usersCount = 0;

        /** @var EntryInterface[] $users */
        foreach ($collection->getIterator() as $users) {
            foreach ($users as $entry) {
                ++$usersCount;

                static::assertInstanceOf(EntryUnaware::class, $entry, 'Should return an instance of EntryUnaware');
                static::assertSame(FakeUserEntity::class, $entry->getSubjectModel());
            }
        }

        static::assertSame(5, $usersCount / 2, 'Should return 5 users for 2 iterations');
    }

    protected function getFakeUsers(): array
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
