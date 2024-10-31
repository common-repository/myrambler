<?php  
/* Plugin Name: MyRambler
Description: Adds a sidebar widget to show the MyRambler blog counter or MyRambler external Widget.  
Author: Arapov Denis (Rambler)
Version: 0.7
Author URI: http://developers.rambler.ru/
Plugin URI: http://developers.rambler.ru/?id=2078943 
*/    

class MyRambler_RSS_Widget extends WP_Widget {
	function MyRambler_RSS_Widget() {
		$widget_ops = array('classname' => 'widget_myrambler_rss', 'description' => __('Adds a sidebar widget to show the MyRambler RSS counter', 'myrambler') );
		$this->WP_Widget('myrambler_rss', __('MyRambler RSS', 'myrambler'), $widget_ops);
	}
 
	function widget($args, $instance) {
		extract($args, EXTR_SKIP);
 
		echo $before_widget;

		$title = empty($instance['title']) ? __('Read in MyRambler', 'myrambler') : apply_filters('widget_title', $instance['title']);
		$show_comment = empty($instance['show_comment']) ? 0 : 1;
 
		if ( !empty( $title ) ) { echo $before_title . $title . $after_title; };


                echo '<!-- Start Rambler Widget Button --><ul>';

		// Posts
                $url = get_feed_link('rss2');
                $url = urlencode(substr($url, 7));

		echo '<li><a href="http://www.myrambler.ru/subscribe/'.$url.'" title="'.__('Read posts in MyRambler', 'myrambler').'">';
		echo '<img src="http://www.myrambler.ru/cnt/' . $url . '" alt="" border="0" width="88" height="15">';
                echo '</a></li>';

                if ( $show_comment ) {

			// Comments
	                $url = get_feed_link('comments_rss2');
	                $url = urlencode(substr($url, 7));
	
			echo '<li><a href="http://www.myrambler.ru/subscribe/'.$url.'" title="'.__('Read comments in MyRambler', 'myrambler').'">';
			echo '<img src="http://www.myrambler.ru/cnt/' . $url . '" alt="" border="0" width="88" height="15">';
	                echo '</a></li>';

		}

		echo '</li></ul><!-- End Rambler Widget Button -->';


		echo $after_widget;
	}
 
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['show_comment'] = strip_tags($new_instance['show_comment']);
 
		return $instance;
	}
 
	function form($instance) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'show_comment' => '1' ) );
		$title = strip_tags($instance['title']);
		$show_comment = strip_tags($instance['show_comment']);


?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php echo __('Title', 'myrambler') ?>: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" /></label></p>
		<p><label for="<?php echo $this->get_field_id('show_comment'); ?>"><?php echo __('Show comment counter', 'myrambler') ?>: <input id="<?php echo $this->get_field_id('show_comment'); ?>" name="<?php echo $this->get_field_name('show_comment'); ?>" type="checkbox" <?php echo $show_comment ? ' checked="1"' : ''; ?>value="1" /></label></p>
<?php


	}
}


class MyRambler_Widget extends WP_Widget {
	function MyRambler_Widget() {
		$widget_ops = array('classname' => 'widget_myrambler', 'description' => __('Adds a sidebar widget to show MyRambler external Widget', 'myrambler') );
		$this->WP_Widget('myrambler', __('MyRambler Widget', 'myrambler'), $widget_ops);
	}
 
	function widget($args, $instance) {

		extract($args, EXTR_SKIP);

		$title	= empty($instance['title']) ? __('Read in MyRambler', 'myrambler') : apply_filters('widget_title', $instance['title']);
                $height	= empty($instance['height']) ? 200 : apply_filters('widget_title', $instance['height']);
                $url	= $instance['url'];

                echo $before_widget . $before_title . $title . $after_title;

                echo "<style> .nvwidget div {margin: 0px; padding: 0px; border: 0px;}</style>";
                echo '<div class="nvwidget">';
                echo '<script type="text/javascript" src="http://www.myrambler.ru/js/UWA/load.js.php?env=BlogWidget2"></script>';
		echo '<script type="text/javascript">';
		echo 'var BW = new UWA.BlogWidget({moduleUrl:"' . $url . '"});';
		echo 'BW.setPreferencesValues({"details":true, "openOutside":false});';
		echo 'BW.setConfiguration({"title":"'. $title . '", "height":'. $height .', "autoresize": false, "displayTitle": false});';
		echo '</script>';
                echo '</div>';

		echo $after_widget;

	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['url'] = strip_tags($new_instance['url']);
		$instance['height'] = strip_tags($new_instance['height']);
 
		return $instance;
	}
 
