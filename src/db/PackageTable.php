<?php
namespace WpGet\db;

class PackageTable extends TableBase
{
    public function  getFieldDefinition()
    { 
        return array(
            'reposlug'          => 'string', // repo slug ( as defined  in WPGET_REPO_SLUG )
            'name'              => 'string', // package name ( as defined  in WPGET_PACKAGE_NAME )
            
            'major'             => 'integer',
            'minor'             => 'integer',
            'build'             => 'integer',
            'version'           => 'string',  // complete version
            
            'added'             => 'string', // plugin update date

            'upgrade_notice'    => 'string', // notice for update
            'tested'            => 'string', // wp version tested 
            
            'homepage'          => 'string', // plugin homepage url
            'author'            => 'string', // author name
            'author_profile'    => 'string', // plugin author page url 
            
            'requires'          => 'string', // wp version required
            'requires_php'      => 'string', // php version required

            'relativepath'      => 'string', // path to file zip

            

            // preview session
            'description'       => 'text',
            'installation'      => 'text',
            'faq'               => 'text',
            'changelog'         => 'text',
            'old_version'       => 'text',

            // preview banner
            'banners_low'       => 'string',
            'banners_high'      => 'string',
            
            // icons
            'icons_1x'          => 'string',
            'icons_2x'          => 'string',
            'icons_default'     => 'string',
            
        );
    }

    
    public function getTableName()
    {
        return "package";
    }
}