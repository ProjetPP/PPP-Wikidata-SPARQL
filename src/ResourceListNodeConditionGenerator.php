<?php

namespace PPP\WikidataSparql;

use InvalidArgumentException;
use PPP\DataModel\AbstractNode;
use PPP\DataModel\ResourceListNode;
use PPP\DataModel\ResourceNode;

/**
 * @license GNU GPL v2+
 * @author Bene* < benestar.wikimedia@gmail.com >
 * @author Thomas Pellissier Tanon
 */
class ResourceListNodeConditionGenerator implements ConditionGenerator {

	/**
	 * @var string
	 */
	private $languageCode;

	/**
	 * @param string $languageCode
	 */
	public function __construct( $languageCode ) {
		$this->languageCode = $languageCode;
	}

	/**
	 * @see ConditionGenerator
	 */
	public function generateCondition( AbstractNode $node, $variableName ) {
		if ( !( $node instanceof ResourceListNode ) ) {
			throw new InvalidArgumentException();
		}

		return "\n{" . implode( "\n} UNION {", array_map( function( ResourceNode $resourceNode ) use ( $variableName ) {
			return str_replace( "\n", "\n\t", "\n" . $this->generateStringCondition( $resourceNode, $variableName ) );
		}, $node->toArray() ) ) . "\n}";
	}

	private function generateStringCondition( ResourceNode $resourceNode, $variableName ) {
		// only StringResourceNodes are supposed to occur here
		$value = addslashes( $resourceNode->getValue() );
		return '{ ' . $variableName . ' rdfs:label "' . $value . '"@' . $this->languageCode . ' . }' .
			' UNION { ' . $variableName . ' skos:altLabel "' . $value . '"@' . $this->languageCode . ' . }';
	}

	/**
	 * @see ConditionGenerator
	 */
	public function getType() {
		return 'list';
	}

}
