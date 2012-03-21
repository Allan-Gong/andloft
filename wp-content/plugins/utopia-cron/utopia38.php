<?php
/*==============================================================================
Plugin Name: utopia38/U-CRON - Easy Pseudo Cron Jobbys for WordPress
Version: 1.00
Plugin URI: http://www.ActiveBlogging.com/info/wordpress-cron-plugin/
Description: Makes it easy to set up (semi)-timed page load events without messing with cron. Go to Manage/U-Cron subtab for settings after activating
Author: David Pankhurst
Author URI: http://ActiveBlogging.com/
Copyright: Copyright (c) 2007-8 David Pankhurst - Licensed Under Artistic License 2.0 (http://www.gnu.org/philosophy/license-list.html#GPLCompatibleLicenses) 

v1.00
first release

==============================================================================*/
function utopia38_cron()
{
  $data=get_option('utopia38_data');
  $list=(isset($data['l'])?$data['l']:array());
  $total=count($list);
  if (0==$total)
    return;
  // check if time to go anywhere...
  $now=time();
  $tick=array();
  // get next call to make - add a bit of randomness to avoid dup calls   
  for ($i=0;$i<$total;++$i)
    $tick[]=$list[$i]['c']+mt_rand(0,240);
  $value=min($tick);
  if ($value<$now)
  {
    // note: since we choose first match, it's possible to have more than one -
    // however, ALL matches will need to be serviced soon, so any match IS ok
    $i=array_search($value,$tick);
    // set next call time - surrent rounded ot 'proper' call time
    $list[$i]['c']=utopia38_next($list[$i]['t'],$list[$i]['f'],$now);
    $data['l']=$list;
    update_option('utopia38_data',$data);
    $error="";
    // now check and register page load
    utopia38_check($list[$i]['u'],$error);
    return;
  }
}
//------------------------------------------------------------------------------
function utopia38_check($url,&$error)
{
  // load the url to activate it
  // first, snoopy...
  require_once(ABSPATH.WPINC.'/class-snoopy.php');
  $error="";
  $snoop=new Snoopy();
  // note we load a minimal page - saves time/bandwidth, and even a partial 
  // read activates the full page loading at the server's end
  $snoop->maxlength=256;
  $snoop->read_timeout=10;
  $oldLevel=error_reporting(E_ALL^(E_WARNING|E_NOTICE));
  $result=$snoop->fetch($url);
  error_reporting($oldLevel);
  if ($result)
    return TRUE;
  $error=$snoop->error;
  return FALSE;
}
//------------------------------------------------------------------------------
function utopia38_validate($time,$freq,$url)
{
  // make input work - if really bad, return false, else finished entry 
  $now=time();
  if (!preg_match('@^(2[0-3]|[01][0-9]|[0-9]).(\d\d)$@',trim($time),$matches))
    return FALSE;
  $timeValue=intval($matches[1])*3600+intval($matches[2])*60;
  // time can be 0m or 0h format
  if (!preg_match('@^(\d+)\s*([mhMH]?)$@',$freq,$matches))
    return FALSE;
  $freqValue=$matches[1]*60;
  // if 'm' and 'h' not included, assume 'h'
  if ( 'm'!=$matches[2] && 'M'!=$matches[2] )
    $freqValue *= 60;
  // never allow calling frequency of less than 5 minutes
  $freqValue=max(300,$freqValue);
  // get gmt time of call start and clamp to 24 hour day
  $offset=get_settings('gmt_offset')*3600;
  $timeValue=($timeValue-$offset+86400)%86400;
  // now find next gmt time that we 'fire'
  $nextCall=utopia38_next($timeValue,$freqValue,$now);
  $rec=array('t'=>$timeValue,'f'=>$freqValue,'u'=>$url,'c'=>$nextCall);
  return $rec;
}
//------------------------------------------------------------------------------
function utopia38_next($start,$freq,$now)
{
  // figure out next firing time (sec) and return it
  $midnight=$now-($now%86400);
  $startTime=$midnight+$start;
  if ($startTime>$now)
    $startTime -= 86400;
  // get crossover time
  $remainder=($now-$startTime)%$freq;
  $nextTick=$now+($freq-$remainder);
  // avoid pointing back to 'now'
  if ($startTime-$now<30)
    $startTime += $freq;
  return $nextTick;
}
//------------------------------------------------------------------------------
function utopia38_menu()
{
  if (!current_user_can('manage_options'))
  {
    echo '<div class="wrap"><br>This section is only available to the administrator.<br><br></div>';
    exit();
  }
  $data=get_option('utopia38_data');
  $list=(isset($data['l'])?$data['l']:array());
  $total=count($list);
  $quoted=get_magic_quotes_gpc();
  $time="";
  $freq="";
  $url="";
  $msg="";
  $update=FALSE;
  // get input (if any)
  if (isset($_POST['add']))
  {
    $time=trim(isset($_POST["time"])?$_POST["time"]:"");
    $freq=trim(isset($_POST["freq"])?$_POST["freq"]:"");
    $url=trim(isset($_POST["url"])?$_POST["url"]:"");
    if ($quoted)
    {
      $url=stripslashes($url);
    }
    $rec=utopia38_validate($time,$freq,$url);
    if (is_array($rec))
    {
      $list[]=$rec;
      $msg="Entry added";
      $update=TRUE;
    }
    else
    {
      $msg="Invalid inputs";
    }
  }
  else
  {
    // check if one of our keys
    for ($i=0;$i<$total;++$i)
    {
      if (isset($_POST["del$i"]))
      {
        // delete entry & pack array
        array_splice($list,$i,1);
        $msg="Entry deleted";
        $update=TRUE;
        break;
      }
      else if (isset($_POST["edit$i"]))
      {
        // update entry
        $time=trim(isset($_POST["time$i"])?$_POST["time$i"]:"");
        $freq=trim(isset($_POST["freq$i"])?$_POST["freq$i"]:"");
        $url=trim(isset($_POST["url$i"])?$_POST["url$i"]:"");
        if ($quoted)
        {
          $url=stripslashes($url);
        }
        $rec=utopia38_validate($time,$freq,$url);
        if (is_array($rec))
        {
          $list[$i]=$rec;
          $msg="Entry updated";
          $update=TRUE;
        }
        else
        {
          $msg="Invalid inputs - couldn't update";
        }
        break;
      }
      else if (isset($_POST["test$i"]))
      {
        // try and access entry
        $url=$list[$i]['u'];
        if (utopia38_check($url,$msg))
          $msg="Read AOK: $url";
        break;
      }
    }
  }
  // save if needed
  if ($update)
  {
    $data['l']=$list;
    update_option('utopia38_data',$data);
    $total=count($list);
  }
  // now output results
  $homeURL=get_bloginfo('home');
  $color='bgcolor="#E5F3FF"';
  if (!empty($msg))
    $msg='<div style="padding:10px;background:#ffe0e0;font-weight:bold;text-align:center;">'.htmlentities($msg,ENT_QUOTES,'UTF-8').'</div>';
echo <<<HTML_CODE
<div class="wrap"><h2>U-Cron (Utopia Cron) Easy Timed Jobs</h2>
<h3>URL Loading/Checks</h3>
$msg
<style>
<!--
.u38tbl th {background:#83B4D8;}
-->
</style>
<form method='post'>
<table cellpadding="5" cellspacing="0" border="0" align="center" class="u38tbl"><tbody>
<tr><td colspan="7"><h3>Add New Check</h3></td></tr>
<tr>
<th>&nbsp;</th><th align="center">Start Time (hh:mm)</th>
<th align="center">How Often (m/h)</th>
<th align="center">Full URL (WITH http://)</th>
<th align="center">Add</th>
<th>&nbsp;</th>
<th>&nbsp;</th>
</tr>
<tr>
<td $color>&nbsp;</td>
<td align="center" $color><input type="text" value="" name="time" size="6" /></td>
<td align="center" $color><input type="text" value="" name="freq" size="6" /></td>
<td align="center" $color><input type="text" value="" name="url" size="45" /></td>
<td align="center" $color><input type="submit" value="Add" name="add" /></td>
<td $color>&nbsp;</td>
<td $color>&nbsp;</td>
</tr>
HTML_CODE;
  if ($total)
  {
echo <<<HTML_CODE
<tr><td colspan="7"><h3>Current Checks</h3></td></tr>
<tr>
<th align="center">Delete</th>
<th align="center">Start Time (hh:mm)</th>
<th align="center">How Often (m/h)</th>
<th align="center">Full URL (WITH http://)</th>
<th align="center">Update</th>
<th align="center">Test</th>
<th align="center">View</th>
</tr>
HTML_CODE;
    $offset=get_settings('gmt_offset')*3600;
    for ($i=0;$i<$total;++$i)
    {
      // get info
      $time=$list[$i]['t'];
      $freq=$list[$i]['f'];
      $url=$list[$i]['u'];
      // convert to proper local time
      $time=($time+$offset+86400)%86400;
      $time=sprintf('%02d:%02d',intval($time/3600),intval(($time%3600)/60));
      if ($freq%3600)
        $freq=sprintf('%dm',intval($freq/60));
      else
        $freq=sprintf('%dh',intval($freq/3600));
      // now prep for web display
      $time=htmlentities($time,ENT_QUOTES,'UTF-8');
      $freq=htmlentities($freq,ENT_QUOTES,'UTF-8');
      $url=htmlentities($url,ENT_QUOTES,'UTF-8');
      $colorOut=( $i&1 ?  $color : "" );
echo <<<HTML_CODE
<tr>
<td align="center" $colorOut><input type="submit" value="Delete" name="del$i" onclick="return confirm('Are you sure you want to delete?')" /></td>
<td align="center" $colorOut><input type="text" value="$time" name="time$i" size="6" /></td>
<td align="center" $colorOut><input type="text" value="$freq" name="freq$i" size="6" /></td>
<td align="center" $colorOut><input type="text" value="$url" name="url$i" size="45" /></td>
<td align="center" $colorOut><input type="submit" value="Update" name="edit$i" /></td>
<td align="center" $colorOut><input type="submit" value="Test" name="test$i" /></td>
<td align="center" $colorOut><a href="$url" target="_blank">View Page</a></td>
</tr>
HTML_CODE;
    }
  }
echo <<<HTML_CODE
</tbody></table>
</form>
<br /><br />
<h3>How to Use</h3>
This is a simple plugin to approximately time tasks. As visitors load your blog 
pages, this
plugin checks if any pages need loading and does so. By loading these web pages,
you can do useful tasks like checking your email. What you do is:
<ol>
<li><b>Enter the URL of the web page to check.</b><br />
This is the web page to 'load', or trigger. For example, if you wanted to run
your e-mail post checker (as set in <b>Options/Writing/Post</b> via e-mail), you could
try this entry:
<pre>
{$homeURL}/wp-mail.php
</pre>
</li>
<li><b>Enter a starting time for the check.</b><br />
For example you might want to check at <b>2:35 pm</b>, which is entered <b>14:35</b>
(Military time, where the hour goes from 0 to 24). Note for repeated checks just
pick a start time (for example if you want 10 minutes after the hour, just pick
<b>00:10</b> or <b>01:10</b>, etc).
</li>
<li><b>Enter a repeat time for the check.</b><br />
This is how often you want to check - for instance every 10 minutes, 15 minutes,
or even 24 hours to do it just once a day. Make sure you add <b>m</b> or <b>h</b>
after the number to set hours or minutes (for example <b>15m</b> or <b>12h</b> -
if you leave it off, hours are assumed).
</li>
<li><b>Save, then test.</b><br />
Save the entry, then use the <b>Test</b> and <b>View</b> buttons to verify. 
<b>Test</b> will load the page just as U-Cron would, and displays the result 
(<b>AOK</b> or an error message). The <b>View</b> link opens the URL in a 
seperate page, so you can confirm that the page you're loading is the right one.
</li>
</ol>
<h3>Notes</h3>
<ul>
<li>
Your theme MUST have the function <b>wp_footer</b> included in it, like this:
<pre>
&lt;?php wp_footer(); ?&gt;
&lt;/body&gt;
</pre>
(most newer themes already have this in <b>footer.php</b> or possibly 
<b>index.php</b>).
</li>
<li>
This plugin uses blog page views to do the timing - for this reason, times are 
<u>approximate</u> not exact, and if your blog gets few visits, then timing 
will be affected. If you need precise timing, it is best to use a systems 
timer like <b>cron</b>. 
</li>
<li>
Checking URLs frequently (or many URLs) can affect blog performance. This plugin 
prevents you checking a URL more often than once every 5 minutes.
</li>
</ul>
<br /><hr />
Easy cron plugin for WordPress (utopia38/U-Cron) v1.00 &copy; David Pankhurst/
<a href="http://ActiveBlogging.com">ActiveBlogging.com</a>. Licensed Under 
<a href="http://www.gnu.org/philosophy/license-list.html#GPLCompatibleLicenses" 
target="_blank">Artistic License 2.0</a> 
</div>
HTML_CODE;
}
//------------------------------------------------------------------------------
if (!is_plugin_page())
{
  // simple addon - add page to manager panel - no upper panels, no multipage, etc...
  function utopia38_menuAdd()
  {
    add_submenu_page('edit.php','U-Cron','U-Cron',9,'u38_menu','utopia38_menu');
  }
  add_action('admin_menu','utopia38_menuAdd');
}
//------------------------------------------------------------------------------
add_action('wp_footer','utopia38_cron');
//------------------------------------------------------------------------------
?>