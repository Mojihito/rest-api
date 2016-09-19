<?php
/**
 * This file is part of the rest-api package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Form\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;


/**
 * Class RemoveNotSubmittedFieldListener
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class RemoveNotSubmittedFieldListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SUBMIT => 'onPreSubmit',
        );
    }

    public function onPreSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        if (!$data) {
            return;
        }

        foreach ($form->all() as $key => $name) {
            if (!array_key_exists($key, $data)) {
                $form->remove($key);
            }
        }
    }
}
