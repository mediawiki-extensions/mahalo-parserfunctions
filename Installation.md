# Acquiring the software #

There are two easy ways to acquire the Mahalo ParserFunctions software: by subversion and by archive.

## Subversion ##

_Note: [Subversion](http://en.wikipedia.org/wiki/Subversion_%28software%29) is a version control system.  To download from subversion, you must have a subversion client._

To download the latest code from subversion, execute this command:
```
svn co http://mahalo-parserfunctions.googlecode.com/svn/trunk/ mahalo-parserfunctions
```

This will create a directory called `mahalo-parserfunctions` which will contain all the project files.

To upgrade at any time, navigate to this directory and execute this command:
```
svn update
```

## Archive ##

To download the latest release,
  1. Go to the [Download List](http://code.google.com/p/mahalo-parserfunctions/downloads/list) and select a release archive (zip)
  1. Extract the archive, this will create a directory called `mahalo-parserfunctions`

# Installation and activation #

Once you have acquired the software, copy the `mahalo-parserfunctions` directory to `$IP/extensions` where `$IP` is your MediaWiki installation directory.

Finally, edit your `LocalSettings.php` file and add `require()` lines for each extension you you would like to activate.

For example, to activate the ImageLink parser function, you would add this line:
```
require_once('extensions/mahalo-parserfunctions/mhoImageLink.php');
```

For a list of available parser functions, see the [Project Home](http://code.google.com/p/mahalo-parserfunctions/) page.