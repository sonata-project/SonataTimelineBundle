<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\TimelineBundle\Spread;

use FOS\UserBundle\Model\UserManagerInterface;
use Spy\Timeline\Model\ActionInterface;
use Spy\Timeline\Spread\Entry\EntryCollection;
use Spy\Timeline\Spread\SpreadInterface;
use Spy\Timeline\Spread\Entry\EntryUnaware;
use Symfony\Bridge\Doctrine\RegistryInterface;

class AdminSpread implements SpreadInterface
{
    protected $supportedVerbs = array(
        'sonata.admin.create',
        'sonata.admin.update',
        'sonata.admin.delete',
    );

    protected $supportedRoles = array(
        'ROLE_SUPER_ADMIN'
    );

    protected $registry;

    protected $userClass;

    /**
     * @param UserManagerInterface $userManager
     */
    public function __construct(RegistryInterface $registry, $userClass)
    {
        $this->registry = $registry;
        $this->userClass = $userClass;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ActionInterface $action)
    {
        return in_array($action->getVerb(), $this->supportedVerbs);
    }

    /**
     * {@inheritdoc}
     */
    public function process(ActionInterface $action, EntryCollection $coll)
    {
        $users = $this->getUsers();

        foreach ($users as $user) {
            $coll->add(new EntryUnaware($this->userClass, $user[0]->getId()), 'SONATA_ADMIN');
        }
    }

    /**
     * Returns corresponding users
     *
     * @return \Doctrine\ORM\Internal\Hydration\IterableResult
     */
    protected function getUsers()
    {
        $qb = $this->registry->getManager()->createQueryBuilder();

        $qb
            ->select('u')
            ->from($this->userClass, 'u')
            ->where($qb->expr()->like("u.roles", ':roles'))
            ->setParameter('roles', '%"ROLE_SUPER_ADMIN"%');

        return $qb->getQuery()->iterate();
    }
}