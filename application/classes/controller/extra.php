<?php

class Controller_Extra extends Controller {

	function action_index() {
		$view = View::factory('blank');
		$view->title = "Extras";
		$view->shortName = "/";
		$view->body = html::anchor("extra/viewie","ViewInIE") . "<br />\n" . html::anchor("extra/cookietest","Cookie Test") . "<br />\n" . html::anchor("extra/mail","Mail Test") . "<br />\n" . html::anchor("extra/todo","ToDo Liste");
		$this->request->response = $view;
	}
	
	function action_viewie () {
//		if ($this->request->id == 'off') { // on <-> off
			Cookie::delete('ViewInIE');
//		} else {
			Cookie::set('ViewInIE', true);
			Cookie::$expiration = 0;
			Cookie::$path = '/';
			Cookie::$domain = '';
//		}
		echo "Dump: ";
		var_dump (cookie::get('ViewInIE'));
	}

	function action_cookietest() {
		$cookie = array(
			'name'   => 'test',
			'value'  => 'yes',
			'expire' => '0',
			'path'   => '/',
			'domain' => '',
			0
		               );
		if ($this->uri->segment(3) == 'off') {
			if (cookie::delete('ViewInIE'))
				echo 'Cookie gelöscht!';
			else
				echo 'nicht gelöscht';
		} else {
			echo cookie::set($cookie);
			if (cookie::set($cookie))
				echo 'Cookie gesetzt!';
			else
				echo 'nicht gesetzt!';
		}
		echo "\n<br/>Dump: ";
		var_dump (cookie::get('test'));
	}
	
	function action_mail() {
		$view = View::factory('blank');
		$view->title = "Extras - Mailtest";
		$view->text = "<form action='./mail' method='POST'>
				<input name='email' type='text'></input>
				</form>";
		
		$pattern = "%^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$%";
		
		@$email = $_POST['email'];
		$view->text .= "Mail: " . $email . "\n<br />";
		
		if(!preg_match($pattern,$email) ){
			$view->text .= 'Die E-Mail Adresse ist nicht Korrekt';
		} else {
			$view->text .= "Scheint korrekt zu sein!";
		}

		$this->request->response = $view;
	}


	function action_todo () {
		$view = View::factory('blank');

		$view->title = "Extras - ToDo-Liste";
		$view->text = "<ol>
				<li>Inhalt: About-Seiten</li>
				<li>Suche</li>
				<li>Archiv</li>
				<li>Gallerie</li>
				</ol>";

	
		$this->request->response = $view;
	}

	function action_captcha () {
		$view = View::factory('/blank');
		$view->title = "Extras - ToDo-Liste";
		$view->text = "";

		// Look at the counters for valid and invalid
		// responses in the Session Profiler.
//		new Profiler;

		// Load Captcha library, you can supply the name
		// of the config group you would like to use.
		$captcha = new Captcha;

		// Ban bots (that accept session cookies) after 50 invalid responses.
		// Be careful not to ban real people though! Set the threshold high enough.
		if ($captcha->invalid_count() > 49)
			exit('Bye! Stupid bot.');

		// Form submitted
		if ($_POST)
		{
			// Captcha::valid() is a static method that can be used as a Validation rule also.
			if (Captcha::valid($this->input->post('captcha_response')))
			{
				$view->text .= '<p style="color:green">Good answer!</p>';
			}
			else
			{
				$view->text .= '<p style="color:red">Wrong answer!</p>';
			}

			// Validate other fields here
		}

		// Show form
		$view->text .= form::open();
		$view->text .= '<p>Other form fields here...</p>';

		// Don't show Captcha anymore after the user has given enough valid
		// responses. The "enough" count is set in the captcha config.
		if ( ! $captcha->promoted())
		{
			$view->text .= '<p>';
			$view->text .= $captcha->render(); // Shows the Captcha challenge (image/riddle/etc)
			$view->text .= '</p>';
			$view->text .= form::input('captcha_response');
		}
		else
		{
			$view->text .= '<p>You have been promoted to human.</p>';
		}

		// Close form
		$view->text .= form::submit(array('value' => 'Check'));
		$view->text .= form::close();

		$this->request->response = $view;
	}
	
	function action_tests () {
		$view = new View('blank');
		$view->title = "Some output Tests";
		$view->text = 'nofing';//\Sebi\blub();
		$this->request->response = $view;
	}

