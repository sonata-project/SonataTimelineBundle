<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\TimelineBundle\Entity;

use Spy\TimelineBundle\Entity\Component as BaseComponent;

/**
 * Component entity for Doctrine ORM.
 *
 * @uses BaseComponent
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class Component extends BaseComponent
{
	/**
	 * @var string
	 */
	protected $adminCode;
	
	
	/**
	 * Set adminCode
	 *
	 * @param string $adminCode
	 * @return Component
	 */
	public function setAdminCode($adminCode)
	{
		$this->adminCode = $adminCode;
	
		return $this;
	}
	
	/**
	 * Get adminCode
	 *
	 * @return string
	 */
	public function getAdminCode()
	{
		return $this->adminCode;
	}
}
