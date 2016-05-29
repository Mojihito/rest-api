<?php
/**
 * This file is part of the cotton_api package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AuthController
 * @package AppBundle\Controller
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class AuthController extends Controller
{
    /**
     * @param Request $request
     * @ApiDoc(
     *  section="Users",
     *  resource=false,
     *  authentication = true,
     *  authenticationRoles = {"ROLE_USER"},
     *  description="Get current user",
     *  requirements={
     *     {"name"="_format", "dataType"="string", "requirement"="json|xml", "description"="Response format"},
     *  },
     *  statusCodes={
     *     200="OK",
     *     201="Created",
     *     400="Bad Request",
     *     401="Unauthorized",
     *     403="Forbidden",
     *     404="Not Found",
     *     500="Internal Server Error"
     *  }
     * )
     * @Route("/users/me.{_format}", name="users_current",
     *     requirements={
     *         "_format": "json|xml",
     *     })
     * @Method({"GET"})
     * @return JsonResponse
     * @Rest\View
     */
    public function meAction(Request $request)
    {
        $responseHandler = $this->get('vardius_crud.response.handler');
        $redis = $this->get('snc_redis.default');
        $user = $this->getUser();

        if ($user) {
            $params = [
                'data' => $user,
                'lastActivity' => $redis->get('user_old_' . $user->getId())
            ];


            return $responseHandler->getResponse($request->get('_format'), null, null, $params, 200, [], ['groups' => ['show']]);
        }

        return $responseHandler->getResponse($request->get('_format'), null, null, ['message' => 'User is not identified'], 404);
    }

    /**
     * @ApiDoc(
     *  section="Authorization",
     *  resource=false,
     *  description="Revoke token",
     *  authentication = true,
     *  requirements={
     *     {"name"="_format", "dataType"="string", "requirement"="json|xml", "description"="Response format"},
     *  },
     *  statusCodes={
     *     200="OK",
     *     201="Created",
     *     400="Bad Request",
     *     401="Unauthorized",
     *     403="Forbidden",
     *     404="Not Found",
     *     500="Internal Server Error"
     *  }
     * )
     * @Route("/oauth/v2/revoke.{_format}", name="oauth_token_revoke",
     *     requirements={
     *         "_format": "json|xml",
     *     })
     * @Method({"GET|POST"})
     * @return JsonResponse
     * @Rest\View
     */
    public function revokeAction(Request $request)
    {
        $responseHandler = $this->get('vardius_crud.response.handler');
        $entityManager = $this->get('doctrine.orm.entity_manager');
        $repository = $entityManager->getRepository('AppBundle:RefreshToken');

        $token = $request->get('token');
        $accessToken = $repository->findOneBy(['token' => $token]);

        if ($accessToken) {
            $entityManager->remove($accessToken);
            $entityManager->flush();

            return $responseHandler->getResponse($request->get('_format'), null, null, ['message' => 'Revoked access for token: ' . $token]);
        }

        return $responseHandler->getResponse($request->get('_format'), null, null, ['message' => 'User is not identified'], 404);
    }
}