	function action_tagcloud($max = 0) {
		
		$tags_db = DB::query(Database::SELECT, 'SELECT `ETAGS` AS `tags` FROM `entries`')->execute()->as_array();
		
		$tags = array();
		foreach ($tags_db as $val) {
			foreach (explode(',',$val['tags']) as $value) {
				if (isset($tags[$value]))
					$tags[$value]++;
				else
					$tags[$value] = 1;
			}
		}

		ksort($tags);
		$tmp = $tags;
		arsort($tmp);
		$tmp = array_keys($tmp);
		
		$tags['code'] += 0;

		$highest = 40;
		$tag_values = array_values($tags);
		$tag_keys = array_keys($tags);
		$high = $tags[$tmp[0]];

		$text = '<pre>Highest: ' . $highest .
				'<br />Maximum: ' . $max .
				'<br />Highest count: ' . $high;

		$cloud = '';

		for ($i = 0; $i < count($tags); $i++) {
			$size = floor($tag_values[$i]/($high)*$highest);
			$text .= '<br />' . $i . "\t" . $size . "\t" . $tag_keys[$i];
			$cloud .= '<span style="font-size:' . ($size) . 'px">' . $tag_keys[$i] . '</span> ';
		}

		$view = View::factory('blank');
		$view->title = 'Tagcloud';
		$view->text = $text . '</pre><div style="width:200px;">' . $cloud . '</div>';
		$this->request->response = $view;
	}
	
