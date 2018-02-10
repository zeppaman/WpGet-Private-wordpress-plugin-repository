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

    /**
     * wp info plugin data
     *
     * @var StdClass
     */
    private $plugin_info;
    
  
    
    function __construct(  )
    {  

        $this->init_vars();

        add_filter( "pre_set_site_transient_update_plugins", array( $this, "wpget_pre_set_site_transient_update_plugins" ) );
        add_filter( "plugins_api", array( $this, "wpget_plugins_api" ), 10, 3 );

        // add_filter( "plugins_api_result", array( $this, "wpget_plugins_api_result"), 10,3); 
        // add_filter( "plugins_api_args", array( $this, "wpget_plugins_api_args"), 10,2); 
        
        // after updare
        //add_filter( "upgrader_post_install", array( $this, "wpget_upgrader_post_install" ), 10, 3 );
        
        // message after upgrade
        //add_action( 'in_plugin_update_message-' . $this->plugin_slug, array( $this, 'wpget_in_plugin_update_message' ) );

        
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
        $this->token                = 'nGXLbWrR0HLLQHljYZ6mLeOv2ZOhVu';
        $this->wpget_api_url        = WPGET_API_URL . 'Catalog/Package';
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

    

    // function wpget_plugins_api_result($res, $action, $args )
    // {
    //     error_log("***************** FUNCTION: " .__FUNCTION__);
    //     return $res;
    // }

    // function wpget_plugins_api_args($args, $action )
    // {
    //     error_log("***************** FUNCTION: " .__FUNCTION__);
    //     return $args;
    // }
    
    // function wpget_upgrader_post_install($response, $hook_extra, $result)
    // {
    //     //error_log("***************** FUNCTION: " .__FUNCTION__);
    //     // Remember if our plugin was previously activated
    //     $was_activated = is_plugin_active( PLUGIN_SLUG );

    //     global $wp_filesystem;
    //     $plugin_folder = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . dirname( PLUGIN_SLUG );
    //     $wp_filesystem->move( $result['destination'], $plugin_folder );
    //     $result['destination'] = $plugin_folder;

    //     // Re-activate plugin if needed
    //     if ( $was_activated )
    //     {
    //         $activate = activate_plugin( PLUGIN_SLUG );
    //     }
        
    //     return $result;

    // }

    // function wpget_in_plugin_update_message()
    // {
    //     //error_log("***************** FUNCTION: " .__FUNCTION__);
    //     //echo 'message after update';
    // }
    
    public function get_remote_info( $version = '' )
    {
        $url = add_query_arg( array(
            'name'      => $this->wpget_package_name,
            'version'   => $version,
            'reposlug'  => $this->wpget_repo_slug
            ), 
            $this->wpget_api_url
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
        
  
    function transient_info()
    {
        $transient = new \stdClass();

        $transient->slug            = $this->plugin_slug;
        $transient->plugin          = $this->plugin_basename;
        $transient->new_version     = $this->plugin_info->version;
        $transient->url             = $this->plugin_info->url; // plugin url
        $transient->package         = $this->plugin_info->package; // absolute path to file zip

        $transient->icons           = array(
            '1x'            => 'https://ps.w.org/akismet/assets/icon-128x128.png?rev=969272',
            '2x'            => 'https://ps.w.org/akismet/assets/icon-256x256.png?rev=969272',
            'default'       => 'https://ps.w.org/akismet/assets/icon-256x256.png?rev=969272'
        );

        $transient->banners         = array(
            '1x'            => 'https://ps.w.org/akismet/assets/banner-772x250.jpg?rev=479904',
            '2x'            => 'https://ps.w.org/akismet/assets/banner-772x250.jpg?rev=479904',
            'default'       => 'https://ps.w.org/akismet/assets/banner-772x250.jpg?rev=479904',
            'low'           => 'https://ps.w.org/akismet/assets/banner-772x250.jpg?rev=479904',
            'high'          => 'https://ps.w.org/akismet/assets/banner-772x250.jpg?rev=479904',
            
        );

        $transient->banners_rtl     = array();
        $transient->tested          = $this->plugin_info->tested;
        $transient->compatibility   = new \stdClass();


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
        error_log(print_r($remote_info,true));
        // If a newer version is available, add the update
        if ( $remote_info  &&  version_compare( $this->plugin_file_data['Version'], $remote_info->version, '<' ) )
        {
            
            $this->plugin_info = $remote_info;
            $transient->response[$this->plugin_basename] = $this->transient_info();;
       
        }

        return $transient;
    }

    /**
     * Function called in plugin version information to display in the details lightbox
     *
     * @param [type] $false
     * @param [type] $action
     * @param [type] $response
     * @return void
     */
    public function wpget_plugins_api( $false, $action, $response )
    {
        if ( empty( $response->slug ) || $response->slug != $this->plugin_slug )
        {
            return $false;
        }

        $info = $this->transient_info();
        
        // add section
        
        $info->sections = array(
            'description'       => 'The new version of the plugin',
            'installation'      => 'This is another section',
            'faq'               => 'FAQ',
            'changelog'         => 'Some new features',
            'previous_version'  => 'Previous Versionds'
        );

        // other info
        $info->name             = $this->plugin_file_data['Plugin Name'];
        $info->upgrade_notice   = '<ul><li>[Improvement] New changes made in version 4.0 were causing problem at websites running on PHP version less than 5.0</li></ul>';

        $info->author           = "Francesco Minà";
        $info->author_profile   = "https://github.com/zeppaman/WpGet";
        $info->requires_php     = '5.4';
        $info->requires         = '4.0.0'; // wp version required
        $info->added            = '2007-01-21';
        $info->homepage         = 'https://github.com/zeppaman/WpGet';

       
        return $info;
        
    }
}

new \WpGet\Updater\WpGetUpdater();

?>