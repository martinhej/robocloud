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
            'priority' => ['S' => $message->getPriority()],
            'purpose' => ['S' => $message->getPurpose()],
        ];

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
