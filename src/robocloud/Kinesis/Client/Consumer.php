<?php

namespace robocloud\Kinesis\Client;

/**
 * Class Consumer.
 *
 * The client to read records from Amazon Kinesis Stream. The main functions of
 * this class are:
 * - provide a way to continue at the last read record
 * - skip Kinesis Stream results having empty Records
 * - convert Kinesis Stream results to objects of the type MessageInterface.
 *
 * @package robocloud\Kinesis
 */
class Consumer extends AbstractKinesisClient implements ConsumerInterface {

  /**
   * The sequence number at which the last reading stopped.
   *
   * @var string
   */
  protected $lastSequenceNumber;

  /**
   * The shard id from which the last reading was done.
   *
   * @var string
   */
  protected $lastShardId;

  /**
   * The count of messages read from the stream at once.
   *
   * @var int
   */
  protected $batchSize = 10;

  /**
   * The initial shard iterator type.
   *
   * Set by default to "TRIM_HORIZON" which means from the beginning. It could
   * be set to "LATEST" that will read most recent data in the shard.
   *
   * @var string
   *
   * @see http://docs.aws.amazon.com/kinesis/latest/APIReference/API_GetShardIterator.html
   */
  protected $initialShardIteratorType = self::ITERATOR_TYPE_TRIM_HORIZON;

  /**
   * Number of milliseconds behind latest event in the stream.
   *
   * @var int
   */
  protected $lag;

  /**
   * Read from beginning iterator type.
   *
   * @see http://docs.aws.amazon.com/kinesis/latest/APIReference/API_GetShardIterator.html
   */
  const ITERATOR_TYPE_TRIM_HORIZON = 'TRIM_HORIZON';

  /**
   * Read after the provided sequence number iterator type.
   *
   * @see http://docs.aws.amazon.com/kinesis/latest/APIReference/API_GetShardIterator.html
   */
  const ITERATOR_TYPE_AFTER_SEQUENCE_NUMBER = 'AFTER_SEQUENCE_NUMBER';

  /**
   * {@inheritdoc}
   */
  public function getMessages($shard_id = NULL, $last_sequence_number = NULL) {

    $records = [];

    // Get the initial iterator.
    try {
      $shard_iterator = $this->getInitialShardIterator($shard_id, $last_sequence_number);
    }
    catch (\Exception $e) {
      $this->processError($e, [
        'shard_id' => $shard_id,
        'last_sequence_number' => $last_sequence_number,
      ]);

      return $records;
    }

    do {

      $has_records = FALSE;

      // Load batch of records.
      try {
        $res = $this->getClient()->getRecords([
          'Limit' => $this->batchSize,
          'ShardIterator' => $shard_iterator,
          'StreamName' => $this->getStreamName(),
        ]);

        // Get the Shard iterator for next batch.
        $shard_iterator = $res->get('NextShardIterator');
        $behind_latest = $res->get('MillisBehindLatest');

        foreach ($res->search('Records[].[SequenceNumber, Data]') as $event) {
          list($sequence_number, $message_data) = $event;

          try {
            $records[$sequence_number] = $this->createMessage($message_data, $shard_id, $behind_latest, $this->getStreamName());
          }
          catch (\Exception $e) {
            $this->processError($e, $event);
          }

          $has_records = TRUE;
        }
      }
      catch (\Exception $e) {
        $this->processError($e);
      }

    } while (!empty($shard_iterator) && !empty($behind_latest) && !$has_records);

    if (!empty($sequence_number)) {
      $this->lastSequenceNumber = $sequence_number;
    }

    if (!empty($shard_id)) {
      $this->lastShardId = $shard_id;
    }

    if (!empty($behind_latest)) {
      $this->lag = $behind_latest;
    }

    return $records;
  }

  /**
   * {@inheritdoc}
   */
  public function getLag() {
    return $this->lag;
  }

  /**
   * {@inheritdoc}
   */
  public function getLastSequenceNumber() {
    return $this->lastSequenceNumber;
  }

  /**
   * {@inheritdoc}
   */
  public function getLastShardId() {
    return $this->lastShardId;
  }

  /**
   * {@inheritdoc}
   */
  public function setBatchSize($size) {
    $this->batchSize = (int) $size;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setInitialShardIteratorType($type) {
    $this->initialShardIteratorType = $type;
    return $this;
  }

  /**
   * Gets the initial shard iterator.
   *
   * @param string $shard_id
   *   The shard id.
   * @param string $starting_sequence_number
   *   The starting sequence number.
   *
   * @return object
   *   The shard iterator.
   */
  protected function getInitialShardIterator($shard_id, $starting_sequence_number = NULL) {

    $args = [
      'ShardId' => $shard_id,
      'StreamName' => $this->getStreamName(),
      'ShardIteratorType' => $this->initialShardIteratorType,
    ];

    // If we have the starting sequence number update also the ShardIteratorType
    // to start reading just after it.
    if (!empty($starting_sequence_number)) {
      $args['ShardIteratorType'] = self::ITERATOR_TYPE_AFTER_SEQUENCE_NUMBER;
      $args['StartingSequenceNumber'] = $starting_sequence_number;
    }

    $res = $this->getClient()->getShardIterator($args);

    return $res->get('ShardIterator');
  }

  /**
   * Creates the message object from the Kinesis stream data.
   *
   * @param string $message_data
   *   Serialized message from Kinesis.
   * @param string $shard_id
   *   The shard id.
   * @param int $lag
   *   The milliseconds behind latests.
   * @param string $stream_name
   *   The Kinesis stream name.
   *
   * @return \robocloud\Message\MessageInterface
   *   The message object.
   *
   * @throws \InvalidArgumentException
   *   When the serialized message could not be unserialized.
   * @throws \robocloud\Exception\InvalidMessageDataException
   *   If a valid message could not be created from the provided data.
   */
  protected function createMessage($message_data, $shard_id, $lag, $stream_name) {

    $message_data = $this->getMessageFactory()->unserialize($message_data);

    $message_data['shardId'] = $shard_id;
    $message_data['streamName'] = $stream_name;
    $message_data['lag'] = $lag;

    return $this->getMessageFactory()->setMessageData($message_data)->createMessage();
  }

}
