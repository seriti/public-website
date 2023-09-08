<?php
namespace App\Website;

use Seriti\Tools\SetupModuleData;

class SetupData extends SetupModuledata
{

    public function setupSql()
    {
        $this->tables = ['files','menu','page','help'];

        $this->addCreateSql('files',
                            'CREATE TABLE `TABLE_NAME` (
                              `file_id` int(10) unsigned NOT NULL,
                              `title` varchar(255) NOT NULL,
                              `file_name` varchar(255) NOT NULL,
                              `file_name_orig` varchar(255) NOT NULL,
                              `file_text` longtext NOT NULL,
                              `file_date` date NOT NULL DEFAULT \'0000-00-00\',
                              `location_id` varchar(64) NOT NULL,
                              `location_rank` int(11) NOT NULL,
                              `key_words` text NOT NULL,
                              `description` text NOT NULL,
                              `file_size` int(11) NOT NULL,
                              `encrypted` tinyint(1) NOT NULL,
                              `file_name_tn` varchar(255) NOT NULL,
                              `file_ext` varchar(16) NOT NULL,
                              `file_type` varchar(16) NOT NULL,
                              PRIMARY KEY (`file_id`),
                              FULLTEXT KEY `search_idx` (`key_words`)
                            ) ENGINE=MyISAM DEFAULT CHARSET=utf8');  

        $this->addCreateSql('menu',
                            'CREATE TABLE `TABLE_NAME` (
                              `id` int(11) NOT NULL AUTO_INCREMENT,
                              `id_parent` int(11) NOT NULL,
                              `title` varchar(255) NOT NULL,
                              `level` int(11) NOT NULL,
                              `lineage` varchar(255) NOT NULL,
                              `rank` int(11) NOT NULL,
                              `rank_end` int(11) NOT NULL,
                              `menu_type` varchar(64) NOT NULL,
                              `menu_link` varchar(255) NOT NULL,
                              `menu_access` varchar(64) NOT NULL,
                              `link_mode` varchar(64) NOT NULL,
                              PRIMARY KEY (`id`)
                            ) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8');

        $this->addCreateSql('help',
                            'CREATE TABLE `TABLE_NAME` (
                              `id` INT NOT NULL AUTO_INCREMENT,
                              `title` VARCHAR(255) NOT NULL,
                              `text_markdown` TEXT NOT NULL,
                              `text_html` TEXT NOT NULL,
                              `rank` INT NOT NULL,
                              `access` VARCHAR(64) NOT NULL,
                              `status` VARCHAR(64) NOT NULL,
                              PRIMARY KEY (`id`)
                            ) ENGINE = MyISAM DEFAULT CHARSET = utf8');

        $this->addCreateSql('page',
                            'CREATE TABLE `TABLE_NAME` (
                              `page_id` int(11) NOT NULL AUTO_INCREMENT,
                              `title` varchar(64) NOT NULL,
                              `text_markdown` text NOT NULL,
                              `text_html` text NOT NULL,
                              `type_id` varchar(64) NOT NULL,
                              `status` varchar(64) NOT NULL,
                              `link_url` varchar(250) NOT NULL,
                              `page_access` varchar(64) NOT NULL,
                              `meta_title` varchar(250) NOT NULL,
                              `meta_key` text NOT NULL,
                              `meta_desc` text NOT NULL,
                              PRIMARY KEY (`page_id`)
                            ) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8');  

       
        //initialisation
        $this->addInitialSql('INSERT INTO `TABLE_PREFIXpage` (`title`,`link_url`,`text_markdown`,`text_html`,`type_id`,`page_access`,`status`) '.
                             'VALUES("Home","home","Hello world","Hello world","STANDARD","NONE","OK")');
        

        //updates use time stamp in ['YYYY-MM-DD HH:MM'] format, must be unique and sequential
        
        $this->addUpdateSql('2019-01-01 12:00','ALTER TABLE `TABLE_PREFIXmenu` 
                            CHANGE COLUMN `menu_id` `id` INT(11) NOT NULL AUTO_INCREMENT ,
                            CHANGE COLUMN `menu_id_parent` `id_parent` INT(11) NOT NULL');

        $this->addUpdateSql('2019-08-01 12:00','ALTER TABLE `TABLE_PREFIXpage` 
                             ADD COLUMN `link_url` VARCHAR(250) NOT NULL AFTER `meta_desc`');

        $this->addUpdateSql('2019-09-15 12:00','ALTER TABLE `TABLE_PREFIXpage` 
                            ADD COLUMN `page_access` VARCHAR(64) NOT NULL AFTER `link_url`');

        $this->addUpdateSql('2019-09-15 12:10','UPDATE `TABLE_PREFIXpage` SET `page_access` = "NONE"');

                            
    }
}


  
?>
