<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 *
 * @author     Sebastian Wagner
 * @copyright  (c) 2008-2009 Sebastian Wagner
 */
class Controller_Blog extends Controller {

	function action_page($seite = 1) {
		if (!(int)$seite)	// ID ungültig
			$seite = 1;
		$blog = new Model_Blog;
			// Seite
		$start = ($seite-1) * 10;
		$end = ($seite-1) * 10 + 10;
			// Anzahl der Posts insgesammt
		$numPosts = $blog->numEntries();
			// Post-Daten aus DB holen
		$entries = $blog->getEntryList($start,$end);
		if ($entries->count()) {
				// pagination
			$pagination = Pagination::factory(array(
				'total_items'	=> $numPosts,
			));
				// view
			$view = View::factory('blog/overview');
			$view->title = i18n::get('last-posts');
			$view->bottom = $pagination;
			$view->rss = '/feed';
			$view->entries = $entries;
			
		} else {
			$view = View::factory('blank');
			$view->title = i18n::get('error');
			$view->text = i18n::get('no-fitting-posts') . '<br />' . Helper_Sebix::anchor('/', i18n::get('overview'), i18n::get('go-to-overview'));
		}
		$this->request->response = $view;
	}

	function action_category($category = '',$seite = 1) {
		$blog = new Model_Blog();
//		new Profiler();
			// site
		if (!(int)$seite)
			$seite = 1;
		$start = ($seite-1) * 10;
		$end = ($seite-1) * 10 + 10;

		if (!empty($category)) {
				// Hole CATID
			$cat = $blog->getCat($category);
			if ($cat) {	// kat gefunden
					// anzahl posts
				$numPosts = $blog->numEntries($cat['id']);
					// posts holen
				$entries = $blog->getEntryList($start,$end,'std',$cat['id']);
				if ($entries->count()) {	// entries found
						// pagination
					$pagination = Pagination::factory(array(
						'total_items'    => $numPosts,
					));
						// view
					$view = View::factory('blog/overview');
					$view->title = i18n::get('category') . ': ' . $cat['name'];
					$view->rss = '/feed/category/'.$cat['name'];
					$view->bottom = $pagination;
					$view->top = $cat['desc'];
					$view->entries = $entries;

				} else {	// keine einträge gefunden (zum passenden bereich)
					$view = View::factory('blank');
					$view->title = i18n::get('error');
					$view->text = i18n::get('no-fitting-posts') . '<br />' . Helper_Sebix::anchor('category/', i18n::get('category') . '-' . i18n::get('overview'), i18n::get('go-to-category') . '-' . i18n::get('overview'));
				}
			} else {	// keine passende kat gefunden
				
					// alternative kategorie
				$cat = $blog->getCat($category . '%');
				if ($cat) {
					$view = View::factory('blank');
					$view->title = i18n::get('error');
					$view->text = i18n::get('category-not-exist') . '<br />' . i18n::get('do-you-mean') . ' ' . Helper_Sebix::anchor('/category/' . $cat['name'], $cat['name'], i18n::get('go-to-category') . ' ' . $cat['name']) . '?';
				} else {
					$view = View::factory('blank');
					$view->title = i18n::get('error');
					$view->text = i18n::get('category-not-exist') . '<br />' .  sebix::anchor('category/', i18n::get('category') . '-' . i18n::get('overview') . i18n::get('go-to-category') . '-' . i18n::get('overview'));
				}
			}
		} else {		// sonst übersicht der kats zeigen
			$cats = $blog->getCats();
			$view = View::factory('blank');
			$view->title = i18n::get('category') . ' ' . i18n::get('overview');
			$view->text = '';
			foreach ($cats as $row) {
				$id = $row['CATID'];
				$help = $id;
				for (; ($help / 10) > 1;$help /= 10)
					$view->text .= '&nbsp;';
					
				$view->text .= $id . ' ' . Helper_Sebix::anchor('category/'.$row['CATNAME'],$row['CATNAME'], i18n::get('go-to-category') . ' ' . $row['CATNAME']) . '<br />';
			}
		}
		$this->request->response = $view;

	}

