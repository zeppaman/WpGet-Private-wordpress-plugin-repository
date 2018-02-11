<?php
namespace WpGet\Updater;

class WpGetUpdater {

    

    /**
     * Plugin file (plugin_file.php)
     * @var string
     */
    public $plugin_file;

    /**
     * Plugin directory (plugin_directory)
     * @var string
     */
    public $plugin_dir;
    
    /**
     * Plugin WP Slug (plugin_directory/plugin_file.php)
     * @var string
     */
    public $plugin_basename;

    /**
     * Plugin slug
     * @var string
     */
    public $plugin_slug;


    /**
     * Plugin file data
     *
     * @var array
     */
    private $plugin_file_data;

    /**
     * wpget url API
     *
     * @var url
     */
    private $wpget_api_url;

    /**
     * wpget Repository Slug
     *
     * @var url
     */
    private $wpget_repo_slug;
    
    /**
     * wpget Package Name
     *
     * @var url
     */
    private $wpget_package_name;
    
    /**
     * WPGet read Token
     *
     * @var string
     */
    private $token;

    
  
    
    function __construct(  )
    {  

        $this->init_vars();

        add_filter( "pre_set_site_transient_update_plugins", array( $this, "wpget_pre_set_site_transient_update_plugins" ) );
        add_filter( "plugins_api", array( $this, "wpget_plugins_api" ), 10, 3 );

        
        
        // message after upgrade
        add_action( 'in_plugin_update_message-' . $this->plugin_basename, array( $this, 'wpget_in_plugin_update_message' ), 10, 2 );

        // pre upgrade
        add_filter( 'upgrader_pre_download', array($this,'wpget_upgrader_pre_download'), 10, 3 ); 
        
        // after upgrade
        add_filter( "upgrader_post_install", array( $this, "wpget_upgrader_post_install" ), 10, 3 );
        
    }
    /**
     * Initialize variables
     *
     * @return void
     */
    private function init_vars()
    {
        $abs_path = path_join( path_join( WP_PLUGIN_DIR, WPGET_PLUGIN_DIR ), WPGET_PLUGIN_FILE );
        $path_parts = pathinfo($abs_path);

        $this->get_plugin_file_data( $abs_path );

        // plugin vars
        $this->plugin_dir           = WPGET_PLUGIN_DIR;
        $this->plugin_file          = WPGET_PLUGIN_FILE;
        $this->plugin_basename      = plugin_basename( $abs_path ); 
        $this->plugin_slug          = $path_parts['filename'];

        // repo vars
        $this->token                = WPGET_TOKEN_READ;
        $this->wpget_api_url        = WPGET_API_URL;
        $this->wpget_package_name   = WPGET_PACKAGE_NAME;
        $this->wpget_repo_slug      = WPGET_REPO_SLUG;


    }

    private function get_plugin_file_data($abs_path)
    {
        $default_headers = array(
            'Plugin Name'   => 'Plugin Name',
            'Plugin URI'    => 'Plugin URI',
            'Version'       => 'Version',
            'Description'   => 'Description',
            'Author'        => 'Author',
            'Author URI'    => 'Author URI',
            'Text Domain'   => 'Text Domain',
            'Domain Path'   => 'Domain Path',
            'Network'       => 'Network',
            
        );


        $this->plugin_file_data = get_file_data($abs_path, $default_headers, 'plugin');

    }
    
    function wpget_upgrader_post_install($response, $hook_extra, $result)
    {
        // Remember if our plugin was previously activated
        
        $was_activated = is_plugin_active( $this->plugin_basename );
         
        // remove .wpget.yml file
        if ( isset( $result['source_files'] ) && is_array( $result['source_files'] ) )
        {
            
            foreach( $result['source_files'] as $index => $file)
            {
                if ( $file === '.wpget.yml' && file_exists( $f = path_join($result['destination'],$result['source_files'][$index]) ) )
                {
                    unlink( $f );
                    break;
                }
            }
            
        }
        
        // TODO: if plugin have to be deactivate and reactivate by yml params 
        // Re-activate plugin if needed
        // if ( $was_activated )
        // {
        //     $activate = activate_plugin( $this->plugin_basename );
        // }
        
        return $result;

    }

    function wpget_in_plugin_update_message($plugin_data,$response)
    {
        echo $response->upgrade_notice;
    }
   

