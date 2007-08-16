<?php
/*
 * mhoParserFunction.php - Provides convenient base class for developing parser functions.
 * @author Jim R. Wilson
 * @version 0.1
 * @copyright © 2007 Mahalo.com, Inc.
 * @licence GNU General Public Licence 2.0
 * -----------------------------------------------------------------------
 * Requirements:
 *     MediaWiki 1.9.x or higher
 *     PHP 5.x or higher
 * -----------------------------------------------------------------------
 */
 
# Confirm MW environment
if (defined('MEDIAWIKI')) {

/**
 * Base class for developing Parser functions.
 */
abstract class MahaloParserFunction {

    /**
     * Default system messages (should be overridden in child class via $mMsgs)
     */
    var $mMsgs = array();
    var $mDefaultMsgs = array(
        '*' => array(
    	    'error-box' => '<div class="errorbox">$1 $2</div>',
        ),
        'en' => array(
    	    'error-prefix' => "'''Error''': ",
        )
    );

    /**
     * Constructor
     * @param Boolean $autowire Whether to automatically hook into MW upon instantiation (defaults to true)
     */
    function __construct( $autowire=true ) {
        
        # Use reflection to detect and set various options
        $ro = new ReflectionObject( $this );

        # Set MsgPrefix to class name if unspecified
        if (!$this->mMsgPrefix) {
            $this->mMsgPrefix = strtolower($ro->name).'-';
        }
    
        # Detect parser functions implementations
        # Note: Any methods beginning with an underscore will not be considered.
        $methods = $ro->getMethods();
        $this->mParserFunctions = array();
        foreach ($methods as $method) {
            if (
                $ro->name === $method->class &&
                !$method->isAbstract() &&
                !$method->isStatic() &&
                !$method->isConstructor() &&
                $method->isPublic() &&
                !preg_match('/^_/', $method->name)
            ) $this->mParserFunctions[] = $method->name;
        }
    
        # Wire it up!
        if ($autowire) {
            global $wgExtensionFunctions, $wgHooks;
            $wgExtensionFunctions[] = array($this, 'setup');
            $wgHooks['LanguageGetMagic'][] = array($this, 'parserFunctionMagic');
        }
    }

    /**
     * Sets up parser functions.
     */
    function setup( ) {

        # Setup parser hooks
    	global $wgParser;
    	foreach ( $this->mParserFunctions as $function ) {
        	$wgParser->setFunctionHook( $function, array($this, $function) );
    	}

        # Determine system messages
        $msgs = array_merge_recursive($this->mDefaultMsgs, $this->mMsgs);
        foreach ( $msgs as $lang=>$langMsgs ) $msgs[$lang] = array_merge($msgs['*'], $langMsgs);
        unset( $msgs['*'] );

        # Add messages to cache
    	global $wgMessageCache;
        foreach ( $msgs as $lang => $langMsgs ) {
            $lm = array();
            foreach ($langMsgs as $key=>$val) $lm[$this->mMsgPrefix.$key] = $val;
            $wgMessageCache->addMessages( $lm, $lang );
        }
    }
    
    /**
     * Adds magic words for parser functions.
     * @param Array $magicWords
     * @param String $langCode Language code
     * @return Boolean Always true
     */
    function parserFunctionMagic( &$magicWords, $langCode='en' ) {
        foreach ($this->mParserFunctions as $function) {
            $magicWords[$function] = array( 0, $function );
        }
        return true;
    }
    
    /**
     * Convenience method for displaying an error message.
     * Note: Any additional args not listed in the method signature are cleansed
     * of HTML special characters and passed forward to the msg resolution functions.
     * @param String $msg The error message to display.
     * @return String Error message wrapped in an errorbox.
     */
    function error( $msg ) {
        $args = func_get_args();
        array_shift($args);
        array_walk($args, 'htmlspecialchars');
        return $this->msg(
            'error-box',
            $this->msg( 'error-prefix' ),
            wfMsgReplaceArgs( wfMsgGetKey( $this->mMsgPrefix . $msg, true ), $args )
        );
    }
    
    /**
     * Convenience method for retrieving a wfMsgForContent() message.
     * Note: Any additional args not listed in the method signature are passed 
     * forward to the msg resolution function.
     * @param String $msg The system message to use.
     */
    function msg( $msg ) {
        $args = func_get_args();
        array_shift($args);
        return wfMsgReplaceArgs( wfMsgGetKey( $this->mMsgPrefix . $msg, true ), $args );
    }
}

} # End MW Environment wrapper

