<?php

namespace robocloud\MessageProcessing\Filter;

use robocloud\Message\MessageInterface;

/**
 * Class FilterByRoboId.
 *
 * @package robocloud\MessageProcessing\Filter
 */
class FilterByRoboId implements FilterInterface {

  /**
   * The Message purpose.
   *
   * @var string
   */
  protected $roboId;

  /**
   * FilterByPurpose constructor.
   *
   * @param string $roboId
   *   The Message purpose.
   */
  public function __construct($roboId) {
    $this->roboId = $roboId;
  }

  /**
   * {@inheritdoc}
   */
  public function keepMessage(MessageInterface $message) {
    if ($message->getRoboId() == $this->getRoboId()) {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Gets the Message purpose.
   *
   * @return string
   *   The Message purpose.
   */
  public function getRoboId() {
    return $this->roboId;
  }

}
