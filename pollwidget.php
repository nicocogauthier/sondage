<?php

/**
 * Classe Poll_Widget
 */
class Poll_Widget extends WP_Widget
{
    /**
     * Constructeur
     */
    public function __construct()
	{
		parent::__construct('poll', 'Sondage', array('description' =>'Un sondage personnalisable.'));
		add_action('admin_menu',array($this,'add_admin_menu'), 20);
	}

	/**
	 * Fonction qui crée un sous menu dans l'interface d'administration 
	 */
	public function add_admin_menu()
	{
		add_submenu_page('poll','Sondage','Editer', 'manage_options', 'poll_sondage', array($this,'menu_html')); 
	}

	/**
	 * Fonction affichage du sous menu dans l'interface d'administration
	 */
	public function menu_html()
	{
		echo '<h1>'.get_admin_page_title().'</h1>';
	}


    /**
     * Affichage du widget
     */
    public function widget($args, $instance)
	{	
		echo $args['before_widget'];
		echo $args['before_title'];
		echo apply_filters('widget_title', $instance['title']);
		echo $args['after_title'];
?>
		<form action="" method="post">
			<p>
				<label for="question_sondage"> Question : </label>
				<input id="question_sondage" name="question_sondage" type="text"/>
			</p>
			<input type="submit"/>
		</form>
<?php
		echo $args['after_widget'];
    }

    /**
     * Affichage du formulaire dans l'administration
     */
    public function form($instance)
	{
		$title = isset($instance['title']) ? $instance['title'] : ''; 
?>
		<p>
			<label for="<?php echo $this->get_field_name('title'); ?>">
			<?php _e('Title:'); ?>
			</label>
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>"/>
		</p>
<?php
    }
}
