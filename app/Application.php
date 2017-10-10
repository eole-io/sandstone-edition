<?php

use Eole\Sandstone\Application as BaseApplication;

class Application extends BaseApplication
{
    /**
     * Initialize Sandstone application.
     *
     * {@Inheritdoc}
     */
    public function __construct(array $values = [])
    {
        parent::__construct($values);

        $this->register(new Provider\EnvironmentProvider());
        $this->register(new Provider\SerializerProvider());
        $this->register(new Provider\ApiResponseProvider());
        $this->register(new Provider\WebsocketServerProvider());
        $this->register(new Provider\PushProvider());
        $this->register(new Provider\ServiceProvider());

        $this->registerUserProviders();

        $this->register(new Provider\DoctrineProvider());

        $this['app.user_provider'] = function () {
            return new \Symfony\Component\Security\Core\User\InMemoryUserProvider([
                // username: admin / password: foo
                'admin' => [
                    'roles' => ['ROLE_ADMIN'],
                    'password' => '$2y$10$3i9/lVd8UOFIJ6PAMFt8gu3/r5g0qeCJvoSlLCsvMTythye19F77a',
                ],
            ]);
        };

        $this->register(new \Silex\Provider\SecurityServiceProvider(), [
            'security.firewalls' => [
                'api' => [
                    'pattern' => '^/api',
                    'oauth' => true,
                    'stateless' => true,
                    'anonymous' => true,
                    'users' => $this['app.user_provider'],
                ],
            ],
        ]);

        $this->register(new \Eole\Sandstone\OAuth2\Silex\OAuth2ServiceProvider(), [
            'oauth.firewall_name' => 'api',
            'oauth.security.user_provider' => 'app.user_provider',
            'oauth.tokens_dir' => $this['project.root'].'/var/oauth-tokens',
            'oauth.scope' => $this['environment']['oauth']['scope'],
            'oauth.clients' => $this['environment']['oauth']['clients'],
        ]);
    }

    /**
     * Here is user application providers for both RestApi and websocket containers.
     * Register services, doctrine mappings...
     */
    private function registerUserProviders()
    {
        $this->register(new App\AppProvider());
    }
}
