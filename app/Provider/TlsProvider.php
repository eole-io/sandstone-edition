<?php

namespace Provider;

use Pimple\ServiceProviderInterface;
use Pimple\Container;

class TlsProvider implements ServiceProviderInterface
{
    /**
     * {@InheritDoc}
     */
    public function register(Container $app)
    {
        $app['sandstone.tls'] = [
            'enabled' => true,
            'local_cert' => $app['project.root'].'/config/tls/cert.pem',
            'local_pk' => $app['project.root'].'/config/tls/key.pem',
            'verify_peer' => false,
            'allow_self_signed' => true,
            //'passphrase' => $app['project.root'].'/config/tls/key.pem',
        ];
    }
}
