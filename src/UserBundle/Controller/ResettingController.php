<?php
/**
 * This file is part of the rest-api package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace UserBundle\Controller;

use FOS\UserBundle\Event\{
    FilterUserResponseEvent, FormEvent, GetResponseUserEvent, UserInterface
};
use FOS\UserBundle\FOSUserEvents;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\{
    JsonResponse, RedirectResponse, Request
};
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class ResettingController
 * @package UserBundle\Controller
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class ResettingController extends \FOS\UserBundle\Controller\ResettingController
{
    /**
     * @ApiDoc(
     *  resource=false,
     *  section="Resetting",
     *  description="Request reset user password: submit form and send email",
     *  views = {"default"},
     *  requirements={
     *      {
     *          "name"="username",
     *          "dataType"="string",
     *          "description"="Username or email"
     *      }
     *  },
     *  parameters={
     *      {"name"="username", "dataType"="string", "required"=true, "description"="Username or email"}
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
     */
    public function sendEmailAction(Request $request)
    {
        $username = $request->request->get('username');

        /** @var $user UserInterface */
        $user = $this->get('fos_user.user_manager')->findUserByUsernameOrEmail($username);

        if (null === $user) {
            return new JsonResponse([
                'errors' => [
                    'invalid_username' => $username
                ]
            ], 404);
        }

        if ($user->isPasswordRequestNonExpired($this->container->getParameter('fos_user.resetting.token_ttl'))) {
            return new JsonResponse([
                'errors' => [
                    'password_already_requested' => $this->get('translator')->trans('resetting.password_already_requested', [], 'FOSUserBundle')
                ]
            ], 400);
        }

        if (null === $user->getConfirmationToken()) {
            /** @var $tokenGenerator \FOS\UserBundle\Util\TokenGeneratorInterface */
            $tokenGenerator = $this->get('fos_user.util.token_generator');
            $user->setConfirmationToken($tokenGenerator->generateToken());
        }

        $template = $this->getParameter('fos_user.resetting.email.template');
        $rendered = $this->render($template, array(
            'user' => $user,
            'confirmationUrl' => $this->getParameter('website') . '/reset/' . $user->getConfirmationToken()
        ));

        $renderedLines = explode("\n", trim($rendered));
        $subject = $renderedLines[0];
        $body = implode("\n", array_slice($renderedLines, 1));

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($this->getParameter('fos_user.resetting.email.from_email'))
            ->setTo($user->getEmail())
            ->setBody($body);

        $this->get('mailer')->send($message);

        $user->setPasswordRequestedAt(new \DateTime());
        $this->get('fos_user.user_manager')->updateUser($user);

        return new JsonResponse([
            'email' => $this->getObfuscatedEmail($user)
        ]);
    }

    /**
     * @ApiDoc(
     *  resource=false,
     *  section="Resetting",
     *  description="Reset user password",
     *  input="FOS\UserBundle\Form\Type\ResettingFormType",
     *  views = {"default"},
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
     */
    public function resetAction(Request $request, $token)
    {
        return parent::resetAction($request, $token);
    }
}
