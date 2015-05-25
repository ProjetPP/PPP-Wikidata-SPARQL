<?php

namespace PPP\WikidataSparql;

use InvalidArgumentException;
use PPP\DataModel\AbstractNode;
use PPP\DataModel\IntersectionNode;
use PPP\DataModel\MissingNode;
use PPP\DataModel\ResourceListNode;
use PPP\DataModel\ResourceNode;
use PPP\DataModel\StringResourceNode;
use PPP\DataModel\TripleNode;
use PPP\DataModel\UnionNode;
use PPP\Module\TreeSimplifier\NodeSimplifier;

/**
 * Do some actions for specific use case:
 * - if a predicate is not useful like "name" or "identity" cast subjects to wikibase items
 * - if the predicate is son or daughter use "child" with an intersection with the relevant sex
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 *
 *
 * TODO: duplicate of the same class in PPP-Wikidata
 */
class SpecificTripleNodeSimplifier implements NodeSimplifier {

	private static $MEANINGLESS_PREDICATES = array(
		'name',
		'identity',
		'definition'
	);

	/**
	 * @see NodeSimplifier::isSimplifierFor
	 */
	public function isSimplifierFor(AbstractNode $node) {
		return $node instanceof TripleNode &&
		$node->getSubject() instanceof ResourceListNode &&
		$node->getPredicate() instanceof ResourceListNode &&
		$node->getObject() instanceof MissingNode;
	}

	/**
	 * @see NodeSimplifier::doSimplification
	 */
	public function simplify(AbstractNode $node) {
		if(!$this->isSimplifierFor($node)) {
			throw new InvalidArgumentException('SpecificTripleNodeSimplifier can only clean TripleNode objects');
		}

		return $this->doSimplification($node);
	}

	public function doSimplification(TripleNode $node) {
		$additionalNodes = array();
		$otherPredicates = array();

		/** @var ResourceNode $predicate */
		foreach($node->getPredicate() as $predicate) {
			if(in_array($predicate->getValue(), self::$MEANINGLESS_PREDICATES)) {
				$additionalNodes[] = $node->getSubject();
			} else if($predicate->equals(new StringResourceNode('son'))) {
				$additionalNodes[] = $this->buildSonNode($node);
			} else if($predicate->equals(new StringResourceNode('daughter'))) {
				$additionalNodes[] = $this->buildDaughterNode($node);
			} else {
				$otherPredicates[] = $predicate;
			}
		}

		if(!empty($otherPredicates)) {
			$additionalNodes[] = new TripleNode($node->getSubject(), new ResourceListNode($otherPredicates), $node->getObject());
		}

		if(count($additionalNodes) === 1) {
			return $additionalNodes[0];
		}

		return new UnionNode($additionalNodes);
	}

	private function buildSonNode(TripleNode $node) {
		return new IntersectionNode(array(
			new TripleNode(
				$node->getSubject(),
				new ResourceListNode(array(new StringResourceNode( 'child', 'en' ))),
				$node->getObject()
			),
			new TripleNode(
				$node->getObject(),
				new ResourceListNode(array(new StringResourceNode( 'sex', 'en' ))),
				new ResourceListNode(array(new StringResourceNode( 'male', 'en' )))
			),
		));
	}

	private function buildDaughterNode(TripleNode $node) {
		return new IntersectionNode(array(
			new TripleNode(
				$node->getSubject(),
				new ResourceListNode(array(new StringResourceNode( 'child', 'en' ))),
				$node->getObject()
			),
			new TripleNode(
				$node->getObject(),
				new ResourceListNode(array(new StringResourceNode( 'sex', 'en' ))),
				new ResourceListNode(array(new StringResourceNode( 'female', 'en' )))
			),
		));
	}
}
