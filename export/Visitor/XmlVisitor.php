<?php

namespace Mooc\Export\Visitor;

use Mooc\DB\Block;
use Mooc\UI\BlockFactory;
use Mooc\UI\Courseware\Courseware;
use Mooc\UI\Section\Section;
use Mooc\Xml\Builder;

/**
 * XML courseware visitor.
 *
 * @author Christian Flothmann <christian.flothmann@uos.de>
 */
class XmlVisitor extends AbstractVisitor
{
    /**
     * @var \Mooc\UI\BlockFactory
     */
    private $blockFactory;

    /**
     * @var \Mooc\Xml\Builder
     */
    private $builder;

    public function __construct(BlockFactory $blockFactory, Builder $builder)
    {
        $this->blockFactory = $blockFactory;
        $this->builder = $builder;
    }

    /**
     * {@inheritdoc}
     */
    public function startVisitingCourseware(Courseware $courseware)
    {
        $this->builder->enterNode($this->builder->appendBlockNode('courseware', $courseware->title));

        $this->builder->addNamespace(
            'http://moocip.de/schema/courseware/',
            'http://moocip.de/schema/courseware/courseware-1.0.xsd'
        );
        $this->builder->addNamespace(
            'http://www.w3.org/2001/XMLSchema-instance',
            null,
            'xsi'
        );

        foreach ($courseware->getModel()->children as $chapter) {
            $this->startVisitingChapter($chapter);
            $this->endVisitingChapter($chapter);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function endVisitingCourseware(Courseware $courseware)
    {
        $this->builder->leaveNode();
    }

    /**
     * {@inheritdoc}
     */
    public function startVisitingChapter(Block $chapter)
    {
        $this->builder->enterNode($this->builder->appendBlockNode('chapter', $chapter->title));

        foreach ($chapter->children as $chapter) {
            $this->startVisitingSubChapter($chapter);
            $this->endVisitingSubChapter($chapter);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function endVisitingChapter(Block $chapter)
    {
        $this->builder->leaveNode();
    }

    /**
     * {@inheritdoc}
     */
    public function startVisitingSubChapter(Block $subChapter)
    {
        $this->builder->enterNode($this->builder->appendBlockNode('subchapter', $subChapter->title));

        foreach ($subChapter->children as $block) {
            $section = $this->blockFactory->makeBlock($block);
            $this->startVisitingSection($section);
            $this->endVisitingSection($section);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function endVisitingSubChapter(Block $subChapter)
    {
        $this->builder->leaveNode();
    }

    /**
     * {@inheritdoc}
     */
    public function startVisitingSection(Section $section)
    {
        $this->builder->enterNode($this->builder->appendBlockNode('section', $section->title, array(
            $this->builder->createAttributeNode('icon', $section->icon),
        )));

        foreach ($section->getModel()->children as $block) {
            $uiBlock = $this->blockFactory->makeBlock($block);
            $this->startVisitingBlock($uiBlock);
            $this->endVisitingBlock($uiBlock);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function endVisitingSection(Section $section)
    {
        $this->builder->leaveNode();
    }

    /**
     * {@inheritdoc}
     */
    public function startVisitingBlock(\Mooc\UI\Block $block)
    {
        $alias = null;
        $namespace = $block->getXmlNamespace();
        $schemaLocation = $block->getXmlSchemaLocation();

        if ($namespace !== null && $schemaLocation !== null) {
            $alias = strtolower(get_class($block));

            if (preg_match('/\\\\(\w+)$/', $alias, $matches)) {
                $alias = $matches[1];
            }

            if (substr($alias, -5) === 'block') {
                $alias = substr($alias, 0, strlen($alias) - 5);
            }

            $this->builder->addNamespace($namespace, $schemaLocation, $alias);
        }

        $properties = $block->export();
        $attributes = array();

        foreach ($properties as $name => $value) {
            if ($alias !== null) {
                $name = $alias.':'.$name;
            }

            $attributes[] = $this->builder->createAttributeNode($name, $value);
        }

        $this->builder->appendBlockNode('block', $block->title, $attributes);
    }
}
