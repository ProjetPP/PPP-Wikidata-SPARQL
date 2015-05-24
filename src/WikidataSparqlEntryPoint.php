<?php

namespace PPP\WikidataSparql;

use Exception;
use GuzzleHttp\Client;
use PPP\DataModel\DeserializerFactory;
use PPP\DataModel\SentenceNode;
use PPP\DataModel\SerializerFactory;
use PPP\Module\DataModel\Deserializers\ModuleResponseDeserializer;
use PPP\Module\DataModel\Serializers\ModuleRequestSerializer;
use PPP\Module\DataModel\ModuleRequest;
use PPP\Module\DataModel\ModuleResponse;
use PPP\Module\HttpException;

/**
 * Tool entry point.
 *
 * @licence GPLv2+
 * @author Thomas Pellissier Tanon
 */
class WikidataSparqlEntryPoint {

	/**
	 * @var string
	 */
	private $parsingBackendUrl;

	/**
	 * @param string $parsingBackendUrl
	 */
	public function __construct( $parsingBackendUrl ) {
		$this->parsingBackendUrl = $parsingBackendUrl;
	}

	/**
	 * Main function
	 */
	public function exec() {
		try {
			$this->filterRequestMethod();
			$request = $this->getRequest();
			$response = $this->getNLPResponse( $request );
			$this->outputResponse( $this->buildSparqlQuery( $response ) );
		} catch( HttpException $e ) {
			$this->outputHttpException( $e );
		} catch( Exception $e ) {
			$this->outputHttpException( new HttpException( $e->getMessage(), 500, $e ) );
		}
	}

	private function filterRequestMethod() {
		if( !array_key_exists( 'REQUEST_METHOD', $_SERVER ) ) {
			return;
		}
		if( $_SERVER['REQUEST_METHOD'] === 'OPTIONS' ) {
			exit();
		}
		if( $_SERVER['REQUEST_METHOD'] !== 'GET' ) {
			new HttpException( 'Bad request method: ' . $_SERVER['REQUEST_METHOD'], 405 );
		}
	}

	/**
	 * @return ModuleRequest
	 */
	private function getRequest() {
		if( !array_key_exists( 'q', $_GET )|| !array_key_exists( 'lang', $_GET ) ) {
			throw new HttpException( 'You should provide a query and a language', 400 );
		}

		return new ModuleRequest( 
			$_GET['lang'],
			new SentenceNode( $_GET['q'] ),
			'wikidata-sparql-' . time(). '-'. rand( 0, PHP_INT_MAX )
		);
	}

	private function getNLPResponse( ModuleRequest $moduleRequest ) {
		$client = new Client();
		$response = $client->post(
			$this->parsingBackendUrl,
			[ 'json' => $this->buildRequestSerializer()->serialize( $moduleRequest ) ]
		);


		if( $response->getStatusCode()!== 200 ) {
			throw new Exception( 'The parsing failed' );
		}

		foreach( $response->json() as $responseSerialization ) {
			return $this->buildResponseDeserializer()->deserialize( $responseSerialization );
		}

		throw new Exception( 'Parsing does not return anything' );
	}

	private function buildSparqlQuery( ModuleResponse $response ) {
		return ''; //TODO
	}

	private function outputResponse( $query ) {
		@header( 'Content-type: application/sparql-query' );
		echo $query;
	}

	private function buildResponseDeserializer() {
		$deserializerFactory = new DeserializerFactory();
		return new ModuleResponseDeserializer( $deserializerFactory->newNodeDeserializer() );
	}

	private function buildRequestSerializer() {
		$serializerFactory = new SerializerFactory();
		return new ModuleRequestSerializer( $serializerFactory->newNodeSerializer() );
	}

	private function outputHttpException( HttpException $exception ){
		$this->setHttpResponseCode( $exception->getCode() );
		echo $exception->getMessage();
	}

	private function setHttpResponseCode( $code ) {
		if( function_exists( 'http_response_code' ) ) {
			@http_response_code( $code );
		} else {
			@header( 'X-PHP-Response-Code: '. $code, true, $code );
		}
	}
}
