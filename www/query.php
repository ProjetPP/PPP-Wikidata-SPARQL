<?php

namespace PPP\WikidataSparql;

require_once(__DIR__ . '/../vendor/autoload.php');

$entryPoint = new WikidataSparqlEntryPoint( 'http://grammatical.backend.askplatyp.us/' );
$entryPoint->exec();
