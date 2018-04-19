<?php

namespace robocloud\MessageProcessing\Transformer;

use robocloud\Message\MessageInterface;

/**
 * Class SimpleDynamoDbTransformer.
 *
 * @see http://docs.aws.amazon.com/amazondynamodb/latest/APIReference/API_BatchWriteItem.html
 *   For more info on transforming messages to DynamoDB format.
 *
 * @package robocloud\MessageProcessing\Transformer
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
      'identifier'   => ['S' => (string) $message->getPurpose()],
    ];

    if ($description = $message->getDescription()) {
      $data['description'] = ['S' => $description];
    }

    if ($message_data = $message->getData()) {
      foreach ($message_data as $key => $value) {
        if (!empty($value)) {
          $data['data']['M'][$key] = ['S' => (string) $value];
        }
      }
    }

    if ($labels = $message->getTags()) {
      foreach ($labels as $label) {
        if (!empty($label)) {
          $data['labels']['L'][] = ['S' => (string) $label];
        }
      }
    }

    return $data;
  }

}
