<?php

namespace Mooc\Export;

use Mooc\Export\Visitor\XmlVisitor;
use Mooc\UI\BlockFactory;
use Mooc\UI\Courseware\Courseware;
use Mooc\Xml\Builder;

/**
 * Courseware XML export.
 *
 * @author Christian Flothmann <christian.flothmann@uos.de>
 */
class XmlExport implements ExportInterface
{
    /**
     * @var \Mooc\UI\BlockFactory
     */
    private $blockFactory;

    public function __construct(BlockFactory $blockFactory)
    {
        $this->blockFactory = $blockFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function export(Courseware $courseware)
    {
        $document = new \DOMDocument('1.0', 'UTF-8');
        $builder = new Builder($document);
        $visitor = new XmlVisitor($this->blockFactory, $builder);
        $visitor->startVisitingCourseware($courseware);

        return $document;
    }
}
