<?php

// Define the current working directory as root directory for includes.
if (! defined ( 'ROOT_DIRECTORY' )) {
	define ( 'ROOT_DIRECTORY', getcwd () );
}

// Register auto-loader.
spl_autoload_register ( function ($ClassName) {
	
	// Buffer ROOT_DIRECTORY in variable to allow modification.
	$IncludeDir = ROOT_DIRECTORY;
	
	// Add trailing DIRECTORY_SEPARATOR if necessary.
	if ($IncludeDir [strlen ( $IncludeDir ) - 1] !== DIRECTORY_SEPARATOR) {
		$IncludeDir .= DIRECTORY_SEPARATOR;
	}
	
	// Attempt to include the class source file.
	include_once $IncludeDir . 'includes' . DIRECTORY_SEPARATOR . $ClassName . '.php';
} );
