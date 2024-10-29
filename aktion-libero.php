<?php
/*
Plugin Name: Aktion Libero
Plugin URI: http://freitagsspiel.de/?page_id=7175
Description: Das Widget zeigt die neuesten Nachrichten der »Aktion Libero | Sportblogs gegen Homophobie im Fu&szlig;ball«. &Uuml;ber die CSS-Klasse »aktionlibero« kann das Aussehen gestaltet werden.
Version: 1.2
Author: Harald M&uuml;ller
Author URI: http://freitagsspiel.de/
License: GPLv2
*/

function libero()
{
  $options = get_option("widget_libero");
  if (!is_array($options)){
    $options = array(
      'title' => 'Nachrichten:',
      'news' => '5',
      'chars' => '30',
    );
  }

  // RSS Objekt 
	$daten = '<?xml version="1.0" encoding="UTF-8"?>
	<rss version="2.0">
	<channel>
	<title>Aktion Libero</title>
	<description>Sportblogs gegen Homophobie im Fußball</description>
	<language>de</language>
	<link>http://aktion-libero.de/?feed=rss2</link>
	<lastBuildDate>Wed, 16 Nov 2011 00:00:00 GMT</lastBuildDate>
	<item>
		<title>Homepage</title>
		<description>Website der Aktion</description>
		<link>http://www.aktion-libero.de</link>
	</item>
	</channel>
	</rss>';
  $rss = @simplexml_load_file('http://aktion-libero.de/?feed=rss2');
    if($rss === false){
		$rss = simplexml_load_string($daten);
		echo $after_widget;
    }
?>
<ul class="aktionlibero">
  <?php 
  // maximale Anzahl an News, (Null) zeigt alle
  $max_news = $options['news'];
  // maximale Länge des Titels
  $max_length = $options['chars'];
  // RSS Elemente durchlaufen 
  $cnt = 0;
  foreach($rss->channel->item as $i) { 
    if($max_news > 0 AND $cnt >= $max_news){
        break;
    }
    ?>
  <li class="aktionlibero">
    <?php
    $title = $i->title;
    $length = strlen($title);
    if($length > $max_length){
      $title = substr($title, 0, $max_length)." ...";
    }
    ?>
    <a href="<?=$i->link?>" target="_blank" class="aktionlibero">
    <?=$title?>
    </a> </li>
  <?php 
    $cnt++;
  } 
  ?>
</ul>
<?php  
}

function widget_libero($args)
{
  extract($args);
  
  $options = get_option("widget_libero");
  if (!is_array($options)){
    $options = array(
      'title' => 'Nachrichten:',
      'news' => '5',
      'chars' => '30',
    );
  }
  
  $pfad = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
  echo $before_widget;
  echo $before_title;
  echo $after_title;
  echo '<a href="http://www.aktion-libero.de/" class="aktionlibero"><img src="'.$pfad.'aktion-libero.png" width="100" height="53" style="display:block;padding-bottom:10px;"  class="aktionlibero" /></a><p style="padding-bottom:10px;" class="aktionlibero"><a href="http://www.aktion-libero.de/" class="aktionlibero">Sportblogs gegen Homophobie im Fu&szlig;ball</a></p>';
  echo '<strong class="aktionlibero">'.$options['title'].'</strong><br class="aktionlibero" />';
  libero();
  echo $after_widget;
}

function libero_control()
{
  $options = get_option("widget_libero");
  if (!is_array($options)){
    $options = array(
      'title' => 'Neu:',
      'news' => '5',
      'chars' => '30',
    );
  }
  
  if($_POST['libero-Submit'])
  {
    $options['title'] = htmlspecialchars($_POST['libero-WidgetTitle']);
    $options['news'] = htmlspecialchars($_POST['libero-NewsCount']);
    $options['chars'] = htmlspecialchars($_POST['libero-CharCount']);
    update_option("widget_libero", $options);
  }
?>
<p>
  Dieses Widget zeigt aktuelle Nachrichten der »Aktion Libero | Sportblogs gegen Homophobie im Fu&szlig;ball«.
  <br />
  <br />
  <label for="libero-WidgetTitle">&Uuml;berschrift: </label>
  <br />
  <input type="text" id="libero-WidgetTitle" name="libero-WidgetTitle" value="<?php echo $options['title'];?>" />
  <br />
  <br />
  <label for="libero-NewsCount">Anzahl der angezeigten Nachrichten: </label>
  <br />
  <input type="text" id="libero-NewsCount" name="libero-NewsCount" value="<?php echo $options['news'];?>" />
  <br />
  <br />
  <label for="libero-CharCount">Maximale Zeichenl&auml;nge: </label>
  <br />
  <input type="text" id="libero-CharCount" name="libero-CharCount" value="<?php echo $options['chars'];?>" />
  <br />
  <br />
  <input type="hidden" id="libero-Submit"  name="libero-Submit" value="1" />
</p>
<?php
}

function libero_init()
{
  register_sidebar_widget(__('Aktion Libero'), 'widget_libero');    
  register_widget_control('Aktion Libero', 'libero_control', 300, 200);
}
add_action("plugins_loaded", "libero_init");
?>
