<?php

namespace PPP\WikidataSparql;

use PPP\DataModel\AbstractNode;

class SparqlGenerator {

	private $prefixes = array(
		"PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>",
		"PREFIX skos: <http://www.w3.org/2004/02/skos/core#>",
		"PREFIX wikibase: <http://wikiba.se/ontology#>",
		"PREFIX wdt: <http://www.wikidata.org/prop/direct/>"
	);

	/**
	 * @var ConditionGeneratorFactory
	 */
	private $conditionGeneratorFactory;

	public function __construct(
		ConditionGeneratorFactory $conditionGeneratorFactory
	) {
		$this->conditionGeneratorFactory = $conditionGeneratorFactory;
 	}

	/**
	 * @param AbstractNode $node
	 * @return string
	 */
	public function generateSparql( AbstractNode $node ) {
		$sparql = implode( "\n", $this->prefixes ) . "\n";

		$sparql .= 'SELECT DISTINCT ?result WHERE {' . "\n\t";

		$sparql .= $this->generateWhereConditions( $node ) . "\n";

		return $sparql . '}';
	}

	/**
	 * @param AbstractNode $node
	 * @return string
	 */
	private function generateWhereConditions( AbstractNode $node ) {
		$generator = $this->conditionGeneratorFactory->getConditionGenerator( $node );
		return $generator->generateCondition( $node, '?result' );
	}

}