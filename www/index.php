<?php

namespace PPP\WikidataSparql;

require_once(__DIR__ . '/../vendor/autoload.php');

$entryPoint = new WikidataSparqlEntryPoint( 'http://askplatyp.us:9002/' );
$entryPoint->exec();
