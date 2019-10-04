<?php
namespace App\Website;

use Seriti\Tools\SetupModule;

class SeoSetup extends SetupModule
{
    public function setup() {

        $param = [];
        $param['info'] = 'Must be a valid <a href="https://support.google.com/analytics/answer/1008080" target="_blank">Google analytics ID</a>.'.
                         'Leave blank to ignore.';
        $param['value'] = '';
        $this->addDefault('TEXT','GA_ANALYTICS_ID','Google analytics ID',$param);

        $param = [];
        $param['info'] = 'Meta page title to be used on all pages where not specified for that page.';
        $param['value'] = '';
        $this->addDefault('TEXT','WWW_META_TITLE','Default Meta page title',$param);

        $param = [];
        $param['info'] = 'Meta key words to be used on all pages where not specified for that page.'.
                         'Separate by using commas or spaces.';
        $param['value'] = '';
        $this->addDefault('TEXT','WWW_META_KEY','Default Meta key words',$param);

        $param = [];
        $param['info'] = 'Brief Meta page description to be used on all pages where not specified for that page.';
        $param['rows'] = 5;
        $param['value'] = '';
        $this->addDefault('TEXTAREA','WWW_META_DESC','Default Meta page description',$param);
    }    
}

        

?>
