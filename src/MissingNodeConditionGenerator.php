<?php

namespace PPP\WikidataSparql;

use PPP\DataModel\AbstractNode;
use PPP\DataModel\MissingNode;

class MissingNodeConditionGenerator implements ConditionGenerator {

	public function generateCondition( AbstractNode $node, $variableName ) {
		if ( !( $node instanceof MissingNode ) ) {
			throw new InvalidArgumentException();
		}

		return $variableName;
	}

	public function getType() {
		return 'missing';
	}

}