	function action_post($category = '',$short_name = '',$top = '',$form = array()) {
		$blog = new Model_Blog;
		
		if ((int)$category == 0) {	// string übergeben / nicht intern
				// kat-id zum namen finden
			$cat = $blog->getCat($category);
			if ($cat) {	// kat exestiert
				$entry = $blog->getEntry($cat['id'],$short_name);
				if ($entry) {// post exestiert
					require_once('modules/markup/classes/textile.php');
					$textile = new Markup_Textile();
					$view = new View('blog/view');
					$view->title = $entry['title'];
					$view->id = $entry['id'];
					$view->body = $textile->TextileThis($entry['body']);
					$view->short_name = $entry['name'];
					$view->author = $entry['author'];
					$view->time = $entry['time'];
					$view->intro = $entry['intro'];
					$view->cat = $entry['cat'];
					$view->captcha = new Captcha('riddle');;
					$view->top = $top;
					$view->rss = '/feed/comments/' . $entry['name'];
					$view->form = $form;
					$view->tags = explode(',',$entry['tags']);
					$view->comments = $blog->getComments($entry['id']);	
				} else {	// angaben stimmen nicht, altnerativ:
					$entry = $blog->getEntry($cat['id'],$short_name . '%');
					if ($entry) {	// wenn post gefunden
						Helper_Sebix::redirect('/post/' . $entry['cat'] . '/' . $entry['name']);
					} else {	// nichts gefunden
						$view = View::factory('blank');
						$view->title = i18n::get('error');
						$view->text = i18n::get('no-fitting-post') . '<br />' . Helper_Sebix::anchor('/', i18n::get('overview'), i18n::get('go-to-overview'));
					}
				}
			} else {	// alternative kategorie
				$cat = $blog->getCat($category . '%');
				if ($cat) {
					$view = View::factory('blank');
					$view->title = 'Fehler';
					$view->text = i18n::get('category-not-exist') . '<br />' . i18n::get('do-you-mean') . Helper_Sebix::anchor('/category/' . $cat['name'], $cat['name'], i18n::get('go-to-category') . ' ' . $cat['name']) . '?';
				} else {
					$view = View::factory('blank');
					$view->title = 'Fehler';
					$view->text = i18n::get('category-not-exist') . '<br />' .  sebix::anchor('category/', i18n::get('category') . '-' . i18n::get('overview') . i18n::get('go-to-category') . '-' . i18n::get('overview'));
				}
			}
		} else {
			if ($top) {		// interner aufruf
				$entry = $blog->getEntry(0,0,$category);
				$view = View::factory('blog/view');
				$view->title = $entry['title'];
				$view->id = $entry['id'];
				$view->body = $entry['body'];
				$view->short_name = $entry['name'];
				$view->author = $entry['author'];
				$view->time = $entry['time'];
				$view->intro = $entry['intro'];
				$view->cat = $entry['cat'];
				$view->captcha = $this->captcha;
				$view->top = $top;
				$view->form = $form;
				$view->tags = explode(',',$entry['tags']);
				$view->comments = $blog->getComments($entry['id']);
			} else {
				$view = View::factory('blank');
				$view->title = i18n::get('error');
				$view->text = i18n::get('no-fitting-post') . '<br />' . sebix::anchor('/', i18n::get('overview'), i18n::get('go-to-overview'));
			}
		}
		$this->request->response = $view;
	}
	

	
	function action_comment_insert($id) {

		$this->captcha = new Captcha('riddle');
		$valid = new Validate($_POST);

		if ($this->captcha->invalid_count() > 20)
			exit('Bye! Stupid bot.');
		if ($id < 1 OR (int)$id < 1)
			exit('Hacker!');
		
		$valid->rule('body', 'trim');
		$valid->rules('body', array('trim' => array(),
									'not_empty' => array(),
									'min_length' => array(20),
									'max_length' => array(10000),
									'htmlspecialchars' => array(),
									'nl2br' => array(),
								));
		$valid->rules('author', array(	'trim' => array(),
										'not_empty' => array(),
										'min_length' => array(3),
										'max_length' => array(50),
										'htmlspecialchars' => array(),
								));
		$valid->rules('mail', array('trim' => array(),
									'not_empty' => array(),
									'email' => array(),
									'min_length'  => array(5),
									'max_length' => array(100),
									'htmlspecialchars' => array(),
								));
		$url = trim($_POST['url']);		// falls leer
		if (!empty($url))
			$valid->rules('url', array(	'trim' => array(),
										'url' => array(),
										'min_length' => array(0),
										'max_length' => array(100),
										'htmlspecialchars' => array(),
									));
		$captchavalid = Captcha::valid($_POST['captcha_response']);
		if ($valid->check() == FALSE || !$captchavalid) {		// Falsche Angaben
			$top = '';
			$errors = array();//$valid->errors();
			if (!$captchavalid)
				$top .= 'Falsche Captcha Antwort!';
			foreach ($errors as $value) {
				$top .= $value . '<br />';
			}
			$this->action_post($id, '', '<div class="redbox">' . $top . '</div>',
									array('body' => $_POST['body'],
										'author' => $_POST['author'],
										'mail' => $_POST['mail'],
										'url' => $_POST['url']
									));
		} else {
			$insert['eid'] = $id;
			$insert['cbody'] = $_POST['body'];
			$insert['cauthor'] = $_POST['author'];
			$insert['cmail'] = $_POST['mail'];
			$insert['curl'] = $_POST['url'];
			$db->insert('comments',$insert);
			$this->action_post($id, '', '<div class="greenbox">Der Kommentar wurde hinzugefügt!</div>');
		}

	}

function action_tag($tag = '',$seite = 1) {
		$blog = new Model_Blog;
			// site
		if ((int)$seite == 0)
			$seite = 1;
		$start = ($seite-1) * 10;
		$end = ($seite-1) * 10 + 10;

		if (!empty($tag)) {
				// anzahl posts
			$numPosts = $blog->numEntries(0,$tag);
			if ($numPosts) {
				$entries = $blog->getEntryList($start,$end,'std',0,$tag);
					// pagination
				$pagination = Pagination::factory(array(
					'total_items'    => $numPosts,
				));
					// view
				$view = View::factory('blog/overview');
				$view->title = 'Tag: ' . $tag;
				$view->rss = '/feed/tag/' . $tag;
				$view->bottom = $pagination;
				$view->entries = $entries;

				
			} else {
				// keine einträge gefunden (zum passenden bereich)
				$view = View::factory('blank');
				$view->title = 'Fehler';
				$view->text = i18n::get('no-fitting-posts') . '<br />' . Helper_Sebix::anchor('/', i18n::get('overview'), i18n::get('go-to-overview'));
			}
		} else {		// sonst übersicht der kats zeigen
				$view = View::factory('blank');
				$view->title = 'Tags Suche';
				$view->text = 'Es wurde kein Tag angegeben!<br />' . Helper_Sebix::anchor('/', 'Zur Übersicht','Gehe zur Übersicht');
		}
		$this->request->response = $view;

	}
}
