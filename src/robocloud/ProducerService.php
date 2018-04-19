<?php

namespace robocloud;

use robocloud\Message\MessageInterface;

class ProducerService {

  /**
   * @param MessageInterface[] $messages
   */
  public static function pushMessages(array $messages) {

  }

}

/*
 * @todo
 * - update dynamodb transformer
 * - implement producer service
 * - implement consumer recovery via interface - for the moment use in file storage
 * - implement consumer
 * - add it to robopoint project and try out
 */
