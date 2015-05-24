<?php

namespace PPP\WikidataSparql;

use InvalidArgumentException;
use PPP\DataModel\AbstractNode;
use PPP\DataModel\UnionNode;

class UnionNodeConditionGenerator implements ConditionGenerator {

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
		if ( !( $node instanceof UnionNode ) ) {
			throw new InvalidArgumentException();
		}

		return '{ ' . implode( ' UNION ', array_map( function( AbstractNode $subNode ) use ( $variableName ) {
			return $this->conditionGeneratorFactory->getConditionGenerator( $subNode )->generateCondition( $subNode, $variableName );
		}, $node->getOperands() ) ) . ' }' . " .\n\t";
	}

	public function getType() {
		return 'union';
	}

}
