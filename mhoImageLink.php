<?php
/*
 * mhoImageLink.php - Adds a parser function for making internal images link to external URLs
 * @author Jim R. Wilson
 * @version 0.1
 * @copyright © 2007 Mahalo.com, Inc.
 * @licence GNU General Public Licence 2.0
 * @addtogroup Extensions
 * -----------------------------------------------------------------------
 * Description:
 *     This is a MediaWiki extension which adds a parser function for linking
 *     internal images to internal or external URLs.
 * Requirements:
 *     MediaWiki 1.9.x or higher
 *     PHP 5.x or higher
 * Installation:
 *     1. Create a folder in your $IP/extensions directory called 'parserfunctions'
 *         if it doesn't exist already. (Note: $IP is your MediaWiki install dir)
 *     2. Drop the following scripts into $IP/extensions/parserfunctions:
 *         mhoImageLink.php - This file
 *         mhoParserFunction.php - Abstract base class for parser functions
 *     3. Enable the extension by adding this line to your LocalSettings.php:
 *         require_once('extensions/parserfunctions/mhoImageLink.php');
 * -----------------------------------------------------------------------
 */
 
# Confirm MW environment
if (defined('MEDIAWIKI')) {

# Dependencies
include_once('mhoParserFunction.php');

define('MAHALO_IMAGE_LINK_VERSION','0.1');

# Credits
$wgExtensionCredits['parserhook'][] = array(
    'name'=>'MahaloImageLink',
    'author'=>'[http://www.mahalo.com Mahalo.com]',
    'url'=>'http://code.google.com/p/mahalo-parserfunctions/',
    'description'=>'Adds a parser function for making internal images link to URLs',
    'version'=>MAHALO_IMAGE_LINK_VERSION
);

/**
 * MahaloImageLink implementation
 */
class MahaloImageLink extends MahaloParserFunction {

    /* Cacheable system messages used by this class */
    var $mMsgs = array(
        '*' => array(
    	    'embed-clause' => '<a href="$1" title="$3"><img src="$2" title="$3" alt="$3" /></a>',
        ),
        'en' => array(
    	    'missing-params' => 'imageLink has not been passed all required parameters',
    	    'bad-url' => 'imageLink does not accept the url "<tt>$1</tt>"',
    	    'no-such-image' => 'imageLink cannot find [[Image:$1]]',
	    )
    );
    
    /**
     * Outputs the internal image wrapped in a link
     * @param Parser $parser Instance of running Parser.
     * @param String $image Name of image to display.
     * @param String $url External URL to which to link
     * @param String $alt Alternate text for image and link (optional)
     * @return String A parser strip flag which will be later replaced with raw html.
     */
    function imageLink( $parser, $image=null, $url=null, $alt='' ) {
    
        # Short-circuit if requried params are missing
        if ($image===null || $url===null)
            return $this->error('missing-params');

        # Prepare incomming params
        $image = trim($image);
        $url = trim($url);
        $alt = trim($alt);
        
        # Check for bad URLs
        if (!preg_match('/^('.wfUrlProtocols().')/',$url) ||
            preg_match('/\'"/',$url)
        ) {
            $t = Title::newFromText($url);
            if (!$t) return $this->error('bad-url', $url);
            $url = $t->getFullURL();
        }
            
        # Check to see that the selected image exists
        $imageObj = Image::newFromName( $image );
        if (!$imageObj->exists())
            return $this->error('no-such-image', $image);
        
        # Finally, since all checks passed, display it!       
        return $parser->insertStripItem(
            $this->msg(
                'embed-clause',
                htmlentities( $url, ENT_COMPAT ),
                $imageObj->getURL(),
                htmlentities( $alt, ENT_COMPAT )
            ),
            $parser->mStripState
        );
    }

}

# Create global instance (will autowire itself)
$wgMahaloImageLink = new MahaloImageLink();

} # End MW Environment wrapper
?>
