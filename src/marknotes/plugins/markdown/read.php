<?php

namespace MarkNotes\Plugins\Markdown;

defined('_MARKNOTES') or die('No direct access allowed');

class Read
{
    /**
     * Notes written in .md can contains variables.  The function below will translate these variables.
     */
    private static function replaceVariables(string $markdown) : string
    {
        $aeFunctions = \MarkNotes\Functions::getInstance();
        $aeSettings = \MarkNotes\Settings::getInstance();
        $markdown = str_replace('%ROOT%', rtrim($aeFunctions->getCurrentURL(false, false), '/'), $markdown);
        $markdown = str_replace('%DOCS%', rtrim($aeSettings->getFolderDocs(false), DS), $markdown);

        return $markdown;
    }
    /**
     * The markdown file has been read, this function will get the content of the .md file and
     * make some processing like data cleansing
     */
    public static function readMD(&$markdown = null)
    {
        if (trim($markdown) === '') {
            return true;
        }

        // Be sure to have content with LF and not CRLF in order to be able to use
        // generic regex expression (match \n for new lines)
        $markdown = str_replace("\r\n", "\n", $markdown);

        // -----------------------------------------------------------------------
        // URL Cleaner : Make a few cleaning like replacing space char in URL or in image source
        // Replace " " by "%20"

        $matches = array();
        if (preg_match_all('/<img *src *= *[\'|"]([^\'|"]*)/', $markdown, $matches)) {
            foreach ($matches[1] as $match) {
                $sMatch = str_replace(' ', '%20', $match);
                $markdown = str_replace($match, $sMatch, $markdown);
            }
        }

        // And do the same for links
        $matches = array();
        if (preg_match_all('/<a *href *= *[\'|"]([^\'|"]*)/', $markdown, $matches)) {
            foreach ($matches[1] as $match) {
                $sMatch = str_replace(' ', '%20', $match);
                $markdown = str_replace($match, $sMatch, $markdown);
            }
        }

        $markdown = self::replaceVariables($markdown);

        return true;
    }

    /**
     * Attach the function and responds to events
     */
    public function bind()
    {
        $aeEvents = \MarkNotes\Events::getInstance();
        $aeEvents->bind('markdown.read', __CLASS__.'::readMD');
        return true;
    }
}
