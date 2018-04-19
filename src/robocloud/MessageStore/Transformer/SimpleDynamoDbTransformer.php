<?php

namespace robocloud\MessageStore\Transformer;

use robocloud\Message\MessageInterface;

/**
 * Class SimpleDynamoDbTransformer.
 *
 * More info on transforming messages to DynamoDB format:
 * http://docs.aws.amazon.com/amazondynamodb/latest/APIReference/API_BatchWriteItem.html
 *
 * @package robocloud\MessageStore\Transformer
 */
class SimpleDynamoDbTransformer implements TransformerInterface {

  /**
   * {@inheritdoc}
   */
  public function transformMessage(MessageInterface $message) {
    $data = [
      'partitionKey' => ['S' => $message->getRoboId()],
      'eventTime'    => ['S' => $message->getMessageTime()],
      'title'        => ['S' => $message->getTitle()],
      'origin'       => ['S' => $message->getMessageId()],
      'identifier'   => ['S' => $message->getPurpose()],
    ];

    if ($description = $message->getDescription()) {
      $data['description'] = ['S' => $description];
    }

    if ($message_data = $message->getData()) {
      foreach ($message_data as $key => $value) {
        $data['data']['M'][$key] = ['S' => $value];
      }
    }

    if ($labels = $message->getTags()) {
      foreach ($labels as $label) {
        $data['labels']['L'][] = ['S' => $label];
      }
    }

    return $data;
  }

}
