<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\TimelineBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Admin\AdminInterface;
use Spy\Timeline\Driver\ActionManagerInterface;
use Spy\Timeline\Model\ComponentInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

class AdminExtension extends AbstractAdminExtension
{
    /**
     * @var ActionManagerInterface
     */
    protected $actionManager;

    /**
     * @var TokenStorageInterface|SecurityContextInterface
     */
    protected $securityContext;

    /**
     * NEXT_MAJOR: Go back to signature class check when bumping requirements to SF 2.6+.
     *
     * @param ActionManagerInterface                         $actionManager
     * @param TokenStorageInterface|SecurityContextInterface $tokenStorage
     */
    public function __construct(ActionManagerInterface $actionManager, $tokenStorage)
    {
        if (!$tokenStorage instanceof TokenStorageInterface && !$tokenStorage instanceof SecurityContextInterface) {
            throw new \InvalidArgumentException('Argument 2 should be an instance of Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface or Symfony\Component\Security\Core\SecurityContextInterface');
        }

        $this->actionManager = $actionManager;
        $this->securityContext = $tokenStorage;
    }

    /**
     * {@inheritdoc}
     */
    public function postUpdate(AdminInterface $admin, $object)
    {
        $this->create($this->getSubject(), 'sonata.admin.update', [
            'target' => $this->getTarget($admin, $object),
            'target_text' => $admin->toString($object),
            'admin_code' => $admin->getCode(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function postPersist(AdminInterface $admin, $object)
    {
        $this->create($this->getSubject(), 'sonata.admin.create', [
            'target' => $this->getTarget($admin, $object),
            'target_text' => $admin->toString($object),
            'admin_code' => $admin->getCode(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function preRemove(AdminInterface $admin, $object)
    {
        $this->create($this->getSubject(), 'sonata.admin.delete', [
            'target_text' => $admin->toString($object),
            'admin_code' => $admin->getCode(),
        ]);
    }

    /**
     * @return ComponentInterface
     */
    protected function getSubject()
    {
        return $this->actionManager->findOrCreateComponent($this->securityContext->getToken()->getUser());
    }

    /**
     * @param AdminInterface $admin
     * @param mixed          $object
     *
     * @return ComponentInterface
     */
    protected function getTarget(AdminInterface $admin, $object)
    {
        return $this->actionManager->findOrCreateComponent($admin->getClass(), $admin->id($object));
    }

    /**
     * @param ComponentInterface $subject
     * @param string             $verb
     * @param array              $components
     */
    protected function create(ComponentInterface $subject, $verb, $components = [])
    {
        $action = $this->actionManager->create($subject, $verb, $components);

        $this->actionManager->updateAction($action);
    }
}
