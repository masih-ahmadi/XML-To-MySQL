<?php

function processXml($config, $xml) {
    // Database configuration
    $dbHost = $config['database']['host'];
    $dbUser = $config['database']['user'];
    $dbPassword = $config['database']['password'];
    $dbName = $config['database']['name'];
    $dbTableName = $config['database']['table'];

    // Error logging configuration
    $logFile = $config['logfile'];

    // Connect to the database
    $db = new mysqli($dbHost, $dbUser, $dbPassword, $dbName);

    // Check the database connection
    if ($db->connect_error) {
        // Log errors to the logfile
        error_log("Connection failed: " . $db->connect_error, 3, $logFile);
        exit(1);
    }


      // Insert data into the database
     foreach ($xml->children() as $item) {
        $values = [];
        foreach ($item->children() as $field) {
            $values[] = "'" . $field . "'";
        }
        $insertQuery = "INSERT INTO $dbTableName VALUES (" . implode(', ', $values) . ")";
        $db->query($insertQuery);
    }

    // Close the database connection
    $db->close();
    //$stmt->close();
    echo "Data successfully processed and inserted into the database.\n";
}

// Check if the script is being run directly or included in another script
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    // If run directly, load the XML file and execute the processing logic
    $config = include 'config.php';
    $xmlFilePath = $config['xmlfile'];
    $xml = simplexml_load_file($xmlFilePath);

    if ($xml === false) {
        // Log errors to the logfile
        error_log("Error: Failed to parse XML file $xmlFilePath", 3, $config['logfile']);
        exit(1);
    }

    // Call the main processing function
    processXml($config, $xml);
}

?>
