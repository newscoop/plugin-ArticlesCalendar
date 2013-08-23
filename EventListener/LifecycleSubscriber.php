<?php
/**
 * @package Newscoop\ArticlesCalendarBundle
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\ArticlesCalendarBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Newscoop\EventDispatcher\Events\GenericEvent;
use Newscoop\ArticlesCalendarBundle\Entity\Settings;

/**
 * Event lifecycle management
 */
class LifecycleSubscriber implements EventSubscriberInterface
{
    private $em;

    public function __construct($em) {
        $this->em = $em;
    }

    public function install(GenericEvent $event)
    {
        if ($event->getArgument('plugin_name') != 'newscoop/articles-calendar-plugin') {
            return;
        }

        $tool = new \Doctrine\ORM\Tools\SchemaTool($this->em);
        $tool->updateSchema($this->getClasses(), true);

        // Generate proxies for entities
        $this->em->getProxyFactory()->generateProxyClasses($this->getClasses(), __DIR__ . '/../../../../library/Proxy');

        $settings = new Settings();
        $settings->setFirstDay(1);
        $settings->setNavigation(true);
        $settings->setShowDayNames(true);
        $settings->setRendition('issuethumb');
        $settings->setImageWidth(0);
        $settings->setImageHeight(0);
        $settings->setStyles('/* Some custom CSS */');
        $settings->setIsActive(true);
        $settings->setPublicationNumbers('2');
        $settings->setCreatedAt(new \DateTime('now'));
        $this->em->persist($settings);
        $this->em->flush();
    }

    public function update(GenericEvent $event)
    {
        if ($event->getArgument('plugin_name') != 'newscoop/articles-calendar-plugin') {
            return;
        }


        $tool = new \Doctrine\ORM\Tools\SchemaTool($this->em);
        $tool->updateSchema($this->getClasses(), true);

        // Generate proxies for entities
        $this->em->getProxyFactory()->generateProxyClasses($this->getClasses(), __DIR__ . '/../../../../library/Proxy');
    }

    public function remove(GenericEvent $event)
    {
        if ($event->getArgument('plugin_name') != 'newscoop/articles-calendar-plugin') {
            return;
        }
        
        $tool = new \Doctrine\ORM\Tools\SchemaTool($this->em);
        $tool->dropSchema($this->getClasses(), true);
    }

    public static function getSubscribedEvents()
    {
        return array(
            'plugin.install.newscoop_articles_calendar_plugin' => array('install', 1),
            'plugin.update.newscoop_articles_calendar_plugin' => array('update', 1),
            'plugin.remove.newscoop_articles_calendar_plugin' => array('remove', 1),
        );
    }

    private function getClasses(){
        return array(
            $this->em->getClassMetadata('Newscoop\ArticlesCalendarBundle\Entity\ArticleOfTheDay'),
            $this->em->getClassMetadata('Newscoop\ArticlesCalendarBundle\Entity\Settings'),
        );
    }
}
