<?php

namespace PPP\WikidataSparql;

require_once(__DIR__ . '/../vendor/autoload.php');

$entryPoint = new WikidataSparqlEntryPoint( 'https://grammatical.backend.askplatyp.us/' );
$entryPoint->exec();
