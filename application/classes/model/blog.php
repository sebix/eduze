<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Blog extends Model {
	
	/**
	 * Returns absolute number of entries
	 * 
	 * @return  int  number of entries in DB
	 */
	public function numEntries($category = 0,$tag = 0) {
		if ($category)
			$query = DB::query(Database::SELECT, 'SELECT COUNT(*) AS `COUNT` FROM `entries` WHERE `ETIME` IS NOT NULL AND `ECAT` LIKE \'' . $category . '%\'');
		elseif ($tag)
			$query = DB::query(Database::SELECT, 'SELECT COUNT(*) AS `COUNT` FROM `entries` WHERE `ETIME` IS NOT NULL AND `ETAGS` LIKE \'%' . $tag . '%\'');
		else
			$query = DB::query(Database::SELECT, 'SELECT COUNT(*) AS `COUNT` FROM `entries` WHERE `ETIME` IS NOT NULL');
		$query = $query->execute()->current();
		return $query['COUNT'];
	}

	/**
	 * Returns list of entries
	 * 
	 * @param   int	    Beginn der Entries
	 * @param   int     Ende
	 * @param   string  timeformat, 'std' or 'feed'
	 * @param   int     category id
	 * @return  object  MySQL Object
	 */
	public function getEntryList($start, $end, $timeformat = 'std', $category = 0,$tag = 0) {
		$time = ($timeformat == 'feed')?('\'%Y-%m-%dT%k:%i:%sZ\''):('\'%e.%m.%Y %k:%i\'');
		if ($category)
			return DB::query(Database::SELECT, 'SELECT DATE_FORMAT(`ETIME`, ' . $time . ') AS `time`, DATE_FORMAT(`EUPDATE`, ' . $time . ') AS `update`, cat.`CATNAME` AS `cat`, `ETITLE` AS `title`, `ENAME` AS `name`, `EINTRO` AS `intro`, `EAUTHOR` AS `author`
FROM `entries` e
JOIN `categories` cat ON cat.`CATID` = e.`ECAT`
WHERE `ETIME` IS NOT NULL AND `ECAT` LIKE \'' . $category . '%\'
ORDER BY `ETIME` DESC
LIMIT '.$start.','.$end)->execute();
		elseif ($tag)
			return DB::query(Database::SELECT, 'SELECT DATE_FORMAT(`ETIME`, ' . $time . ') AS `time`, DATE_FORMAT(`EUPDATE`, ' . $time . ') AS `update`, cat.`CATNAME` AS `cat`, `ETITLE` AS `title`, `ENAME` AS `name`, `EINTRO` AS `intro`, `EAUTHOR` AS `author`
FROM `entries` e
JOIN `categories` cat ON cat.`CATID` = e.`ECAT`
WHERE `ETIME` IS NOT NULL AND `ETAGS` LIKE  \'%' . $tag . '%\'
ORDER BY `ETIME` DESC
LIMIT '.$start.','.$end)->execute();
		else
			return DB::query(Database::SELECT, 'SELECT DATE_FORMAT(`ETIME`, ' . $time . ') AS `time`, DATE_FORMAT(`EUPDATE`, ' . $time . ') AS `update`, cat.`CATNAME` AS `cat`, `ETITLE` AS `title`, `ENAME` AS `name`, `EINTRO` AS `intro`, `EAUTHOR` AS `author`
FROM `entries` e
JOIN `categories` cat ON cat.`CATID` = e.`ECAT`
WHERE `ETIME` IS NOT NULL
ORDER BY `ETIME` DESC
LIMIT '.$start.','.$end)->execute();
	}
	
	/**
	 * Gives info about category
	 * 
	 * @param   id|string    Kategorie ID oder Name
	 * @return  array|false  id und name der Kat, bzw. false if not found
	 */
	public function getCat($cat) {
		if ((int)$cat > 0)
			$query = DB::query(Database::SELECT, 'SELECT `CATID`, `CATNAME`, `CATDESCRIPTION` FROM `categories` WHERE `CATID` = \'' . $cat . '\'')->execute();
		else
			$query = DB::query(Database::SELECT, 'SELECT `CATID`, `CATNAME`, `CATDESCRIPTION` FROM `categories` WHERE `CATNAME` LIKE \'' . $cat . '\'')->execute();
		if ($query->count()) {	// Cat gefunden
			return array(
					'id' => $query[0]['CATID'],
					'name' => $query[0]['CATNAME'],
					'desc' => $query[0]['CATDESCRIPTION']);
		} else {	// nichts gefunden
			return false;
		}
	}

	/**
	 * get a list of all cats
	 * 
	 * @return  object  MySQL Object
	 */
	public function getCats() {
		return DB::query(Database::SELECT, 'SELECT `CATID`, `CATNAME` FROM `categories` ORDER BY `CATSORT`')->execute();
	}

	/**
	 * returns object of one entry
	 * 
	 * @param  int	         category
	 * @param  string        name of entry
	 * @param  int|false     when ID is given
	 * @return object|false  MySQL Object or false
	 */
	public function getEntry($cat,$name,$id = false) {
		if ($id)
			$query = DB::query(Database::SELECT, 'SELECT DATE_FORMAT(`ETIME`, \'%e.%m.%Y %k:%i\') AS `time`, DATE_FORMAT(`EUPDATE`, \'%e.%m.%Y %k:%i\') AS `update`, `CATNAME` AS `cat`, `ETAGS` AS `tags`, `ETITLE` AS `title`, `ENAME` AS `name`, `EINTRO` AS `intro`, `EAUTHOR` AS `author`, `EBODY` AS `body`, `EID` AS `id`
FROM `entries`
JOIN `categories` ON `CATID` = `ECAT`
WHERE `EID` = \'' . $id . '\'')->execute();
		else
			$query = DB::query(Database::SELECT, 'SELECT DATE_FORMAT(`ETIME`, \'%e.%m.%Y %k:%i\') AS `time`, DATE_FORMAT(`EUPDATE`, \'%e.%m.%Y %k:%i\') AS `update`, `CATNAME` AS `cat`, `ETAGS` AS `tags`, `ETITLE` AS `title`, `ENAME` AS `name`, `EINTRO` AS `intro`, `EAUTHOR` AS `author`, `EBODY` AS `body`, `EID` AS `id`
FROM `entries`
JOIN `categories` ON `CATID` = `ECAT`
WHERE `ECAT` =\'' . $cat . '\' AND `ENAME` LIKE \'' . $name . '\'')->execute();
		return $query->current();
	}


	/**
	 * returns object of comments
	 * 
	 * @param  int	    optional: entry id
	 * @return object   MySQL Object
	 */
	public function getComments($eid = 0,$name = 0,$limit = 0) {
		$limit = ($limit)?($limit=' LIMIT ' . $limit):'';
		if ($name)
			$query = DB::query(Database::SELECT, 'SELECT DATE_FORMAT(`CTIME`, \'%e.%m.%Y %k:%i\') AS `time`,`CURL` AS `url`,`CBODY` AS `body`,`CAUTHOR` AS `author`, `CID` AS `id`,e.`EID`, `ENAME` AS `name`, `CATNAME` AS `cat`
FROM `comments` c
JOIN `entries` e ON e.`EID` = c.`EID`
JOIN `categories` cat ON `CATID` = `ECAT`
WHERE `ENAME` LIKE \'' . $name . '%\'
ORDER BY `CID` ASC'.$limit)->execute();
		elseif ($eid)
			$query = DB::query(Database::SELECT, 'SELECT DATE_FORMAT(`CTIME`, \'%e.%m.%Y %k:%i\') AS `time`,`CURL` AS `url`,`CBODY` AS `body`,`CAUTHOR` AS `author`, `CID` AS `id`,e.`EID`, `ENAME` AS `name`, `CATNAME` AS `cat`
FROM `comments` c
JOIN `entries` e ON e.`EID` = c.`EID`
JOIN `categories` cat ON `CATID` = `ECAT`
WHERE c.`EID` LIKE \''.$eid.'\'
ORDER BY `CID` ASC'.$limit)->execute();
		else
			$query = DB::query(Database::SELECT, 'SELECT DATE_FORMAT(`CTIME`, \'%e.%m.%Y %k:%i\')  AS `time`,`CURL` AS `url`,`CBODY` AS `body`,`CAUTHOR` AS `author`, `CID` AS `id`,e.`EID`, `ENAME` AS `name`, `CATNAME` AS `cat`
FROM `comments` c
JOIN `entries` e ON e.`EID` = c.`EID`
JOIN `categories` cat ON `CATID` = `ECAT`
ORDER BY `CID` ASC'.$limit)->execute();
		if ($query->count())
			return $query;
		else
			return false;
	}

	/**
	 * Returns list of entries
	 * 
	 * @param   int	    Beginn der Entries
	 * @param   int     Ende
	 * @param   string  timeformat, 'std' or 'feed'
	 * @param   int     category id
	 * @return  object  MySQL Object
	 */
	public function getEntries($start, $end, $timeformat = 'std', $category = 0,$tag = 0) {
		$time = ($timeformat == 'feed') ? ('\'%Y-%m-%dT%k:%i:%sZ\'') : ('\'%e.%m.%Y %k:%i\'');
		if ($category)
			return DB::query(Database::SELECT, 'SELECT DATE_FORMAT(`ETIME`, ' . $time . ') AS `time`, DATE_FORMAT(`EUPDATE`, ' . $time . ') AS `update`, cat.`CATNAME` AS `cat`, `ETITLE` AS `title`, `ENAME` AS `name`, `EINTRO` AS `intro`, `EAUTHOR` AS `author`, `EBODY` AS `body`
FROM `entries` e
JOIN `categories` cat ON cat.`CATID` = e.`ECAT`
WHERE `ETIME` IS NOT NULL AND `ECAT` LIKE \'' . $category . '%\'
ORDER BY `ETIME` DESC			
LIMIT '.$start.','.$end)->execute();
		elseif ($tag)
			return DB::query(Database::SELECT, 'SELECT DATE_FORMAT(`ETIME`, ' . $time . ') AS `time`, DATE_FORMAT(`EUPDATE`, ' . $time . ') AS `update`, cat.`CATNAME` AS `cat`, `ETITLE` AS `title`, `ENAME` AS `name`, `EINTRO` AS `intro`, `EAUTHOR` AS `author`, `EBODY` AS `body`
FROM `entries` e
JOIN `categories` cat ON cat.`CATID` = e.`ECAT`
WHERE `ETIME` IS NOT NULL AND `ETAGS` LIKE  \'%' . $tag . '%\'
ORDER BY `ETIME` DESC
LIMIT '.$start.','.$end)->execute();
		else
			return DB::query(Database::SELECT, 'SELECT DATE_FORMAT(`ETIME`, ' . $time . ') AS `time`, DATE_FORMAT(`EUPDATE`, ' . $time . ') AS `update`, cat.`CATNAME` AS `cat`, `ETITLE` AS `title`, `ENAME` AS `name`, `EINTRO` AS `intro`, `EAUTHOR` AS `author`, `EBODY` AS `body`
FROM `entries` e
JOIN `categories` cat ON cat.`CATID` = e.`ECAT`
WHERE `ETIME` IS NOT NULL
ORDER BY `ETIME` DESC
LIMIT '.$start.','.$end, 0)->execute();
	}

} // End Blog Mode