    private function get_remote_info( $version = '' )
    {
        $url = add_query_arg( array(
            'name'      => $this->wpget_package_name,
            'version'   => $version,
            'reposlug'  => $this->wpget_repo_slug
            ), 
            $this->wpget_api_url . 'Catalog/Package'
        );

        $args = array(
            'headers'     => array('Authorization' => 'Bearer ' . $this->token ),
        ); 
        $response = wp_remote_get( $url ,$args);

        if ( ! is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) === 200 )
        {
            if ($response['response']['code'] == '200')
            {
                $body = json_decode( $response['body'] );                
                return !empty($body) ? $body : null ;    
            }
            else
            {
                error_log("Error code: " . $response['response']['code'] . ', message error: ' . $response['response']['message'] );
                return null;
            }
        }

        return null;
    }
        
  
    private function transient_info($plugin_info)
    {
        $transient = new \stdClass();

        $transient->slug            = $this->plugin_slug;
        $transient->plugin          = $this->plugin_basename;
        $transient->new_version     = $plugin_info->version;
        $transient->url             = $plugin_info->homepage; // plugin url
        $transient->package         = $plugin_info->relativepath; // absolute path to file zip

        $transient->icons           = array(
            '1x'            => $plugin_info->icons_1x,
            '2x'            => $plugin_info->icons_2x,
            'default'       => $plugin_info->icons_default
        );

        $transient->banners         = array(
            'low'           => $plugin_info->banners_low,
            'high'          => $plugin_info->banners_high,
            
        );

        $transient->banners_rtl     = array();
        $transient->tested          = $plugin_info->tested;
        $transient->compatibility   = new \stdClass();

        $transient->upgrade_notice  = $plugin_info->upgrade_notice;

        
        return $transient;
    }

    // Push in plugin version information to get the update notification
    public function wpget_pre_set_site_transient_update_plugins( $transient )
    {
        if ( empty( $transient->checked ) )
        {
			return $transient;
        }

        // Get the remote version
        $remote_info = $this->get_remote_info();
        // If a newer version is available, add the update
        if ( $remote_info  &&  version_compare( $this->plugin_file_data['Version'], $remote_info->version, '<' ) )
        {            
            $transient->response[$this->plugin_basename] = $this->transient_info($remote_info);
        }

        return $transient;
    }

    
    // Function called in plugin version information to display in the details lightbox
    public function wpget_plugins_api( $false, $action, $response )
    {
        if ( empty( $response->slug ) || $response->slug != $this->plugin_slug )
        {
            return $false;
        }
        // Get the remote version
        $remote_info = $this->get_remote_info();
        // If a newer version is available, add the update
        if ( $remote_info  &&  version_compare( $this->plugin_file_data['Version'], $remote_info->version, '<' ) )
        {   
            $info = $this->transient_info($remote_info);
        
            // add section
            
            $info->sections = array(
                'description'       => $remote_info->description,
                'installation'      => $remote_info->installation,
                'faq'               => $remote_info->faq,
                'changelog'         => $remote_info->changelog,
                'old_version'       => $remote_info->old_version,
            );

            // other info
            $info->name             = $this->plugin_file_data['Plugin Name'];
            $info->upgrade_notice   = $remote_info->upgrade_notice;

            $info->author           = $remote_info->author;
            $info->author_profile   = $remote_info->author_profile;
            $info->requires_php     = $remote_info->requires_php;


            $info->requires         = $remote_info->requires;
            $info->added            = $remote_info->added;
            $info->homepage         = $remote_info->homepage;
        
            return $info;

        }
        return $false;
        
    }

    // call before download file. 
    // override for download file with token 
    // $package : path to file
    public function wpget_upgrader_pre_download( $false, $package, $instance )
    { 

        $url = add_query_arg( array(
            'name'      => $this->wpget_package_name,
            'version'   => '', // get latest version
            'reposlug'  => $this->wpget_repo_slug
            ), 
            $this->wpget_api_url . 'Catalog/DownloadPackage'
        );

        $args = array(
            'headers'     => array('Authorization' => 'Bearer ' . $this->token ),
        );

        $response = wp_remote_get( $url ,$args);
        
        if ( ! is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) === 200 )
        {
            // save file in temporary folder 
            $zip = $response['body'];

            
            $newfilename = wp_unique_filename( get_temp_dir() , basename($package) );
            $newfilename = get_temp_dir() . $newfilename;
            $newfilename = wp_normalize_path($newfilename);

            // Now use the standard PHP file functions
            $fp = fopen($newfilename, "w");
            fwrite($fp, $zip);
            fclose($fp);

            delete_site_transient( 'update_plugins' );

            return $newfilename; 
        }
            
    }

    
}

new \WpGet\Updater\WpGetUpdater();

?>