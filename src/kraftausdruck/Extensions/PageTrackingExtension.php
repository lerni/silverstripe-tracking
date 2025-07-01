<?php

namespace Kraftausdruck\Extensions;

use SilverStripe\ORM\ArrayList;
use SilverStripe\Core\Extension;
use SilverStripe\View\ArrayData;
use SilverStripe\Control\Director;
use SilverStripe\Security\Security;
use SilverStripe\View\Requirements;
use SilverStripe\Core\Config\Config;

class PageTrackingExtension extends Extension
{
    private static $track_members = false;
    private static $preconnect = false;

    public function contentControllerInit($controller)
    {
        $member = Security::getCurrentUser();
        $trackMembers = Config::inst()->get(self::class, 'track_members');

        if (Director::isLive() && ($trackMembers || !$member)) {
            $siteConfig = $this->owner->SiteConfig;
            $GTMAccountId = $siteConfig->GTMAccountID;
            $accountV4IDs = $this->perLine($siteConfig->GoogleAnalyticsAccountV4IDs);
            $clarity = $siteConfig->Clarity;
            $consentModeEnabled = $siteConfig->ConsentModeEnabled;

            $preconnect = Config::inst()->get(self::class, 'preconnect');
            $arrayData = new ArrayData([
                'GTMAccountId' => $GTMAccountId,
                'AccountV4IDs' => $accountV4IDs,
                'Clarity' => $clarity,
                'ConsentModeEnabled' => $consentModeEnabled
            ]);

            if ($preconnect === true) {
                if (!empty($GTMAccountId) || !empty($accountV4IDs)) {
                    Requirements::insertHeadTags('<link rel="preconnect" href="https://www.googletagmanager.com">');
                }
                if (!empty($clarity)) {
                    Requirements::insertHeadTags('<link rel="preconnect" href="https://www.clarity.ms">');
                }
            }
            $trackingString = $arrayData->renderWith('TrackingTop');
            if ($trackingString) {
                Requirements::insertHeadTags($trackingString);
            }
        }
    }

    public function perLine($text, $start = 0)
    {
        $r = ArrayList::create();
        if ($text) {
            $stringwithnoemptylines = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", trim($text));
            $lines = explode(PHP_EOL, $stringwithnoemptylines);
            $i = 0;
            foreach ($lines as $l) {
                $trimmed = trim($l);
                if (!empty($trimmed) && $start <= ($i + 1)) {
                    $r->push(ArrayData::create(['Item' => $trimmed]));
                }
                $i++;
            }
        }
        return $r;
    }
}
