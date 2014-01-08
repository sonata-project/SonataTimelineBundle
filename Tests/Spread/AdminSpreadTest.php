<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\TimelineBundle\Tests\Spread;

use Sonata\TimelineBundle\Spread\AdminSpread;
use Spy\Timeline\Model\Action;
use Spy\Timeline\Spread\Entry\EntryCollection;
use Symfony\Bridge\Doctrine\Test\DoctrineTestHelper;

/**
 * Class AdminSpreadTest
 *
 * This is a unit test class for \Sonata\TimelineBundle\Spread\AdminSpread
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class AdminSpreadTest extends \PHPUnit_Framework_TestCase
{
    /**
     * AdminSpread supported verbs
     *
     * @var array
     */
    protected $supportedVerbs = array(
        'sonata.admin.create',
        'sonata.admin.update',
        'sonata.admin.delete',
    );

    /**
     * Test supports() method with supported verbs and non-supported verbs
     */
    public function testSupportsMethod()
    {
        $registryMock = $this->getMock('\Symfony\Bridge\Doctrine\RegistryInterface');
        $spread = new AdminSpread($registryMock, '\Sonata\UserBundle\Entity\User');

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
     * Test process() method in order to test that collection is well completed
     */
    public function testProcessMethod()
    {
        $action = new Action();
        $action->setVerb('a.not.supported.verb');

        $spread = new AdminSpread($this->createRegistryMock(), 'FOS\UserBundle\Entity\User');

        $collection = new EntryCollection();
        $spread->process($action, $collection);

        $this->assertCount(2, $collection->getIterator(), 'Should return 2 users');

        foreach ($collection->getIterator() as $users) {
            foreach ($users as $entry) {
                $this->assertInstanceOf('Spy\Timeline\Spread\Entry\EntryUnaware', $entry, 'Should return an instance of EntryUnaware');
                $this->assertEquals('FOS\UserBundle\Entity\User', $entry->getSubjectModel());
            }
        }
    }

    /**
     * Returns a registry mock with some datas
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function createRegistryMock()
    {
        $users = array();

        for ($i = 1; $i < 3; $i++) {
            $user = $this->getMockBuilder('Sonata\UserBundle\Model\User')
                ->setMethods(array('getId'))
                ->getMockForAbstractClass();
            $user->expects($this->any())->method('getId')->will($this->returnValue($i));

            $users[] = array($user);
        }

        $query = $this->getMockBuilder('Doctrine\ORM\AbstractQuery')
            ->setMethods(array('iterate'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $query->expects($this->once())->method('iterate')->will($this->returnValue($users));

        $doctrineEm = DoctrineTestHelper::createTestEntityManager();

        $qb = $this->getMock('\Doctrine\ORM\QueryBuilder', array('getQuery'), array($doctrineEm));
        $qb->expects($this->once())->method('getQuery')->will($this->returnValue($query));

        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods(array('createQueryBuilder'))
            ->getMock();
        $em->expects($this->once())->method('createQueryBuilder')->will($this->returnValue($qb));

        $registryMock = $this->getMock('\Symfony\Bridge\Doctrine\RegistryInterface');
        $registryMock->expects($this->once())->method('getManager')->will($this->returnValue($em));

        return $registryMock;
    }
}
