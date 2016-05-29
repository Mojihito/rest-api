<?php
/**
 * This file is part of the rest-api package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Mailer;

use Symfony\Bundle\TwigBundle\TwigEngine;

/**
 * Class Mailer
 * @package AppBundle\Mailer
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class Mailer
{
    /** @var  \Swift_Mailer */
    protected $mailer;
    /** @var  TwigEngine */
    protected $twig;
    /** @var  string */
    protected $from;
    /** @var string */
    protected $website;

    /**
     * Mailer constructor.
     * @param \Swift_Mailer $mailer
     * @param TwigEngine $twig
     * @param $from
     * @param $website
     */
    public function __construct(\Swift_Mailer $mailer, TwigEngine $twig, $from, $website)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->from = $from;
        $this->website = $website;
    }

    public function sendEmail($to, $from = null, $subject, $template, array $parameters = [])
    {
        $from = $from ?: $this->from;
        $parameters['website'] = $this->website;

        $message = $this->mailer->createMessage()
            ->setSubject($subject)
            ->setFrom($from)
            ->setTo($to)
            ->setBody(
                $this->twig->render(
                    ':Email:' . $template . '.email.twig',
                    $parameters
                ),
                'text/html'
            );

        return $this->mailer->send($message);
    }
}
