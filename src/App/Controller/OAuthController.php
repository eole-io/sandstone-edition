<?php

namespace App\Controller;

use League\OAuth2\Server\Exception\OAuthException;
use Pimple\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use DDesrosiers\SilexAnnotations\Annotations as SLX;
use Alcalyn\SerializableApiResponse\ApiResponse;

/**
 * @SLX\Controller(prefix="/oauth")
 */
class OAuthController
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Get an OAuth access token.
     *
     * @SLX\Route(
     *      @SLX\Request(method="POST", uri="/access-token")
     * )
     *
     * @param Request $request
     *
     * @return ApiResponse With a fresh OAuth token.
     *
     * @throw HttpException On oauth token creation failure.
     */
    public function postAccessToken(Request $request)
    {
        try {
            $token = $this->container['sandstone.oauth.controller']->postAccessToken($request);

            return new ApiResponse($token);
        } catch (OAuthException $e) {
            return new ApiResponse([
                'oauth_error_type' => $e->errorType,
                'message' => $e->getMessage(),
                'parameter' => $e->getParameter(),
                'should_redirect' => $e->shouldRedirect(),
                'redirect' => $e->redirectUri,
            ], $e->httpStatusCode);
        }
    }
}
