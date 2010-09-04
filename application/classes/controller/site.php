<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Site extends Controller {
	
	function action_index($site = "")
	{
		$hsebix = new Helper_Sebix();
//		$db = Database::instance();

		if ($site != "") {
			$sites[] = "sebi";
			$sites[] = "accessibility";
			$sites[] = "sitemap";
			$sites[] = "partner";
			$sites[] = "impressum";
			$sites[] = "disclaimer";
			$sites[] = "feedinfo";
			
			foreach ($sites as $key => $value) {
				if ($site == $value) {
					$query = DB::query(Database::SELECT, 'SELECT `id`,`shortName`,`title`,`body` FROM `static` WHERE `id` = :key');
					$query->param(':key', $key);
					$query = $query->execute()->current();
//					var_dump($query);
//					die();
				}
			}
			$view = View::factory('blank');
			if (isset($query)) {
				$view->title = $query['title'];
				$view->message = $query['title'];
				$view->shortName = $query['shortName'];
				$view->title = $query['title'];
				$view->text = $hsebix->bb($query['body']);
				
			} else {
				$view->title = 'Fehler';
				$view->message = 'Fehler!';
				$view->text = "Diese Seite exestiert nicht!<br />" . $hsebix->anchor("about/", "Zur &Uuml;bersicht");
			}
		} else {
			$view = View::factory('blank');
			$view->title = 'Infos über Xebi';
			$view->message = 'Infos über Xebi';
			$view->text = "<ul><li>Ich</li></ul>";
		
		}
		$this->request->response = $view;
	}
}

/* End of file site.php */
/* Location: ./system/application/controllers/site.php */
