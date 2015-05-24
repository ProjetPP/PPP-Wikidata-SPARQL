<?php

namespace PPP\WikidataSparql;

use PPP\DataModel\AbstractNode;

class ConditionGeneratorFactory {

	/**
	 * @var ConditioGenerator[]
	 */
	private $conditionGenerators;

	/**
	 * @param string $languageCode
	 */
	public function __construct( $languageCode ) {
		$this->registerConditionGenerator( new ResourceListNodeConditionGenerator( $languageCode ) );
		$this->registerConditionGenerator( new MissingNodeConditionGenerator() );
		$this->registerConditionGenerator( new TripleNodeConditionGenerator( $this, new VariableProvider() ) );
	}

	/**
	 * @param ConditioGenerator $generator
	 */
	public function registerConditionGenerator( ConditionGenerator $generator ) {
		$this->conditionGenerators[$generator->getType()] = $generator;
	}

	/**
	 * @param AbstractNode $node
	 * @return ConditioGenerator
	 */
	public function getConditionGenerator( AbstractNode $node ) {
		if ( !isset( $this->conditionGenerators[$node->getType()] ) ) {
			throw new OutOfBoundsException( 'No condition generator has been defined for type ' . $node->getType() );
		}

		return $this->conditionGenerators[$node->getType()];
	}

}
