<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Outputs the dynamic Captcha resource.
 * Usage: Call the Captcha controller from a view, e.g.
 *        <img src="<?php echo url::site('captcha') ?>" />
 *
 * @package    Captcha
 * @author     Kohana Team, Yahasana
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class Controller_Captcha extends Kohana_Controller {

    public function before()
    {
        $this->driver_name = $this->request->action;
        $this->request->action = 'default';
    }

    public function after()
    {
        Captcha::instance()->update_response();
    }

    public function action_default()
    {
        // Output the Captcha challenge resource (no html)
        // Pull the config group name from the URL
        Captcha::factory($this->driver_name)->render(FALSE);
    }

} // End Captcha_Controller