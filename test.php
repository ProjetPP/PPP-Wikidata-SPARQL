<?php

require 'vendor/autoload.php';

use PPP\DataModel\MissingNode;
use PPP\DataModel\ResourceListNode;
use PPP\DataModel\StringResourceNode;
use PPP\DataModel\TripleNode;
use PPP\WikidataSparql\ConditionGeneratorFactory;
use PPP\WikidataSparql\SparqlGenerator;

$sparqlGenerator = new SparqlGenerator( new ConditionGeneratorFactory( 'en' ) );

$node = new ResourceListNode( array(
	new StringResourceNode( 'test' )
) );

$node2 = new TripleNode(
	new ResourceListNode( array( new StringResourceNode( 'United States' ) ) ),
	new ResourceListNode( array( new StringResourceNode( 'president' ) ) ),
	new MissingNode()
);

$node3 = new TripleNode(
	$node2,
	new ResourceListNode( array( new StringResourceNode( 'birth date' ) ) ),
	new MissingNode()
);

echo $sparqlGenerator->generateSparql( $node2 );
