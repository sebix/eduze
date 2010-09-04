<?php defined('SYSPATH') or die('No direct script access.');

//namespace Namespaces;

function autoloader($cName)
{
  if (strpos($cName,"\\") !== false)
  {
    $path = strtolower('classes/'.str_replace('\\','/',$cName).'.php');
    if (file_exists(APPPATH.$path))
    {
      include(APPPATH.$path);
    } else {
      $modules = Kohana::modules();
      foreach($modules as $module)
      {
        if (file_exists($module.$path))
        {
          include($module.$path);
          break;
        }
      }
    }
  }
}

spl_autoload_register('Namespaces\autoloader',true,true);

?>
