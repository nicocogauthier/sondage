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
		add_action('admin_menu', array($this,'add_admin_menu'), 20);
		add_action('admin_init', array($this,'register_settings'));
		add_action('wp_loaded', array($this, 'insert_reponse_poll_options'));
	}


	/**
	 * Enregistre une option dans le groupe d'options poll_settings
	 */
	public function register_settings()
	{
		register_setting('poll_settings', 'poll_question');
		add_settings_section('poll_section','Paramètres',array($this,'section_html'), 'poll_settings');
		add_settings_field('poll_question','Question', array($this,'question_html'), 'poll_settings', 'poll_section');
					
			
	global $wpdb;
		$recipients = $wpdb->get_results("SELECT id,label FROM wp_poll_options");
		foreach ($recipients as $_recipient) {
			register_setting('poll_settings','poll_ajout_reponse_'.$_recipient->id);
			add_settings_field('poll_ajout_reponse_'.$_recipient->id, '', array($this, 'ajout_reponse_bdd'), 'poll_settings', 'poll_section',array($_recipient->id, $_recipient->label));
		}
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
		$hook = add_submenu_page('poll','Sondage','Editer', 'manage_options', 'poll_sondage', array($this,'menu_html')); 
		add_action('load-'.$hook, array($this,'process_action'));
	}



	/**
	 * Définit le html pour le champ  question du formulaire 
	 */
	public function question_html()	
	{
?>
		<input type="text" name="poll_question" value="<?php echo get_option('poll_question');?>" />
<?php
	}


	/**
	 * Ajoute le html pour le champ  réponse au formulaire 
	 */
	public function ajout_reponse_html()
	{		
?>	
		<input type="text" name="poll_ajout_reponse" value="<?php echo get_option('poll_ajout_reponse');?>" />
<?php
	}


	/*
	 * Ajoute le html pour une reponse provenant de la bdd poll_options
	*/
	public function ajout_reponse_bdd($args)
	{
?>
		<input type="text" name="<?php echo 'poll_ajout_reponse_'.$args[0]?> " value="<?php echo $args[1];?>" />
<?php
	}



	/** Inscrire la nouvelle réponse dans la bdd
	 *
	 */
	public function insert_reponse_poll_options()
	{
		if(isset($_POST['poll_ajout_reponse']) && !empty($_POST['poll_ajout_reponse'])){
			global $wpdb;
			$reponse = $_POST['poll_ajout_reponse'];

			$row = $wpdb->get_row("SELECT * FROM wp_poll_options WHERE label ='$reponse'");
			if(is_null($row)) {
				$wpdb->insert("wp_poll_options", array('label' => $reponse));
			}

		}
	}



	/**
	 * Ajouter la nouvelle réponse au formulaire
	 */
	public function ajouter_reponse()
	{
		$this->insert_reponse_poll_options();
		global $wpdb;
		$recipients = $wpdb->get_results("SELECT label FROM wp_poll_options");
		foreach ($recipients as $_recipient) {
			
		}
	}




	/**
	 * Définit ce qu'il faut faire lorsqu'on souhaite enregister une nouvelle réponse
	 */ 


	public function process_action()
	{
		if(isset($_POST['poll_ajout_reponse'])){
			$this->ajouter_reponse();

		}
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
		<?php do_settings_sections('poll_settings');?>
		<?php submit_button(); ?>
		</form>
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
