<?php

namespace Kraftausdruck\Extensions;

use SilverStripe\Assets\File;
use SilverStripe\Core\Extension;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\HeaderField;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\AssetAdmin\Forms\UploadField;

class SiteConfigExtension extends Extension
{
    private static $db = [
        'GTMAccountID' => 'Varchar',
        'GoogleAnalyticsAccountV4IDs' => 'Text',
        'Clarity' => 'Varchar',
        'ConsentModeEnabled' => 'Boolean'
    ];

    private static $has_one = [
        'BingSiteAuthFile' => File::class
    ];

    private static $owns = [
        'BingSiteAuthFile'
    ];

    public function updateCMSFields(FieldList $fields)
    {
        $tab = 'Root.' . _t(__CLASS__ . '.TabName', 'Tracking');

        $fields->addFieldsToTab($tab, [
            HeaderField::create('GoogleHeading', _t(__CLASS__ . '.GoogleHeadingField', 'Google')),
            TextField::create('GTMAccountID', _t(__CLASS__ . '.GTMAccountIDField', 'Google Tag Manager (GTM-XXXXXXX)'), '', 13),
            TextareaField::create('GoogleAnalyticsAccountV4IDs', _t(__CLASS__ . '.GoogleAnalyticsAccountV4IDsField', 'Google Analytics v4 (G-XXXXXXXXXX)'), '', 13)
                ->setDescription(_t(__CLASS__ . '.GoogleAnalyticsAccountV4IDsFieldDescription', 'One ID per line for multiple properties.')),
            CheckboxField::create('ConsentModeEnabled', _t(__CLASS__ . '.ConsentModeEnabledField', 'Enable Google Consent Mode v2'))
                ->setDescription(_t(__CLASS__ . '.ConsentModeEnabledDescription', 'For privacy-compliant tracking, it\'s recommended to set up Analytics via GTM-tag instead of using the Analytics field above.<br>GTM-tag should be triggered on "klaro-google-analytics-accepted".'))
        ]);

        $fields->addFieldsToTab($tab, [
            HeaderField::create('ClarityHeading', _t(__CLASS__ . '.ClarityHeadingField', 'Clarity')),
            TextField::create('Clarity', _t(__CLASS__ . '.ClarityField', 'Clarity Tracking ID')),
        ]);

        $fields->addFieldsToTab($tab, [
            HeaderField::create('BingHeading', _t(__CLASS__ . '.BingHeadingField', 'Bing')),
            UploadField::create('BingSiteAuthFile', _t(__CLASS__ . '.BingSiteAuthFileField', 'BingSiteAuth.xml'))
        ]);
    }
}
