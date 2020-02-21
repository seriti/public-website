<?php
namespace App\Website;

use Seriti\Tools\SetupModule;

class SiteSetup extends SetupModule
{
    public function setup() {

        $param = [];
        $param['info'] = 'Select whether you would like default or inverse colors on main menu.';
        $param['rows'] = 5;
        $param['value'] = 'INVERSE';
        $param['options'] = array('DEFAULT','INVERSE');
        $this->addDefault('SELECT','WWW_MENU_STYLE','Main menu style',$param);

        $param = [];
        $param['info'] = 'Select the image you would like to use as an icon at top left of main menu (max 50KB)';
        $param['max_size'] = 100000;
        $param['value'] = 'images/sunflower64.png';
        $this->addDefault('IMAGE','WWW_MENU_IMAGE','Main menu icon',$param);

        $param = [];
        $param['info'] = 'Select the colour theme for entire site. Thanks to <a href="http://www.bootswatch.com" target="_blank">bootswatch.com</a>';
        $param['rows'] = 5;
        $param['value'] = 'DEFAULT';
        $param['options'] = array('DEFAULT','cerulean','cosmo','cyborg','darkly','flatly','journal','lumen','paper','readable',
                                  'sandstone','simplex','slate','spacelab','superhero','united','yeti');
        $this->addDefault('SELECT','WWW_SITE_THEME','Colour theme',$param);

        $param = [];
        $param['info'] = 'Specify footer text you would like to appear on all pages. You can use HTML if you wish.';
        $param['rows'] = 10;
        $param['value'] = '';
        $this->addDefault('HTML','WWW_FOOTER','Footer text',$param);

        $param = [];
        $param['info'] = 'Custom CSS rules which will be placed inline after all other CSS. No HTML permitted.';
        $param['rows'] = 10;
        $param['value'] = '';
        $this->addDefault('TEXTAREA','WWW_SITE_CSS','Custom CSS',$param);
    }    
}

        

?>
