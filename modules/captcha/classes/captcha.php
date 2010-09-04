<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Captcha library.
 *
 * $Id: Captcha.php 4072 2009-03-13 17:20:38Z jheathco $
 *
 * @package    Captcha
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class Captcha {

    // Captcha singleton
    protected static $instance;

    // Style-dependent Captcha driver
    protected $driver;

    // Config values
    public static $config = array(
        'style'      => 'basic',
        'width'      => 150,
        'height'     => 50,
        'complexity' => 4,
        'background' => '',
        'fontpath'   => '',
        'fonts'      => array(),
        'promote'    => FALSE,
    );

    /**
     * Singleton instance of Captcha.
     *
     * @return  object
     */
    public static function instance()
    {
        // Create the instance if it does not exist
        empty(Captcha::$instance) and new Captcha;

        return Captcha::$instance;
    }

    /**
     * Constructs and returns a new Captcha object.
     *
     * @param   string  config group name
     * @return  object
     */
    public static function factory($group = NULL)
    {
        return new Captcha($group);
    }

    /**
     * Constructs a new Captcha object.
     *
     * @throws  Kohana_Exception
     * @param   string  config group name
     * @return  void
     */
    public function __construct($group = NULL)
    {
        // Create a singleton instance once
        empty(Captcha::$instance) and Captcha::$instance = $this;

        // No config group name given
        if ( ! is_string($group))
        {
            $group = 'default';
        }

        // Load and validate config group
        if ( ! is_array($config = Kohana::config('captcha.'.$group)))
        {
            $config = Kohana::config('captcha.default');
            $group  = 'default';
        }

        // All captcha config groups inherit default config group
        if ($group !== 'default')
        {
            // Merge config group with default config group
            $config += Kohana::config('captcha.default');
        }

        // Assign config values to the object
        foreach ($config as $key => $value)
        {
            if (isset(Captcha::$config[$key]))
            {
                Captcha::$config[$key] = $value;
            }
        }

        // Store the config group name as well, so the drivers can access it
        Captcha::$config['group'] = $group;

        // If using a background image, check if it exists
        if ( ! empty($config['background']))
        {
            Captcha::$config['background'] = str_replace('\\', '/', realpath($config['background']));

            if ( ! is_file(Captcha::$config['background']))
                throw new Kohana_Exception('Captcha background file ":file" not found', array('file' => Captcha::$config['background']));
        }

        // If using any fonts, check if they exist
        if ( ! empty($config['fonts']))
        {
            Captcha::$config['fontpath'] = str_replace('\\', '/', realpath($config['fontpath'])).'/';

            foreach ($config['fonts'] as $font)
            {
                if ( ! is_file(Captcha::$config['fontpath'].$font))
                    throw new Kohana_Exception('Captcha font file ":file" not found', array('file' => Captcha::$config['fontpath'].$font));
            }
        }

        // Set driver name
        $driver = 'Captcha_Driver_'.ucfirst($config['style']);

        // Initialize the driver
        $this->driver = new $driver;
    }

    /**
     * Validates a Captcha response and updates response counter.
     *
     * @param   string   captcha response
     * @return  boolean
     */
    public static function valid($response)
    {
        // Maximum one count per page load
        static $counted;

        // User has been promoted, always TRUE and don't count anymore
        if (Captcha::instance()->promoted())
            return TRUE;

        // Challenge result
        $result = (bool) Captcha::instance()->driver->valid($response);

        // Increment response counter
        if ($counted !== TRUE)
        {
            $counted = TRUE;

            // Valid response
            if ($result === TRUE)
            {
                Captcha::instance()->valid_count(Session::instance()->get('captcha_valid_count') + 1);
            }
            // Invalid response
            else
            {
                Captcha::instance()->invalid_count(Session::instance()->get('captcha_invalid_count') + 1);
            }
        }

        return $result;
    }

    /**
     * Gets or sets the number of valid Captcha responses for this session.
     *
     * @param   integer  new counter value
     * @param   boolean  trigger invalid counter (for internal use only)
     * @return  integer  counter value
     */
    public function valid_count($new_count = NULL, $invalid = FALSE)
    {
        // Pick the right session to use
        $session = ($invalid === TRUE) ? 'captcha_invalid_count' : 'captcha_valid_count';

        // Update counter
        if ($new_count !== NULL)
        {
            $new_count = (int) $new_count;

            // Reset counter = delete session
            if ($new_count < 1)
            {
                Session::instance()->delete($session);
            }
            // Set counter to new value
            else
            {
                Session::instance()->set($session, (int) $new_count);
            }

            // Return new count
            return (int) $new_count;
        }

        // Return current count
        return (int) Session::instance()->get($session);
    }

    /**
     * Gets or sets the number of invalid Captcha responses for this session.
     *
     * @param   integer  new counter value
     * @return  integer  counter value
     */
    public function invalid_count($new_count = NULL)
    {
        return $this->valid_count($new_count, TRUE);
    }

    /**
     * Resets the Captcha response counters and removes the count sessions.
     *
     * @return  void
     */
    public function reset_count()
    {
        $this->valid_count(0);
        $this->valid_count(0, TRUE);
    }

    /**
     * Checks whether user has been promoted after having given enough valid responses.
     *
     * @param   integer  valid response count threshold
     * @return  boolean
     */
    public function promoted($threshold = NULL)
    {
        // Promotion has been disabled
        if (Captcha::$config['promote'] === FALSE)
            return FALSE;

        // Use the config threshold
        if ($threshold === NULL)
        {
            $threshold = Captcha::$config['promote'];
        }

        // Compare the valid response count to the threshold
        return ($this->valid_count() >= $threshold);
    }

    public function update_response()
    {
        return $this->driver->update_response_session();
    }

    /**
     * Returns or outputs the Captcha challenge.
     *
     * @param   boolean  TRUE to output html, e.g. <img src="#" />
     * @return  mixed    html string or void
     */
    public function render($html = TRUE)
    {
        return $this->driver->render($html);
    }

    /**
     * Magically outputs the Captcha challenge.
     *
     * @return  mixed
     */
    public function __toString()
    {
        return $this->render();
    }

} // End Captcha Class