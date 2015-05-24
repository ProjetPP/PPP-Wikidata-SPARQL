<?php

namespace PPP\WikidataSparql;

use Exception;
use GuzzleHttp\Client;
use PPP\DataModel\DeserializerFactory;
use PPP\DataModel\ResourceListNode;
use PPP\DataModel\SentenceNode;
use PPP\DataModel\SerializerFactory;
use PPP\DataModel\StringResourceNode;
use PPP\Module\DataModel\Deserializers\ModuleResponseDeserializer;
use PPP\Module\DataModel\Serializers\ModuleRequestSerializer;
use PPP\Module\DataModel\ModuleRequest;
use PPP\Module\DataModel\ModuleResponse;
use PPP\Module\HttpException;

/**
 * @license GNU GPL v2+
 * @author Bene* < benestar.wikimedia@gmail.com >
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
		if( !array_key_exists( 'q', $_GET ) ) {
			throw new HttpException( 'You should provide a query', 400 );
		}

		return new ModuleRequest(
			array_key_exists( 'lang', $_GET ) ? $_GET['lang'] : 'en',
			new SentenceNode( $_GET['q'] ),
			'wikidata-sparql-' . time() . '-' . rand( 0, PHP_INT_MAX )
		);
	}

	private function getNLPResponse( ModuleRequest $moduleRequest ) {
		$client = new Client();
		$response = $client->post(
			$this->parsingBackendUrl,
			[ 'json' => $this->buildRequestSerializer()->serialize( $moduleRequest ) ]
		);


		if( $response->getStatusCode() !== 200 ) {
			throw new Exception( 'The parsing failed' );
		}

		foreach( $response->json() as $responseSerialization ) {
			return $this->buildResponseDeserializer()->deserialize( $responseSerialization );
		}

		return new ModuleResponse(
			$moduleRequest->getLanguageCode(),
			new ResourceListNode(
				[ new StringResourceNode( $moduleRequest->getSentenceTree()->getValue() ) ]
			)
		);
	}

	private function buildSparqlQuery( ModuleResponse $response ) {
		$sparqlGenerator = new SparqlGenerator( new ConditionGeneratorFactory( $response->getLanguageCode() ) );
		return $sparqlGenerator->generateSparql( $response->getSentenceTree() );
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

	private function outputHttpException( HttpException $exception ) {
		@http_response_code( $exception->getCode() );
		echo $exception->getMessage();
	}
}
