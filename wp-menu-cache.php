<?php    
/*
Plugin Name: Pamięć podręczna menu nawigacyjnych
Description: Zapisuje wynikowy kod HTML wygenerowanych menu nawigacyjnych w cache.
Version: 1.0.0
Author: Sebastian Bort
*/

class WP_Menu_Cache {

    const transient_key = '_menu_cache';
    private $menu_cache = [];
                                   
	public function __construct() {
		
       add_action('wp_update_nav_menu', [$this, 'delete_navigation_menu_cache']);          
       add_action('wp_nav_menu', [$this, 'save_navigation_menu_to_cache'], 10, 2);         
       add_action('pre_wp_nav_menu', [$this, 'get_navigation_menu_from_cache'], 10, 2);            
	} 

 	public function delete_navigation_menu_cache() {  
		
        delete_option(self::transient_key);
	}      
    
    private function load_cache() {
        
        if(empty($this->menu_cache)) {
            $this->menu_cache = get_option(self::transient_key);
        }        
    }    
    
    private function get_menu_id($input_data) {
        
        return sprintf('%s%s', 'nav_menu_', md5(serialize($input_data)));
    }
        
	public function save_navigation_menu_to_cache($nav_menu, $args) {
		
        $this->load_cache();
        $menu_id = $this->get_menu_id($args);        
        
        if(empty($this->menu_cache[$menu_id])) {
            $this->menu_cache[$menu_id] = $nav_menu;
            update_option(self::transient_key, $this->menu_cache);
        }  
         
		return $nav_menu;
	}
    
	public function get_navigation_menu_from_cache($output, $args) { 
    
        $this->load_cache();
        $menu_id = $this->get_menu_id($args); 
        
        if(!empty($this->menu_cache[$menu_id])) {
            return $this->menu_cache[$menu_id];
        }        
    
        return null;
	}  
}

new WP_Menu_Cache();

?>