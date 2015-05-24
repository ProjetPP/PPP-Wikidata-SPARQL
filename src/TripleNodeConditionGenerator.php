<?php

namespace PPP\WikidataSparql;

use PPP\DataModel\AbstractNode;
use PPP\DataModel\TripleNode;

class TripleNodeConditionGenerator implements ConditionGenerator {

	/**
	 * @var ConditionGeneratorFactory
	 */
	private $conditionGeneratorFactory;

	/**
	 * @var VariableProvider
	 */
	private $variableProvider;

	public function __construct(
		ConditionGeneratorFactory $conditionGeneratorFactory,
		VariableProvider $variableProvider
	) {
		$this->conditionGeneratorFactory = $conditionGeneratorFactory;
		$this->variableProvider = $variableProvider;
	}

	public function generateCondition( AbstractNode $node, $variableName ) {
		if ( !( $node instanceof TripleNode ) ) {
			throw new InvalidArgumentException();
		}

		list( $subjectCondition, $subjectVariable ) = $this->formatAbstractNode( $node->getSubject(), $variableName, 'subject' );
		list( $predicateCondition, $predicateVariable ) = $this->formatAbstractNode( $node->getPredicate(), $variableName, 'predicate' );
		list( $objectCondition, $objectVariable ) = $this->formatAbstractNode( $node->getObject(), $variableName, 'object' );

		$directPredicateVariable = $this->variableProvider->getNewVariable( 'directPredicate' );

		return $subjectCondition . $predicateCondition . $objectCondition .
			// TODO currently broken on test instance
			// $subjectVariable . ' a wikibase:Item' . " .\n\t" .
			$predicateVariable . ' a wikibase:Property' . " .\n\t" .
			$predicateVariable . ' wikibase:directClaim ' . $directPredicateVariable . " .\n\t" . 
			$subjectVariable . ' ' . $directPredicateVariable . ' ' . $objectVariable . " .\n\t";
	}

	private function formatAbstractNode( AbstractNode $node, $variableName, $prefix ) {
		if ( $node->getType() === 'missing' ) {
			return array( null, $variableName );
		}

		$generator = $this->conditionGeneratorFactory->getConditionGenerator( $node );
		$newVariableName = $this->variableProvider->getNewVariable( $prefix );
		return array( $generator->generateCondition( $node, $newVariableName ), $newVariableName );
	}

	public function getType() {
		return 'triple';
	}

}
