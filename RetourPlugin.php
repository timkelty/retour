<?php
/**
 * Retour plugin for Craft CMS
 *
 * Redirect URLs to retain your website's SEO gravitas when migrating a website or restructuring it.
 *
 * @author    Andrew Welch
 * @copyright Copyright (c) 2016 nystudio107
 * @link      http://nystudio107.com
 * @package   Retour
 * @since     1.0.0
 */

namespace Craft;

class RetourPlugin extends BasePlugin
{
    /**
     * @return mixed
     */
    public function init()
    {
        craft()->onException = function(\CExceptionEvent $event)
        {
            if (isset($event->exception->statusCode) && ($event->exception->statusCode == 404))
            {
                if (craft()->request->isSiteRequest() && !craft()->request->isLivePreview())
                {

/* -- See if we should redirect */

                    $url = craft()->request->getUrl();
                    $redirect = craft()->retour->findRedirectMatch($url);

/* -- Log all 404's */

                    craft()->retour->incrementStatistics($url);

/* -- Redirect if we found a match, otherwise let Craft handle it */

                    if (isset($redirect))
                    {
                        $event->handled = true;
                        RetourPlugin::log("Redirecting " . $url . " to " . $redirect['redirectDestUrl'], LogLevel::Info, false);
                        craft()->request->redirect($redirect['redirectDestUrl'], false, $redirect['redirectHttpCode']);
                    }
                }
            }
        };
    }

    /**
     * Returns the user-facing name.
     *
     * @return mixed
     */
    public function getName()
    {
        $pluginNameOverride = $this->getSettings()->getAttribute('pluginNameOverride');
        return empty($pluginNameOverride) ? Craft::t('Retour') : $pluginNameOverride;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return Craft::t("Redirect URLs to retain your website's SEO gravitas when migrating a website or restructuring it.");
    }

    /**
     * @return string
     */
    public function getDocumentationUrl()
    {
        return 'https://github.com/nystudio107/retour/blob/master/README.md';
    }

    /**
     * @return string
     */
    public function getReleaseFeedUrl()
    {
        return 'https://raw.githubusercontent.com/nystudio107/retour/master/releases.json';
    }

    /**
     * Returns the version number.
     *
     * @return string
     */
    public function getVersion()
    {
        return '1.0.0';
    }

    /**
     * @return string
     */
    public function getSchemaVersion()
    {
        return '1.0.0';
    }

    /**
     * @return string
     */
    public function getDeveloper()
    {
        return 'nystudio107';
    }

    /**
     * @return string
     */
    public function getDeveloperUrl()
    {
        return 'http://nystudio107.com';
    }

    /**
     * @return bool
     */
    public function hasCpSection()
    {
        return true;
    }

    /**
     * @return array
     */
    protected function defineSettings()
    {
        return array(
            'pluginNameOverride'  => AttributeType::String
        );
    }

    public function registerCpRoutes()
    {
        return array(
            'retour/settings'                   => array('action' => 'retour/editSettings'),
            'retour/new'                        => array('action' => 'retour/editRedirect'),
            'retour/edit/(?P<redirectId>\d+)'   => array('action' => 'retour/editRedirect'),
        );
    }

    /**
     */
    public function onBeforeInstall()
    {
    }

    /**
     */
    public function onAfterInstall()
    {

/* -- Show our "Welcome to Retour" message */

        craft()->request->redirect(UrlHelper::getCpUrl('retour/welcome'));
    }

    /**
     */
    public function onBeforeUninstall()
    {
    }

    /**
     */
    public function onAfterUninstall()
    {
    }

}
