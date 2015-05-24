<?php

namespace PPP\WikidataSparql;

use PPP\DataModel\AbstractNode;

/**
 * @license GNU GPL v2+
 * @author Bene* < benestar.wikimedia@gmail.com >
 * @author Thomas Pellissier Tanon
 */
class SparqlGenerator {

	private $prefixes = array(
		"PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>",
		"PREFIX skos: <http://www.w3.org/2004/02/skos/core#>",
		"PREFIX wikibase: <http://wikiba.se/ontology#>",
		"PREFIX hint: <http://www.bigdata.com/queryHints#>"
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
		$sparql = implode( "\n", $this->prefixes ) . "\n\n";

		$sparql .= 'SELECT DISTINCT ?result WHERE {';

		$sparql .= "\n\t" . 'hint:Query hint:optimizer "None" .' . "\n";

		$sparql .= str_replace( "\n", "\n\t", $this->generateWhereConditions( $node ) );

		return $sparql . "\n}";
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