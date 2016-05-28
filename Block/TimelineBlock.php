<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\TimelineBundle\Block;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\BlockBundle\Block\BaseBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Spy\Timeline\Driver\ActionManagerInterface;
use Spy\Timeline\Driver\TimelineManagerInterface;
use Spy\Timeline\Model\TimelineInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * @author     Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class TimelineBlock extends BaseBlockService
{
    /**
     * @var ActionManagerInterface
     */
    protected $actionManager;

    /**
     * @var TimelineManagerInterface
     */
    protected $timelineManager;

    /**
     * @var SecurityContextInterface
     */
    protected $securityContext;

    /**
     * @param string                   $name
     * @param EngineInterface          $templating
     * @param ActionManagerInterface   $actionManager
     * @param TimelineManagerInterface $timelineManager
     * @param SecurityContextInterface $securityContext
     */
    public function __construct($name, EngineInterface $templating, ActionManagerInterface $actionManager, TimelineManagerInterface $timelineManager, SecurityContextInterface $securityContext)
    {
        $this->actionManager = $actionManager;
        $this->timelineManager = $timelineManager;
        $this->securityContext = $securityContext;

        parent::__construct($name, $templating);
    }

    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $token = $this->securityContext->getToken();

        if (!$token) {
            return new Response();
        }

        $subject = $this->actionManager->findOrCreateComponent($token->getUser(), $token->getUser()->getId());

        $entries = $this->timelineManager->getTimeline($subject, array(
            'page' => 1,
            'max_per_page' => $blockContext->getSetting('max_per_page'),
            'type' => TimelineInterface::TYPE_TIMELINE,
            'context' => $blockContext->getSetting('context'),
            'filter' => $blockContext->getSetting('filter'),
            'group_by_action' => $blockContext->getSetting('group_by_action'),
            'paginate' => $blockContext->getSetting('paginate'),
        ));

        return $this->renderPrivateResponse($blockContext->getTemplate(), array(
            'context' => $blockContext,
            'settings' => $blockContext->getSettings(),
            'block' => $blockContext->getBlock(),
            'entries' => $entries,
        ), $response);
    }

    /**
     * {@inheritdoc}
     */
    public function buildEditForm(FormMapper $formMapper, BlockInterface $block)
    {
        $formMapper->add('settings', 'sonata_type_immutable_array', array(
            'keys' => array(
                array('title', 'text', array(
                    'required' => false,
                    'label' => 'form.label_title',
                )),
                array('icon', 'text', array(
                    'required' => false,
                )),
                array('max_per_page', 'integer', array(
                    'required' => true,
                    'label' => 'form.label_max_per_page',
                )),
            ),
            'translation_domain' => 'SonataTimelineBundle',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Timeline';
    }

    /**
     * {@inheritdoc}
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'max_per_page' => 10,
            'title' => 'Latest Actions',
            'icon' => '<i class="fa fa-clock-o fa-fw"></i>',
            'template' => 'SonataTimelineBundle:Block:timeline.html.twig',
            'context' => 'GLOBAL',
            'filter' => true,
            'group_by_action' => true,
            'paginate' => true,
        ));
    }
}
