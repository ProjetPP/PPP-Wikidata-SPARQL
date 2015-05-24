<?php

namespace PPP\WikidataSparql;

/**
 * @license GNU GPL v2+
 * @author Bene* < benestar.wikimedia@gmail.com >
 * @author Thomas Pellissier Tanon
 */
class VariableProvider {

	/**
	 * @var int
	 */
	private $counter = 0;

	/** 
	 * @param string $prefix
	 * @return string
	 */
	public function getNewVariable( $prefix = 'x' ) {
		return '?' . $prefix . ( $this->counter ++ );
	}

}
