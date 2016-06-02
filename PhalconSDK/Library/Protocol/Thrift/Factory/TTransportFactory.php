<?php

namespace SDK\Library\Protocol\Thrift\Factory;

use SDK\Library\Protocol\Thrift\Transport\TTransport;

class TTransportFactory
{
  /**
   * @static
   * @param TTransport $transport
   * @return TTransport
   */
  public static function getTransport(TTransport $transport)
  {
    return $transport;
  }
}
