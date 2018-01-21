
<?php

class WpGetUpdater {
 
    private $plugin_name; 
    private $plugin_file_data;
    private $wpget_api_url;
 
    function __construct(  )
    {   
        define('SLUG','wpplugin');
        define('PLUGIN_FILE','wpplugin.php');
        define('PLUGIN_DIR','wpplugin');

        $this->init_vars();


        add_filter( "pre_set_site_transient_update_plugins", array( $this, "pre_set_site_transient_update_plugins" ) );
        add_filter( "plugins_api", array( $this, "plugins_api" ), 10, 3 );

        //add_filter( "plugins_api_result", array( $this, "plugins_api_result"), 10,3); 
        //add_filter( "plugins_api_args", array( $this, "plugins_api_args"), 10,2); 
        
        // after updare
        //add_filter( "upgrader_post_install", array( $this, "upgrader_post_install" ), 10, 3 );
        
        // message after upgrade
        add_action( 'in_plugin_update_message-' . $this->plugin_name, array( &$this, 'in_plugin_update_message' ) );

        
    }

    private function get_plugin_file_data()
    {
        $default_headers = array(
            'Name'          => 'Plugin Name',
            'PluginURI'     => 'Plugin URI',
            'Version'       => 'Version',
            'Description'   => 'Description',
            'Author'        => 'Author',
            'AuthorURI'     => 'Author URI',
            'TextDomain'    => 'Text Domain',
            'DomainPath'    => 'Domain Path',
            'Network'       => 'Network',
            
        );

        $f = WP_PLUGIN_DIR . '/' . plugin_basename(dirname(__FILE__)) . '/' . PLUGIN_FILE;

        $this->plugin_file_data = get_file_data($f, $default_headers, 'plugin');
    }
    private function init_vars()
    {
       
        $this->plugin_name = PLUGIN_DIR . '/' . PLUGIN_FILE;
        $this->wpget_api_url = 'http://wpget.net/api/';
      
    }

    // function plugins_api_result($res, $action, $args )
    // {
    //     return $res;
    // }

    // function plugins_api_args($args, $action )
    // {
    //     return $args;
    // }
    
    function upgrader_post_install($response, $hook_extra, $result)
    {
        // Remember if our plugin was previously activated
        $was_activated = is_plugin_active( SLUG );

        global $wp_filesystem;
        $plugin_folder = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . dirname( SLUG );
        $wp_filesystem->move( $result['destination'], $plugin_folder );
        $result['destination'] = $plugin_folder;

        // Re-activate plugin if needed
        if ( $was_activated )
        {
            $activate = activate_plugin( SLUG );
        }
        
        return $result;

    }

    function in_plugin_update_message()
    {
        echo 'message after update';
    }
    
    public function get_remote_version()
    {
		$request = wp_remote_post( $this->wpget_api_url, array( 'body' => array( 'action' => 'info' ) ) );
        if ( ! is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) === 200 )
        {
            $body =  unserialize( base64_decode( $request['body'] ) );
			return $body['version'];
		}

		return false;
    }
    
    private function get_remote_information()
    {
        $request = wp_remote_post( $this->wpget_api_url, array( 'body' => array( 'action' => 'info' ) ) );
        if ( ! is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) === 200 )
        {
			return unserialize( base64_decode( $request['body'] ) );
		}

        return false;
    }

    private function get_plugin_info()
    {
        $repoinfo = $this->get_remote_information();
              
        if ($repoinfo === false) return false;

        $obj = new stdClass();
        $obj->slug = SLUG;
        
        // TODO: populate class with remote repo info
        

        $obj->new_version = '4.0.0';
        $obj->url = 'http://www.wpget.org/';
        $obj->package = 'http://www.wpget.org/wpfile.zip';
        $obj->upgrade_notice = '<ul><li>[Improvement] New changes made in version 4.0 were causing problem at websites running on PHP version less than 5.0</li></ul>';
        $obj->icons = array(
            '1x'        => 'https://ps.w.org/amazon-web-services/assets/icon-128x128.png?rev=1024513',
            '2x'        => 'https://ps.w.org/amazon-web-services/assets/icon-256x256.png?rev=1024513',
            'default'   => 'https://ps.w.org/amazon-web-services/assets/icon-256x256.png?rev=1024513'
        );
        $obj->banners = array(
            '1x'         => 'https://ps.w.org/amazon-web-services/assets/banner-772x250.jpg?rev=776112',
            '2x'         => 'https://ps.w.org/amazon-web-services/assets/banner-1544x500.jpg?rev=776112',
            'default'    => 'https://ps.w.org/amazon-web-services/assets/banner-1544x500.jpg?rev=776112'
        );

        $obj->banners_rtl = array();
        $obj->sections = array(
            'description' => 'The new version of the plugin',
            'another_section' => 'This is another section',
            'changelog' => 'Some new features'
        );

        // $obj->download_link = 'http://www.wpget.org/test.zip';

        // $obj->versions = array(
        //     '1.0.0' => 'http://www.wpget.org/test.1.0.0.zip',
        //     '1.0.1' => 'http://www.wpget.org/test.1.0.1.zip',
        //     '1.1.0' => 'http://www.wpget.org/test.1.1.0.zip',        
        // );

        return $obj;
    }
    
    // Push in plugin version information to get the update notification
    public function pre_set_site_transient_update_plugins( $transient )
    {
        // Extra check for 3rd plugins
        if ( isset( $transient->response[ SLUG ] ) )
        {
			return $transient;
        }
        
        // Get the remote version
		$remote_version = $this->get_remote_version();

		// If a newer version is available, add the update
        if ( $remote_version !== false &&  version_compare( $this->plugin_file_data['Version'], $remote_version, '<' ) )
        {
            $ret = $this->get_plugin_info();

            if ($ret !== false)
            {
                $transient->response[ $this->plugin_name ] = $ret;
            }
        
		}

        return $transient;
    }
 
    // Push in plugin version information to display in the details lightbox
    public function plugins_api( $false, $action, $response )
    {
        if ( empty( $response->slug ) || $response->slug != SLUG )
        {
            return $false;
        }

        $plugin_info = new stdClass();
        
        $plugin_info->slug = SLUG;
       
        $plugin_info->new_version   = '4.0';
        $plugin_info->url           = 'http://www.wpget.org/';
        $plugin_info->package       = 'http://www.wpget.org/wpfile.zip';
        
        $plugin_info->sections = array(
            'description'       => 'The new version of the Auto-Update plugin',
            'another_section'   => 'This is another section',
            'changelog'         => 'Some new features'
        );

        $plugin_info->name = vc_updater()->title;

        return $plugin_info;
        
    }
}

new WpGetUpdater();

?>