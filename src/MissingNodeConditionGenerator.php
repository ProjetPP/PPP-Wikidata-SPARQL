<?php

namespace PPP\WikidataSparql;

use PPP\DataModel\AbstractNode;
use PPP\DataModel\MissingNode;

/**
 * @license GNU GPL v2+
 * @author Bene* < benestar.wikimedia@gmail.com >
 * @author Thomas Pellissier Tanon
 */
class MissingNodeConditionGenerator implements ConditionGenerator {

	/**
	 * @see ConditionGenerator
	 */
	public function generateCondition( AbstractNode $node, $variableName ) {
		if ( !( $node instanceof MissingNode ) ) {
			throw new InvalidArgumentException();
		}

		return $variableName;
	}

	/**
	 * @see ConditionGenerator
	 */
	public function getType() {
		return 'missing';
	}

}
