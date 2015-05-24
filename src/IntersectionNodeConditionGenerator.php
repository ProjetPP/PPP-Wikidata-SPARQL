<?php

namespace PPP\WikidataSparql;

use InvalidArgumentException;
use PPP\DataModel\AbstractNode;
use PPP\DataModel\IntersectionNode;

class IntersectionNodeConditionGenerator implements ConditionGenerator {

	/**
	 * @var ConditionGeneratorFactory
	 */
	private $conditionGeneratorFactory;

	public function __construct(
		ConditionGeneratorFactory $conditionGeneratorFactory
	) {
		$this->conditionGeneratorFactory = $conditionGeneratorFactory;
	}

	public function generateCondition( AbstractNode $node, $variableName ) {
		if ( !( $node instanceof IntersectionNode ) ) {
			throw new InvalidArgumentException();
		}

		return implode( '', array_map( function( AbstractNode $subNode ) use ( $variableName ) {
			return $this->conditionGeneratorFactory->getConditionGenerator( $subNode )->generateCondition( $subNode, $variableName );
		}, $node->getOperands() ) );
	}

	public function getType() {
		return 'intersection';
	}

}
