<?php

namespace Wikibase\MediaInfo\DataModel\Serialization;

use Serializers\DispatchableSerializer;
use Serializers\Exceptions\SerializationException;
use Serializers\Exceptions\UnsupportedObjectException;
use Serializers\Serializer;
use Wikibase\MediaInfo\DataModel\MediaInfo;

/**
 * @license GPL-2.0+
 * @author Bene* < benestar.wikimedia@gmail.com >
 */
class MediaInfoSerializer implements DispatchableSerializer {

	/**
	 * @var Serializer
	 */
	private $termListSerializer;

	/**
	 * @var Serializer
	 */
	private $statementListSerializer;

	/**
	 * @param Serializer $termListSerializer
	 * @param Serializer $statementListSerializer
	 */
	public function __construct(
		Serializer $termListSerializer,
		Serializer $statementListSerializer
	) {
		$this->termListSerializer = $termListSerializer;
		$this->statementListSerializer = $statementListSerializer;
	}

	/**
	 * @see DispatchableSerializer::isSerializerFor
	 *
	 * @param mixed $object
	 *
	 * @return boolean
	 */
	public function isSerializerFor( $object ) {
		return $object instanceof MediaInfo;
	}

	/**
	 * @see Serializer::serialize
	 *
	 * @param MediaInfo $object
	 *
	 * @throws SerializationException
	 * @return array
	 */
	public function serialize( $object ) {
		if ( !$this->isSerializerFor( $object ) ) {
			throw new UnsupportedObjectException(
				$object,
				'MediaInfoSerializer can only serialize MediaInfo objects.'
			);
		}

		return $this->getSerialized( $object );
	}

	private function getSerialized( MediaInfo $mediaInfo ) {
		$serialization = [
			'type' => $mediaInfo->getType(),
			'labels' => $this->termListSerializer->serialize( $mediaInfo->getLabels() ),
			'descriptions' => $this->termListSerializer->serialize( $mediaInfo->getDescriptions() ),
			'statements' => $this->statementListSerializer->serialize( $mediaInfo->getStatements() )
		];

		$id = $mediaInfo->getId();

		if ( $id !== null ) {
			$serialization['id'] = $id->getSerialization();
		}

		return $serialization;
	}

}
