<?php

namespace Mooc\Xml;

/**
 * An XML builder working on a {@link \DOMDocument} instance.
 *
 * @author Christian Flothmann <christian.flothmann@uos.de>
 */
class Builder implements BuilderInterface
{
    /**
     * @var \DOMDocument The document to modify
     */
    private $document;

    /**
     * @var \DOMNode[]
     */
    private $nodeStack = array();

    /**
     * @var \DOMNode
     */
    private $currentNode;

    public function __construct(\DOMDocument $document)
    {
        $this->document = $document;
        $this->enterNode($document);
    }

    /**
     * {@inheritdoc}
     */
    public function enterNode(\DOMNode $node)
    {
        // put current node on the backtracking stack
        if ($this->currentNode !== null) {
            $this->nodeStack[] = $this->currentNode;
        }

        $this->currentNode = $node;
    }

    /**
     * {@inheritdoc}
     */
    public function leaveNode()
    {
        if (count($this->nodeStack) == 0) {
            throw new \RuntimeException('Cannot leave the root node');
        }

        $this->currentNode = array_pop($this->nodeStack);
    }

    /**
     * {@inheritdoc}
     */
    public function addNamespace($namespace, $schemaLocation, $alias = null)
    {
        if ($alias === null) {
            $namespaceNode = $this->createAttributeNode('xmlns', $namespace);
        } else {
            $namespaceNode = $this->createAttributeNode('xmlns:'.$alias, $namespace);
        }

        $rootNode =  $this->document->documentElement;
        $rootNode->appendChild($namespaceNode);

        if ($schemaLocation === null) {
            return;
        }

        if ($rootNode->hasAttribute('xsi:schemaLocation')) {
            $attributeNode = $rootNode->getAttributeNode('xsi:schemaLocation');
        } else {
            $attributeNode = $this->createAttributeNode('xsi:schemaLocation', '');
        }

        $attributeNode->value = trim($attributeNode->value).' '.$namespace.' '.$schemaLocation;

        $this->document->documentElement->appendChild($attributeNode);
    }

    /**
     * {@inheritdoc}
     */
    public function appendBlockNode($elementName, $title = null, array $attributes = array())
    {
        $element = $this->document->createElement($elementName);

        if ($title !== null) {
            $element->appendChild($this->createAttributeNode('title', $title));
        }

        foreach ($attributes as $attribute) {
            $element->appendChild($attribute);
        }

        $this->currentNode->appendChild($element);

        return $element;
    }

    /**
     * {@inheritdoc}
     */
    public function createAttributeNode($name, $value)
    {
        $attribute = $this->document->createAttribute($name);
        $attribute->value = utf8_encode($value);

        return $attribute;
    }
}
