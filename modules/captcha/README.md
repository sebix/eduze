### Initialize in your controller ###
    public function action_signup()
    {
        $captcha = new Captcha; // Or new Captcha('alpha'), new Captcha('math'), ...
        ...
        
### Render captcha field in your view ###
    <label for="captcha"><?php echo $captcha; ?></label>
    <br />
    <input id="captcha" name="captcha" dir="ltr" title="Key in the charaters or answer the question above" type="text">

### Validate captcha ###
    $valid = new Validate($_POST);

    $valid->filter(TRUE, 'trim')
        ->rule('captcha', 'not_empty')
        ->rule('captcha', 'Captcha::valid');
        
### Want more detail ? ###
[Captcha docs](http://docs.kohanaphp.com/libraries/captcha) from kohanaframework.org