	function action_teh () {
		$view = View::factory('blank');
		$view->title = 'Textile Editor Helper';
		$view->text = '<script src="/javascripts/prototype.js" type="text/javascript"></script>
		<form action="#">

<textarea cols="40" id="article_body" name="article[body]" rows="20" style="width: 580px; padding: 5px">
h1. Welcome!

This is a demo of the _Textile Editor Helper_ (TEH).  Feel free to play around with it ;-)

For instructions on downloading, please visit the &quot;plugins&quot;:http://slateinfo.blogs.wvu.edu/plugins page.
</textarea>

<link href="/stylesheets/textile-editor.css" media="screen" rel="Stylesheet" type="text/css" />
<script src="/javascripts/textile-editor.js" type="text/javascript"></script>
<script type="text/javascript">
Event.observe(window, "load", function() {
TextileEditor.initialize("article_body", "extended");
});
</script>
';
		$this->request->response = $view;
	}
	
	function action_teh_live () {
		$view = View::factory('blank');
		$view->title = 'Textile Editor Live Preview';
		$view->head = '<script type="text/javascript">
//<![CDATA[
<!--

/*
 * This is the orginial function from Stuart Langridge at http://www.kryogenix.org/
 */
 
/*
* This is the update function from Jeff Minard - http://www.jrm.cc/
*/
function superTextile(s) {
    var r = s;
    // quick tags first
    qtags = [[\'\\*\', \'strong\'],
             [\'\\?\\?\', \'cite\'],
             [\'\\+\', \'ins\'],  //fixed
             [\'~\', \'sub\'],   
             [\'\\^\', \'sup\'], // me
             [\'@\', \'code\']];
    for (var i=0;i<qtags.length;i++) {
        ttag = qtags[i][0]; htag = qtags[i][1];
        re = new RegExp(ttag+\'\\b(.+?)\\b\'+ttag,\'g\');
        r = r.replace(re,\'<\'+htag+\'>\'+\'$1\'+\'</\'+htag+\'>\');
    }
    // underscores count as part of a word, so do them separately
    re = new RegExp(\'\\b_(.+?)_\\b\',\'g\');
    r = r.replace(re,\'<em>$1</em>\');
	
	//jeff: so do dashes
    re = new RegExp(\'[\s\n]-(.+?)-[\s\n]\',\'g\');
    r = r.replace(re,\'<del>$1</del>\');

    // links
    re = new RegExp(\'"\\b(.+?)\\(\\b(.+?)\\b\\)":([^\\s]+)\',\'g\');
    r = r.replace(re,\'<a href="$3" title="$2">$1</a>\');
    re = new RegExp(\'"\\b(.+?)\\b":([^\\s]+)\',\'g\');
    r = r.replace(re,\'<a href="$2">$1</a>\');

    // images
    re = new RegExp(\'!\\b(.+?)\\(\\b(.+?)\\b\\)!\',\'g\');
    r = r.replace(re,\'<img src="$1" alt="$2">\');
    re = new RegExp(\'!\\b(.+?)\\b!\',\'g\');
    r = r.replace(re,\'<img src="$1">\');
    
    // block level formatting
	
		// Jeff\'s hack to show single line breaks as they should.
		// insert breaks - but you get some....stupid ones
	    re = new RegExp(\'(.*)\n([^#\*\n].*)\',\'g\');
	    r = r.replace(re,\'$1<br />$2\');
		// remove the stupid breaks.
	    re = new RegExp(\'\n<br />\',\'g\');
	    r = r.replace(re,\'\n\');
	
    lines = r.split(\'\n\');
    nr = \'\';
    for (var i=0;i<lines.length;i++) {
        line = lines[i].replace(/\s*$/,\'\');
        changed = 0;
        if (line.search(/^\s*bq\.\s+/) != -1) { 
			line = line.replace(/^\s*bq\.\s+/,\'\t<blockquote>\')+\'</blockquote>\'; 
			changed = 1; 
		}
		
		// jeff adds h#.
        if (line.search(/^\s*h[1|2|3|4|5|6]\.\s+/) != -1) { 
	    	re = new RegExp(\'h([1|2|3|4|5|6])\.(.+)\',\'g\');
	    	line = line.replace(re,\'<h$1>$2</h$1>\');
			changed = 1; 
		}
		
		if (line.search(/^\s*\*\s+/) != -1) { line = line.replace(/^\s*\*\s+/,\'\t<liu>\') + \'</liu>\'; changed = 1; } // * for bullet list; make up an liu tag to be fixed later
        if (line.search(/^\s*#\s+/) != -1) { line = line.replace(/^\s*#\s+/,\'\t<lio>\') + \'</lio>\'; changed = 1; } // # for numeric list; make up an lio tag to be fixed later
        if (!changed && (line.replace(/\s/g,\'\').length > 0)) line = \'<p>\'+line+\'</p>\';
        lines[i] = line + \'\n\';
    }
	
    // Second pass to do lists
    inlist = 0; 
	listtype = \'\';
    for (var i=0;i<lines.length;i++) {
        line = lines[i];
        if (inlist && listtype == \'ul\' && !line.match(/^\t<liu/)) { line = \'</ul>\n\' + line; inlist = 0; }
        if (inlist && listtype == \'ol\' && !line.match(/^\t<lio/)) { line = \'</ol>\n\' + line; inlist = 0; }
        if (!inlist && line.match(/^\t<liu/)) { line = \'<ul>\' + line; inlist = 1; listtype = \'ul\'; }
        if (!inlist && line.match(/^\t<lio/)) { line = \'<ol>\' + line; inlist = 1; listtype = \'ol\'; }
        lines[i] = line;
    }

    r = lines.join(\'\n\');
	// jeff added : will correctly replace <li(o|u)> AND </li(o|u)>
    r = r.replace(/li[o|u]>/g,\'li>\');

    return r;
}


function reloadPreviewDiv() {
	var commentString = document.getElementById(\'message\').value;
	var con = superTextile(commentString);
	document.getElementById(\'livecode\').value = con;
	var c = document.getElementById(\'previewcomment\');
	c.innerHTML = con;
}

//-->
//]]>
</script>';
		$view->text = '<table width="100px"  border="0" cellpadding="5" cellspacing="0">
<tr valign="top">
<td width="50">
<h4>Textile</h2>
<p>
	<textarea cols="10" rows="8" id="message" onKeyUp="reloadPreviewDiv()">h1. heading 1

h2. heading 2

h3. heading 3

h4. heading 4

h5. heading 5

h6. heading 6

*bold text*

*bold* *bold* *bold*

_italic text_

_italic_ _italic_ _italic_

_italic_ *bold* _italic_ *bold*

regular text
single break of lines

double breaks as well

* lists
* are
* simple enough
* *plus* formatting _in_ lists works too

# and then there are number lists
# which are great
# for uhm...things, i suppose.

??cited text??

-deleted text-

+inserted text+

~subscript~ and ^superscript^

@and this is teh codezors@

"Links work":http://sdfsdf.com

"Links with title text!(title text)":http://google.com

Images work too:
!http://www.creatimation.net/images/16_tons_256.gif!

</textarea>
	<em>(To get started, click in the box above and type anything.)</em>
</p>
	
<h4>HTML Output</h2>
<p>
	<textarea cols="10" rows="8" id="livecode"></textarea>
</p>
<p>superTextile.js adds:</p>
<ul>
	<li>h tag support</li>
	<li>single line break support</li>
	<li>forces -del- tag to find<br>
			(newline or space)-(something)-(newline or space)<br>
			so it doesn\'t replace dashes in urls/images </li>
    <li>correct list output <br>
    	<em>(last version was leaving &lt;/lio&gt; and &lt;/liu&gt; around) </em></li>
    <li>A little bit cleaner HTML output</li>
</ul></td>
<td>
<h4>Live Preview!</h2>
<div id="previewcomment"></div>
</td>
</tr>
</table>';
		$this->request->response = $view;
	}
}
