<?php

namespace Mooc\Xml;

/**
 * Modify XML documents.
 *
 * @author Christian Flothmann <christian.flothmann@uos.de>
 */
interface BuilderInterface
{

    /**
     * Enters a new DOM node (i. e. making it the current node to work on).
     *
     * @param \DOMNode $node The node to enter
     */
    public function enterNode(\DOMNode $node);

    /**
     * Leaves the current node.
     *
     * @throws \RuntimeException if the current node has no parent node
     */
    public function leaveNode();

    /**
     * Introduce a new XML namespace with an optional namespace alias.
     *
     * @param string $namespace      The full XML namespace
     * @param string $schemaLocation The url under which the XML schema definition
     *                               file is located
     * @param string $alias          An optional namespace alias (must be given
     *                               to be able to use multiple namespaces in
     *                               a single file)
     */
    public function addNamespace($namespace, $schemaLocation, $alias = null);

    /**
     * Appends a new block node to the current node.
     *
     * @param string $elementName The element name
     * @param string $title       The block title
     * @param array  $attributes  Element attributes
     *
     * @return \DOMElement The new element
     */
    public function appendBlockNode($elementName, $title = null, array $attributes = array());

    /**
     * Creates a new DOM XML attribute.
     *
     * @param string $name  Attribute name
     * @param string $value Attribute value
     *
     * @return \DOMAttr Attribute node
     */
    public function createAttributeNode($name, $value);
}
