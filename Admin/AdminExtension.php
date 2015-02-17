<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\TimelineBundle\Admin;

use Sonata\AdminBundle\Admin\AdminExtension as BaseAdminExtension;
use Sonata\AdminBundle\Admin\AdminInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Spy\Timeline\Driver\ActionManagerInterface;
use Spy\Timeline\Model\ComponentInterface;

class AdminExtension extends BaseAdminExtension
{
    protected $actionManager;

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

    /**
     * {@inheritdoc}
     */
    public function postUpdate(AdminInterface $admin, $object)
    {
        $this->create($this->getSubject(), 'sonata.admin.update', array(
            'target' => $this->getTarget($admin, $object),
            'target_text' => $admin->toString($object),
            'admin_code' => $admin->getCode()
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
            'admin_code' => $admin->getCode()
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function preRemove(AdminInterface $admin, $object)
    {
        $this->create($this->getSubject(), 'sonata.admin.delete', array(
            'target_text' => $admin->toString($object),
            'admin_code' => $admin->getCode()
        ));
    }
}