<?php

namespace robocloud;

use robocloud\DependencyInjection\RobocloudExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class RobocloudBundle.
 *
 * @package robocloud
 */
final class RobocloudBundle extends Bundle
{

    /**
     * @return RobocloudExtension
     */
    public function getContainerExtension() : RobocloudExtension
    {
        if ($this->extension === null) {
            $this->extension = new RobocloudExtension();
        }
        return $this->extension;
    }

}
