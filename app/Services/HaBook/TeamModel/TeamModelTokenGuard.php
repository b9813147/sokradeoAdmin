<?php

namespace App\Services\HaBook\TeamModel;

use App\Helpers\CoreService\CoreServiceApi;
use App\Repositories\UserRepository;
use App\Services\ApplicationService;
use Illuminate\Http\Request;
use Illuminate\Container\Container;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Debug\ExceptionHandler;
use League\OAuth2\Server\ResourceServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Nyholm\Psr7\Factory\Psr17Factory;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;

/**
 * Team Model Token Guard，專門驗證 Team Model Server 發出的 Token
 *
 * @package App\Services\TeamModel
 */
class TeamModelTokenGuard
{
    use CoreServiceApi;

    /**
     * The resource server instance.
     *
     * @var \League\OAuth2\Server\ResourceServer
     */
    protected $server;

    /**
     * The user provider implementation.
     *
     * @var \Illuminate\Contracts\Auth\UserProvider
     */
    protected $provider;

    /**
     * The token repository instance.
     *
     * @var \App\Repositories\Eloquent\Oauth2MemberRepository
     */
    protected $tokens;

    /**
     * Create a new token guard instance.
     *
     * @param \League\OAuth2\Server\ResourceServer $server
     * @param \Illuminate\Contracts\Auth\UserProvider $provider
     * @param UserRepository $tokens
     */
    public function __construct(
        ResourceServer $server,
        UserProvider   $provider,
        UserRepository $tokens
    )
    {
        $this->server   = $server;
        $this->provider = $provider;
        $this->tokens   = $tokens;
    }

    /**
     * Get the user for the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     *
     * @throws \App\Exceptions\RepositoryException
     */
    public function user(Request $request)
    {
        if ($request->bearerToken()) {
            return $this->authenticateViaBearerToken($request);
        }
    }

    /**
     * Authenticate the incoming request via the Bearer token.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     *
     * @throws \App\Exceptions\RepositoryException
     */
    protected function authenticateViaBearerToken(Request $request)
    {
        $psr17Factory   = new Psr17Factory();
        $psrHttpFactory = new PsrHttpFactory($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);
        $psrRequest     = $psrHttpFactory->createRequest($request);

        try {
            // 解析 Team Model Token
            $psr = $this->server->validateAuthenticatedRequest($psrRequest);

            $userInfo = $psr->getAttribute('user_info');
            $haBook   = $userInfo['habook'];
            $name     = $userInfo['name'];

            // 驗證用戶是否存在
            if (!$this->isExist($haBook)) {
                return;
            }

            // 驗證 Team Model ID 是否有綁定 sokradeo 帳號
            $token = $this->tokens->firstWhere(['habook' => $haBook]);

            // 建立新用戶
            if (!$token) {
                $token = $this->tokens->create(['name' => $name, 'habook' => $haBook]);
            }

            // 取得使用者資料
            $user = $this->provider->retrieveById(
                $token->id
            );
            return $user ? $user->withAccessToken($token) : null;
        } catch (OAuthServerException $e) {
            return Container::getInstance()->make(
                ExceptionHandler::class
            )->report($e);
        }
    }

}
