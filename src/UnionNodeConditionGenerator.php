<?php

namespace PPP\WikidataSparql;

use InvalidArgumentException;
use PPP\DataModel\AbstractNode;
use PPP\DataModel\UnionNode;

/**
 * @license GNU GPL v2+
 * @author Bene* < benestar.wikimedia@gmail.com >
 * @author Thomas Pellissier Tanon
 */
class UnionNodeConditionGenerator implements ConditionGenerator {

	/**
	 * @var ConditionGeneratorFactory
	 */
	private $conditionGeneratorFactory;

	public function __construct( ConditionGeneratorFactory $conditionGeneratorFactory ) {
		$this->conditionGeneratorFactory = $conditionGeneratorFactory;
	}

	/**
	 * @see ConditionGenerator
	 */
	public function generateCondition( AbstractNode $node, $variableName ) {
		if ( !( $node instanceof UnionNode ) ) {
			throw new InvalidArgumentException();
		}

		return '{ ' . implode( ' UNION ', array_map( function( AbstractNode $subNode ) use ( $variableName ) {
			return $this->conditionGeneratorFactory->getConditionGenerator( $subNode )->generateCondition( $subNode, $variableName );
		}, $node->getOperands() ) ) . ' }' . " .\n\t";
	}

	/**
	 * @see ConditionGenerator
	 */
	public function getType() {
		return 'union';
	}

}
