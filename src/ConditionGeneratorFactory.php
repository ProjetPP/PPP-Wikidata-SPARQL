<?php

namespace PPP\WikidataSparql;

use OutOfBoundsException;
use PPP\DataModel\AbstractNode;

/**
 * @license GNU GPL v2+
 * @author Bene* < benestar.wikimedia@gmail.com >
 * @author Thomas Pellissier Tanon
 */
class ConditionGeneratorFactory {

	/**
	 * @var ConditionGenerator[]
	 */
	private $conditionGenerators;

	/**
	 * @var string
	 */
	private $languageCode;

	/**
	 * @param string $languageCode
	 */
	public function __construct( $languageCode ) {
		$this->languageCode = $languageCode;

		$this->registerConditionGenerator( new ResourceListNodeConditionGenerator( $languageCode ) );
		$this->registerConditionGenerator( new TripleNodeConditionGenerator( $this, new VariableProvider() ) );
		$this->registerConditionGenerator( new UnionNodeConditionGenerator( $this ) );
		$this->registerConditionGenerator( new IntersectionNodeConditionGenerator( $this ) );
	}

	/**
	 * @return string
	 */
	public function getLanguageCode() {
		return $this->languageCode;
	}

	/**
	 * @param ConditionGenerator $generator
	 */
	public function registerConditionGenerator( ConditionGenerator $generator ) {
		$this->conditionGenerators[$generator->getType()] = $generator;
	}

	/**
	 * @param AbstractNode $node
	 * @return ConditionGenerator
	 */
	public function getConditionGenerator( AbstractNode $node ) {
		if ( !isset( $this->conditionGenerators[$node->getType()] ) ) {
			throw new OutOfBoundsException( 'No condition generator has been defined for type ' . $node->getType() );
		}

		return $this->conditionGenerators[$node->getType()];
	}

}
