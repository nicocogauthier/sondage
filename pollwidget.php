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