	function form($instance) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'url' => '', 'category' => '', height => '200' ) );
		$title = strip_tags($instance['title']);
		$url = strip_tags($instance['url']);
		$height = strip_tags($instance['height']);

		$category = array(
			0	=> 'Основные',
			1	=> 'Новости',
			2	=> 'Бизнес',
			3	=> 'Спорт',
			15	=> 'Авто',
			4	=> 'ТВ, фильмы и музыка',
			6	=> 'Инструменты и технологии',
			7	=> 'Развлечения и игры',
			8	=> 'Жизнь',
			10	=> 'Покупки'
                );

		require_once( ABSPATH . WPINC . '/class-snoopy.php' );
		$sno = new Snoopy();
		$sno->agent = 'WordPress/' . $wp_version;
		$sno->read_timeout = 2;

                $option	= '';
 		$furl	= 'http://www.myrambler.ru/proxy/cache/eco/export-ru.json';
		if( !$sno->fetchtext( $furl )) {
		    die( "alert('Could not connect to lookup host.')" );
		}

		$res = json_decode($sno->results);
                if ( !$res ) {
		    die( "alert('Could not decode json.')" );
		}


                foreach ($category as $key => $value) {
                    $option .= '<option value="'.$key.'">'.$value.'</option>';
                }

?>                
		<script>

			function render(n, cat, i) {

				if (!data[cat]) {
					return false;
				}


				var div = document.getElementById(n);
				var res	= '';
            
				res += '<div style="height: 48px; text-align: center">';
				if (i > 0) {
					res += '<a href="" onclick="return render(\''+n+'\','+cat+','+(i-1)+');" style="text-decoration: none;">&laquo;&laquo;</a>';
				} else {
					res += '&laquo;&laquo;'
				}

				res += ' <a href="" onclick="return choice(\''+n+'\','+cat+','+i+');" title="' + data[cat][i]['title'] + '"><img src="' + data[cat][i]['thumbnail'] +  '" align="absmiddle" border="0"></a> ';

				if (i < data[cat].length - 1) {
					res += '<a href="" onclick="return render(\''+n+'\','+cat+','+(i+1)+');" style="text-decoration: none;">&raquo;&raquo;</a>';
				} else {
					res += '&raquo;&raquo;'
				}

				res += '</div><div style="text-align: justify; margin-bottom: 10px;">' + data[cat][i]['description'] + '</div>';

				div.innerHTML = res;

				return false;
			}

			function choice (n, cat, i) {
				var title	= document.getElementById(n.replace('-widget','-title'));
				var url		= document.getElementById(n.replace('-widget','-url'));

				title.value	= data[cat][i]['title'];
				url.value	= data[cat][i]['link'];

				return false;

			}

			var data = <?php echo $sno->results ?>;


		</script>
		<p><label for="<?php echo $this->get_field_id('category'); ?>"><?php echo __('Category', 'myrambler') ?>: <select class="widefat" id="<?php echo $this->get_field_id('category'); ?>" name="<?php echo $this->get_field_name('category'); ?>" onchange="render('<?php echo $this->get_field_id('widget'); ?>', this.options[this.selectedIndex].value, 0)"><option value="">-</option><?php echo $option; ?></select></label></p>
		<div id="<?php echo $this->get_field_id('widget'); ?>"></div>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php echo __('Title', 'myrambler') ?>: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" /></label></p>
		<p><label for="<?php echo $this->get_field_id('height'); ?>"><?php echo __('Height', 'myrambler') ?>: <input class="widefat" id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" type="text" value="<?php echo attribute_escape($height); ?>" /></label></p>
		<p><input id="<?php echo $this->get_field_id('url'); ?>" name="<?php echo $this->get_field_name('url'); ?>" type="hidden" value="<?php echo attribute_escape($url); ?>" /></p>
<?php


	}
}



function widget_myrss_init() {

        load_plugin_textdomain( 'myrambler', 'wp-content/plugins/myrambler', 'myrambler' );

	register_widget('MyRambler_RSS_Widget');
	register_widget('MyRambler_Widget');
}


// Run our code later in case this loads prior to any required plugins.
add_action('widgets_init', 'widget_myrss_init');
?>
