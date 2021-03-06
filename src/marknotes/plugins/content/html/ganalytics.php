<?php

namespace MarkNotes\Plugins\Content\HTML;

defined('_MARKNOTES') or die('No direct access allowed');

class GAnalytics
{
    /**
     * Add new scripts in the <script> part of the page; add the Google Analytics script
     */
    public static function addJS(&$js = null)
    {
        $aeSettings = \MarkNotes\Settings::getInstance();

        $arr = $aeSettings->getPlugins();

        $analyticsCode = '';
        if (isset($arr['options'])) {
            if (isset($arr['options']['ganalytics'])) {
                $analyticsCode = $arr['options']['ganalytics'];
            }
        }

        if ($analyticsCode !== '') {
			
			if ($aeSettings->getDebugMode()) {
				$js .= "\n<!-- Lines below are added by ".__FILE__."-->";
			}
			
            $js .= "\n<script>(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){ (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o), m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m) })(window,document,'script','https://www.google-analytics.com/analytics.js','ga'); ga('create', '".$analyticsCode."', 'auto'); ga('send', 'pageview');</script>\n";
			
			if ($aeSettings->getDebugMode()) {
				$js .= "<!-- End for ".__FILE__."-->";
			}
			
        }

        return true;
    }

    /**
     * Attach the function and responds to events
     */
    public function bind()
    {
        // No analytics on localhost ("::1" is the IPV6-format)
        if ((in_array($_SERVER['REMOTE_ADDR'], array('127.0.0.1', "::1"))) || ($_SERVER['SERVER_NAME'] === 'localhost')) {
            // Just return, don't add the Google analytics code
            return true;
        }

        $aeSession = \MarkNotes\Session::getInstance();
        $task = $aeSession->get('task', '');
        // The ganalytics plugin is not needed when the task is f.i. 'display' (i.e. when the note
        // is displayed through the interface) or when exporting the note to pdf
        if (in_array($task, array('display','pdf'))) {
            return true;
        }

        $aeEvents = \MarkNotes\Events::getInstance();
        $aeEvents->bind('render.js', __CLASS__.'::addJS');
        return true;
    }
}
