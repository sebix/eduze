<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 *
 * @author     Sebastian Wagner
 * @copyright  (c) 2008-2009 Sebastian Wagner
 */
class Controller_Feed extends Controller {
	
	function action_index($limit = 10) {
		$blog = new Model_Blog;

		if ($limit > 50 || (int)$limit < 1)
			$limit = 10;

			// Entries aus DB holen
		$entries = $blog->getEntries(0, $limit,'feed', 0, 0);
			// view
//		var_dump($entries); die();
		$view = View::factory('feed');
		$view->entries = $entries;
		$view->title = '';
		$view->feed_link = '';
		$this->request->response = $view;
	}

	function action_category($category = '', $limit = 10) {
		$db = Database::instance();
		$xebi = new Helper_Sebix;
		$blog = new Model_Blog;

		if ($limit > 50 || (int)$limit < 1)
			$limit = 10;

		if (!empty($category)) {
			$cat = $blog->getCat($category);
			if ($cat) {	// kat gefunden
				$entries = $blog->getEntries(0, $limit,'feed', $cat['id'], 0);
					// view
				$view = View::factory('feed');
				$view->title = 'Kategorie ' . $cat['name'];
				$view->feed_link = 'category/'.$cat['name'];
				$view->entries = $entries;
			} else {	// keine cat
				$view = View::factory('blank');
				$view->title = 'Fehler';
				$view->message = 'Fehler';
				$view->text = 'Keine passende Kategorie gefunden!<br />' . user::anchor('category/', 'Zur Kategorie-Übersicht', 'Gehe zur Kategorie-Übersicht');
			}
		} else {		// sonst übersicht der kats zeigen
			$query = $blog->getCats();
			$view = View::factory('feed');
			$view->title = 'Kategorie Übersicht';
			$view->message = 'Kategorie Übersicht';
			$view->text = '';
			foreach ($query as $row) {
				$id = $row->id;
				$help = $id;
				for (; ($help / 10) > 1;$help /= 10)
					$view->text .= '&nbsp;';
					
				$view->text .= $id . " " . $xebi->myAnchor('feed/category/'.$row->name,$row->name,'Gehe zur Kategorie ' . $row->name) . "<br />\n";
			}
		}
		$this->request->response = $view;
	}



function action_tag($tag = '', $limit = 10) {
		$db = Database::instance();
		$blog = new Model_Blog;

		if ($limit > 50 || (int)$limit < 1)
			$limit = 10;

		if (!empty($tag)) {
				// anzahl posts
			$numPosts = $blog->numEntries(0,$tag);
			if ($numPosts) {
				$entries = $blog->getEntries(0, $limit, 'feed', 0,$tag);
					// view
				$view = View::factory('feed');
				$view->title = "Tag: " . $tag;
				$view->feed_link = "tag/".$tag;
				$view->entries = $entries;
//				$view->debug = $debug;
			} else {
				// keine einträge gefunden (zum passenden bereich)
				$view = View::factory('blank');
				$view->title = 'Fehler';
				$view->message = 'Fehler';
				$view->text = 'Keine passenden Einträge gefunden!<br />' . user::anchor('/', 'Zur Übersicht','Gehe zur Übersicht');
			}
		} else {		// sonst fehler zeigen
				$view = View::factory('blank');
				$view->title = 'Tags Suche';
				$view->message = 'Tags Suche';
				$view->text = 'Es wurde kein Tag angegeben!<br />' . user::anchor('/', 'Zur Übersicht','Gehe zur Übersicht');
		}
		$this->request->response = $view;
	}


	function action_comments ($post = '', $limit = 10) {
		$db = Database::instance();
		$xebi = new Helper_Sebix;
		$blog = new Model_Blog;

		if ($limit > 50 || (int)$limit < 1)
			$limit = 10;
	
		if (empty ($post)) {	// alle comments
			// Post-Daten aus DB holen
			$query = $blog->getComments(0,0,$limit);
		} elseif ((int)$post > 0) {	// id übergeben
			$query = $blog->getComments($post,0,$limit);
		} else {	// kurz name übergeben
			$query = $blog->getComments(0,$post,$limit);

		}
			// view
		$view = View::factory('feed');
		$view->entries = $query;
		$view->title = 'Kommentare: ' . $post;
		$view->feed_link = '';
		$this->request->response = $view;
	}

}
