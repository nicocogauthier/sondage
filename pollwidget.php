<?php

/**
 * Classe Poll_Widget
 *
 */

class Poll_Widget extends WP_Widget
{
	private $_compteur ;

    /**
     * Constructeur
     */
    public function __construct()
	{
		parent::__construct('poll', 'Sondage', array('description' =>'Un sondage personnalisable.'));
		add_action('admin_menu', array($this,'add_admin_menu'), 20);
		add_action('admin_init', array($this,'register_settings'));
		add_action('wp_loaded', array($this, 'insert_reponse_poll_options'));
		add_action('wp_loaded', array($this, 'ajout_reponse_client'));
		add_action('wp_loaded', array($this, 'reset_bdd')); 
		
	}


	/**
	 * Enregistre une option dans le groupe d'options poll_settings
	 */
	public function register_settings()
	{
		if(isset($_POST['reset_bdd']) && !empty($_POST['reset_bdd'])){
			unregister_setting('poll_settings', 'poll_question');
		}
		else {
			register_setting('poll_settings', 'poll_question');
		}
		

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
		add_action('load-'.$hook, array($this,'process_action_back'));

	}




	/**
	 * Ajoute le html pour le champ  question du formulaire 
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
	}





	/**
	 * Vide les tables wp_poll_options et wp_poll_results
	 */
	public function reset_bdd()	
	{
		if(isset($_POST['reset_bdd']) && !empty($_POST['reset_bdd'])){
			global $wpdb;
			$wpdb->query("DELETE FROM wp_poll_options WHERE 1=1");
			$wpdb->query("DELETE FROM wp_poll_results WHERE 1=1");
		}
	}




	/**
	 * Inscrit la réponse du client dans la base de données wp_poll_results et crée un COOKIE
	 */
	public function ajout_reponse_client()
	{
		if(isset($_POST['reset_bdd']) && !empty($_POST['reset_bdd'])){
				setcookie("vote",NULL,time(),"/wordpress/");
			}

		
		if(isset($_POST['reponse_client']) && !empty($_POST['reponse_client'])){	
			setcookie("vote","oui", time() + 2*24*3600);
			global $wpdb;
			$reponse = $_POST['reponse_client'];
			$row = $wpdb->get_row("SELECT * FROM wp_poll_results WHERE option_id ='$reponse'");
			if(is_null($row)) {			
				$wpdb->insert("wp_poll_results", array('option_id' => $reponse,'total' => $this->_compteur+1));
			}
			else
			{
				$this->_compteur = $row->total + 1;
				$wpdb->update("wp_poll_results", array('total' => $this->_compteur),array('option_id' => $reponse));			
			}
		}
	}




	/**
	 * Définit ce qu'il faut faire lorsqu'on clique sur un des boutons dans l'interface d'administration
	 */ 


	public function process_action_back()
	{
		if(isset($_POST['poll_ajout_reponse'])){
			$this->ajouter_reponse();
		}
		if(isset($_POST['reset_bdd'])){
			$this->reset_bdd();
		}
	}


	/**
	 * Définit ce qu'il faut faire lorsqu on valide le formulaire sur le front office
	 */
	public function process_action_front()
	{
		if(isset($_POST['reponse_client'])){
			$this->ajout_reponse_client();
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

		<form method="post" action="">
			<input type="hidden" name="reset_bdd" value="1" />
			<?php submit_button('Réinitialiser les options et les résultats'); ?>
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

		global $wpdb;
		$recipients_options = $wpdb->get_results("SELECT id,label FROM wp_poll_options");

		if(isset($_COOKIE['vote'])||isset($_POST['reponse_client']) )
		{
?>			
			<h4> Résultats  COucou</h4></br>
<?php	
			$recipients_options = $wpdb->get_results("SELECT id,label FROM wp_poll_options");			
			foreach($recipients_options as $_recipient) { 
				$option_id = $_recipient->id;			
				$total_reponses = $wpdb->get_results("SELECT total FROM wp_poll_results WHERE option_id ='$option_id'");
				foreach($total_reponses as $total_reponse){				
?>			
					<p> <?php echo $_recipient->label.' : '; ?>
					<?php echo $total_reponse->total.' '; ?> vote(s) </p>
<?php
				}	
			}
		}

		else 
		{
?>			
		<h4><?php echo get_option('poll_question');?> </h4></br>
		<form action="" method="post">
		<p>
			<?php foreach ($recipients_options as $_recipient) { ?>
				<label for ="<?php echo $_recipient->id;?>"> <?php echo $_recipient->label;?></label>
				<input type="radio" name="reponse_client" value="<?php echo $_recipient->id;?>" id="<?php echo $_recipient->id;?>"/></br></br>
<?php			
			}
?>		
		</p></br>
		<input type="submit"/>
		</form>
<?php

		}



		echo $args['after_widget'];
    }

    /**
     * Affichage du formulaire dans l'administration widget
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
