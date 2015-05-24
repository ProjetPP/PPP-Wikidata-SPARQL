<?php

namespace PPP\WikidataSparql;

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
