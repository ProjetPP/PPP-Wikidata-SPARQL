<?php

namespace PPP\WikidataSparql;

use PPP\DataModel\AbstractNode;

interface ConditionGenerator {

	/**
	 * @param AbstractNode $node
	 * @param string $variableName
	 *
	 * @return string
	 */
	public function generateCondition( AbstractNode $node, $variableName );

	/**
	 * @return string
	 */
	public function getType();

}
