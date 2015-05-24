<?php

namespace PPP\WikidataSparql;

use PPP\DataModel\AbstractNode;
use PPP\DataModel\ResourceListNode;
use PPP\DataModel\ResourceNode;

class ResourceListNodeConditionGenerator implements ConditionGenerator {

	/**
	 * @var string
	 */
	private $languageCode;

	public function __construct( $languageCode ) {
		$this->languageCode = $languageCode;
	}

	public function generateCondition( AbstractNode $node, $variableName ) {
		if ( !( $node instanceof ResourceListNode ) ) {
			throw new InvalidArgumentException();
		}

		return '{ ' . implode( ' UNION ', array_map( function( ResourceNode $resourceNode ) use ( $variableName ) {
			return $this->generateStringCondition( $resourceNode, $variableName );
		}, $node->toArray() ) ) . ' }' . " .\n\t";
	}

	private function generateStringCondition( ResourceNode $resourceNode, $variableName ) {
		// only StringResourceNodes are supposed to occur here
		$value = addslashes( $resourceNode->getValue() );
		return ' { ' . $variableName . ' rdfs:label "' . $value . '"@' . $this->languageCode . ' . }' .
			' UNION { ' . $variableName . ' skos:altLabel "' . $value . '"@' . $this->languageCode . ' . }';
	}

	public function getType() {
		return 'list';
	}

}
