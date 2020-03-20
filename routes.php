<?php  
/*
NB: This is not stand alone code and is intended to be used within "seriti/slim3-skeleton" framework
The code snippet below is for use within an existing src/routes.php file within this framework
copy the "/website" group into the existing "/admin" group within existing "src/routes.php" file 
*/

//*** BEGIN admin access ***
$app->group('/admin', function () {

    $this->group('/website', function () {
        $this->any('/dashboard', \App\Website\DashboardController::class);
        $this->any('/menu', \App\Website\MenuController::class);
        $this->any('/page', \App\Website\PageController::class);
        $this->any('/page_file', \App\Website\PageFileController::class);
        $this->any('/page_image', \App\Website\PageImageController::class);
        $this->any('/setup', \App\Website\SiteSetupController::class);
        $this->any('/seo', \App\Website\SeoSetupController::class);
        $this->any('/help', \App\Website\HelpController::class);
        $this->get('/setup_data', \App\Website\SetupDataController::class);
        $this->post('/ajax', \App\Website\Ajax::class);
    })->add(\App\Website\Config::class);

})->add(\App\ConfigAdmin::class);
//*** END admin access ***

/*
The code snippet below is for use within an existing src/routes.php file within "seriti/slim3-skeleton" framework
replace the existing public access section with this code.  
*/


//*** BEGIN public access ***
$app->redirect('/', '/public/home', 301);
$app->group('/public', function () {
    $this->redirect('', '/public/home', 301);
    $this->redirect('/', 'home', 301);
 
    $this->any('/help', \App\Website\PublicHelpController::class);
    $this->any('/register', \App\Website\RegisterWizardController::class);
    $this->any('/logout', \App\Website\LogoutController::class);

    //NB: this must come last in group
    $this->any('/{link_url}', \App\Website\WebsiteController::class);
})->add(\App\Website\ConfigPublic::class);
//*** END public access ***

