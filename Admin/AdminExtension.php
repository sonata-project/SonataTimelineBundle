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
use Symfony\Component\Security\Core\SecurityContextInterface;

class AdminExtension extends AbstractAdminExtension
{
    /**
     * @var ActionManagerInterface
     */
    protected $actionManager;

    /**
     * @var SecurityContextInterface
     */
    protected $securityContext;

    /**
     * @param ActionManagerInterface   $actionManager
     * @param SecurityContextInterface $securityContext
     */
    public function __construct(ActionManagerInterface $actionManager, SecurityContextInterface $securityContext)
    {
        $this->actionManager = $actionManager;
        $this->securityContext = $securityContext;
    }

    /**
     * {@inheritdoc}
     */
    public function postUpdate(AdminInterface $admin, $object)
    {
        $this->create($this->getSubject(), 'sonata.admin.update', array(
            'target' => $this->getTarget($admin, $object),
            'target_text' => $admin->toString($object),
            'admin_code' => $admin->getCode(),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function postPersist(AdminInterface $admin, $object)
    {
        $this->create($this->getSubject(), 'sonata.admin.create', array(
            'target' => $this->getTarget($admin, $object),
            'target_text' => $admin->toString($object),
            'admin_code' => $admin->getCode(),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function preRemove(AdminInterface $admin, $object)
    {
        $this->create($this->getSubject(), 'sonata.admin.delete', array(
            'target_text' => $admin->toString($object),
            'admin_code' => $admin->getCode(),
        ));
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
    protected function create(ComponentInterface $subject, $verb, $components = array())
    {
        $action = $this->actionManager->create($subject, $verb, $components);

        $this->actionManager->updateAction($action);
    }
}
