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
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\Service\AbstractAdminBlockService;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\CoreBundle\Form\Type\ImmutableArrayType;
use Spy\Timeline\Driver\ActionManagerInterface;
use Spy\Timeline\Driver\TimelineManagerInterface;
use Spy\Timeline\Model\TimelineInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class TimelineBlock extends AbstractAdminBlockService
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
     * @var TokenStorageInterface|SecurityContextInterface
     */
    protected $securityContext;

    /**
     * NEXT_MAJOR: Go back to signature class check when bumping requirements to SF 2.6+.
     *
     * @param string                                         $name
     * @param EngineInterface                                $templating
     * @param ActionManagerInterface                         $actionManager
     * @param TimelineManagerInterface                       $timelineManager
     * @param TokenStorageInterface|SecurityContextInterface $tokenStorage
     */
    public function __construct($name, EngineInterface $templating, ActionManagerInterface $actionManager, TimelineManagerInterface $timelineManager, $tokenStorage)
    {
        if (!$tokenStorage instanceof TokenStorageInterface && !$tokenStorage instanceof SecurityContextInterface) {
            throw new \InvalidArgumentException('Argument 5 should be an instance of Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface or Symfony\Component\Security\Core\SecurityContextInterface');
        }

        $this->actionManager = $actionManager;
        $this->timelineManager = $timelineManager;
        $this->securityContext = $tokenStorage;

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

        $entries = $this->timelineManager->getTimeline($subject, [
            'page' => 1,
            'max_per_page' => $blockContext->getSetting('max_per_page'),
            'type' => TimelineInterface::TYPE_TIMELINE,
            'context' => $blockContext->getSetting('context'),
            'filter' => $blockContext->getSetting('filter'),
            'group_by_action' => $blockContext->getSetting('group_by_action'),
            'paginate' => $blockContext->getSetting('paginate'),
        ]);

        return $this->renderPrivateResponse($blockContext->getTemplate(), [
            'context' => $blockContext,
            'settings' => $blockContext->getSettings(),
            'block' => $blockContext->getBlock(),
            'entries' => $entries,
        ], $response);
    }

    /**
     * {@inheritdoc}
     */
    public function buildEditForm(FormMapper $formMapper, BlockInterface $block)
    {
        $formMapper->add('settings', ImmutableArrayType::class, [
            'keys' => [
                ['title', TextType::class, [
                    'required' => false,
                    'label' => 'form.label_title',
                ]],
                ['icon', TextType::class, [
                    'required' => false,
                ]],
                ['max_per_page', IntegerType::class, [
                    'required' => true,
                    'label' => 'form.label_max_per_page',
                ]],
            ],
            'translation_domain' => 'SonataTimelineBundle',
        ]);
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
        $resolver->setDefaults([
            'max_per_page' => 10,
            'title' => 'Latest Actions',
            'icon' => '<i class="fa fa-clock-o fa-fw"></i>',
            'template' => 'SonataTimelineBundle:Block:timeline.html.twig',
            'context' => 'GLOBAL',
            'filter' => true,
            'group_by_action' => true,
            'paginate' => true,
        ]);
    }
}
