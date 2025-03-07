<?php
/*
NB: This is not stand alone code and is intended to be used within "seriti/slim3-skeleton" framework
The code snippet below is for use within an existing src/setup_app.php file within this framework
add the below code snippet to the end of existing "src/setup_app.php" file.
This tells the framework about module: name, sub-memnu route list and title, database table prefix.
*/

$container['config']->set('module','website',['name'=>'Website public',
                                             'route_root'=>'admin/website/',
                                             'route_list'=>['dashboard'=>'Dashboard','menu'=>'Menu items','page'=>'Pages',
                                                            'setup'=>'Site setup','seo'=>'SEO Setup','help'=>'Help'],
                                             'table_prefix'=>'www_',
                                             'route_root_page'=>'public/',
                                             'route_root_account'=>'public/account/',
                                             'user_register'=>false
                                            ]);
