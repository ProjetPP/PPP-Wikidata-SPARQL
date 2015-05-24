<?php

namespace PPP\WikidataSparql;

use PPP\DataModel\AbstractNode;

/**
 * @license GNU GPL v2+
 * @author Bene* < benestar.wikimedia@gmail.com >
 * @author Thomas Pellissier Tanon
 */
interface ConditionGenerator {

	/**
	 * Creates the SPARQL condition for the given node.
	 *
	 * @param AbstractNode $node
	 * @param string $variableName
	 * @return string
	 */
	public function generateCondition( AbstractNode $node, $variableName );

	/**
	 * Returns the type of nodes this generator supports.
	 *
	 * @return string
	 */
	public function getType();

}
