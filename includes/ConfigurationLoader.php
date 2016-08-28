<?php

/**
 * @author Jan Merkelbag
 *
 */
trait ConfigurationLoader {
  
  /**
   * Load configurations per-section from an ini-styled file.
   *
   * @param string $FileName
   *          The name of the file to load the configurations from.
   * @param array $Sections
   *          (optional) Define the sections which shall be extracted. Defaults
   *          to an empty array (extract all sections).
   *          
   *          If sections are passed the returned array will have the same order
   *          as the sections array passed (allowing for list()-ing the result).
   *          
   *          Each section is an element which can either be a single string
   *          value or a key-value-pair. In the latter case the key is the
   *          section name and the value is a boolean defining whether the
   *          loading shall fail (true) if the section does not exist or
   *          continue (false).
   * @return array|boolean Returns the array containing the configurations and
   *         sections defined.
   */
  static function loadFromFile(string $FileName, array $Sections = array()) {
    
    // Define all directories available for searching. The first element (the
    // empty string) allows to pass the exact relative or absolute path (or even
    // wrappers) to load the file from.
    $ConfigDirs = array (
        '',
        ROOT_DIRECTORY . DIRECTORY_SEPARATOR . 'settings' 
    );
    
    // Pre-initialise the return value with the fail-value.
    $configs = false;
    
    // Load the configurations from the file in the first directory.
    foreach ( $ConfigDirs as $ConfigDir ) {
      
      // Construct the file path.
      $filePath = $ConfigDir . DIRECTORY_SEPARATOR . $FileName;
      
      // Check whether the configuration file exists in the picked directory.
      if (file_exists ( $filePath )) {
        
        // Attempt to parse the file.
        $configs = parse_ini_file ( $filePath, true );
        
        // If specific sections were passed
        if (count ( $Sections ) && is_array ( $configs )) {
          $configSections = array ();
          foreach ( $Sections as $Section => $Mandatory ) {
            if (is_string ( $Mandatory ) && strtolower ( $Mandatory ) !== 'true' && strtolower ( $Mandatory ) !== 'false') {
              $Section = $Mandatory;
              $Mandatory = true;
            }
            if (array_key_exists ( $Section, $configs )) {
              $configSections [$Section] = $configs [$Section];
            } else if ($Mandatory) {
              Debugger::report ( "Mandatory section <{$Section}> does not exist in configuration file <{$filePath}>!" );
              return false;
            }
          }
          $configs = $configSections;
        }
        break;
      }
    }
    
    // Return the results (may it be a proper array or the fail-value).
    return $configs;
  }
}
