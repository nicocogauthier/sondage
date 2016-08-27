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
		add_action('admin_init',array($this,'register_settings'));
	}


	/**
	 * Enregistre une option dans le groupe d'options poll_settings
	 */
	public function register_settings()
	{
		register_setting('poll_settings', 'poll_question');
		add_settings_section('poll_section','Paramètres',array($this,'section_html'), 'poll_settings');
		add_settings_field('poll_question','Question', array($this,'question_html'), 'poll_settings', 'poll_section');
		add_settings_field('poll_ajout_reponse', 'Ajouter une nouvelle réponse', array($this,'ajout_reponse_html'), 'poll_settings', 'poll_section');
	}



	/**
	 * Renseigne l'introduction au formulaire
	 */
	public function section_html()
	{
		echo 'Renseignez les paramètres du formulaire';
	}



	/**
	 * Fonction qui crée un sous menu dans l'interface d'administration 
	 */
	public function add_admin_menu()
	{
		add_submenu_page('poll','Sondage','Editer', 'manage_options', 'poll_sondage', array($this,'menu_html')); 
	}



	/**
	 * Définit la question du formulaire 
	 */
	public function question_html()	
	{
?>
		<input type="text" name="poll_question" value="<?php echo get_option('poll_question');?>" />
<?php
	}


	/**
	 * Ajoute une réponse au formulaire 
	 */
	public function ajout_reponse_html()
	{		
?>	
		<input type="text" name="poll_ajout_question" value="<?php echo get_option('poll_ajout_question');?>" />
<?php
	}



	/**
	 * Fonction affichage du sous menu dans l'interface d'administration
	 */
	public function menu_html()
	{
		echo '<h1>'.get_admin_page_title().'</h1>';
?>
		<form method="post" action="options.php">
		<?php settings_fields('poll_settings'); ?>
		<?php do_settings_sections('poll_settings');
		/*	<label> Question : </label>
			</br>
			<label> Ajouter une nouvelle réponse : </label>
			<input type="text" name = "ajout_reponse" value="<?php echo get_option('ajout_reponse');?>"/> */ ?>
			<?php submit_button(); ?>
<?php

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
