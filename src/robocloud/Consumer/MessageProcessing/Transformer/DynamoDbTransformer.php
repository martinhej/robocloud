<?php

namespace robocloud\Consumer\MessageProcessing\Transformer;

use robocloud\Message\MessageInterface;

/**
 * Transforms messages to DynamoDb expected format.
 *
 * @see http://docs.aws.amazon.com/amazondynamodb/latest/APIReference/API_BatchWriteItem.html
 *   For more info on transforming messages to DynamoDB format.
 */
class DynamoDbTransformer implements TransformerInterface
{

    /**
     * {@inheritdoc}
     */
    public function transformMessage(MessageInterface $message)
    {
        $data = [
            'roboId' => ['S' => $message->getRoboId()],
            'messageId' => ['S' => $message->getMessageId()],
            'messageTime' => ['S' => $message->getMessageTime()],
            'purpose' => ['S' => $message->getPurpose()],
        ];

        if ($priority = $message->getPriority()) {
            $data['priority']['S'] = $priority;
        }

        if ($responseTo = $message->getResponseTo()) {
            $data['responseTo']['S'] = $responseTo;
        }

        if ($recipientWildcard = $message->getRecipientWildcard()) {
            $data['recipientWildcard']['S'] = $recipientWildcard;
        }

        if ($message_data = $message->getData()) {
            foreach ($message_data as $key => $value) {
                if (!empty($value)) {
                    $data['data']['M'][$key] = ['S' => (string)$value];
                }
            }
        }

        if ($recipients = $message->getRecipients()) {
            foreach ($recipients as $recipient) {
                $data['recipients']['L'][] = ['S' => (string)$recipient];
            }
        }

        if ($tags = $message->getTags()) {
            foreach ($tags as $tag) {
                if (!empty($tag)) {
                    $data['tags']['L'][] = ['S' => (string)$tag];
                }
            }
        }

        return $data;
    }

}
