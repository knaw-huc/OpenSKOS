<?php

/* 
 * Updates the status expired to status obsolete
 */

require_once 'Zend/Console/Getopt.php';
$opts = array(
	'env|e=s' => 'The environment to use (defaults to "production")',
);

try {
	$OPTS = new Zend_Console_Getopt($opts);
} catch (Zend_Console_Getopt_Exception $e) {
	fwrite(STDERR, $e->getMessage()."\n");
	echo str_replace('[ options ]', '[ options ] action', $OPTS->getUsageMessage());
	exit(1);
}

include dirname(__FILE__) . '/../bootstrap.inc.php';

// Allow loading of application module classes.
$autoloader = new OpenSKOS_Autoloader();
$mainAutoloader = Zend_Loader_Autoloader::getInstance();
$mainAutoloader->pushAutoloader($autoloader, array('Editor_', 'Api_'));


// Concepts
$conceptsCounter = 0;

$response  = Api_Models_Concepts::factory()->getConcepts('status:' . OpenSKOS_Concept_Status::_EXPIRED);

if (isset($response['response']['docs'])) {
    foreach ($response['response']['docs'] as $doc) {
        $concept = new Editor_Models_Concept(new Api_Models_Concept($doc));
        $concept->update([], ['status' => OpenSKOS_Concept_Status::OBSOLETE], true, true);
        $conceptsCounter ++;
    }
}

echo $conceptsCounter . ' concepts were updated.' . "\n";


// Profiles and user options

$replaceExpired = function($statuses) {
    $expiredInd = array_search(OpenSKOS_Concept_Status::_EXPIRED, $statuses);
    if ($expiredInd !== false) {
        $statuses[$expiredInd] = OpenSKOS_Concept_Status::OBSOLETE;
    }
    return $statuses;
};

// Profiles
$profilesCounter = 0;
$profilesModel = new OpenSKOS_Db_Table_SearchProfiles();		
foreach ($profilesModel->fetchAll() as $profile) {
    $searchOptions = unserialize($profile->searchOptions);
    
    if (in_array('expired', $searchOptions['status'])) {
        $searchOptions['status'] = $replaceExpired($searchOptions['status']);    
        $profile->setSearchOptions($searchOptions);

        $profile->save();
        $profilesCounter ++;
    }
}

echo $profilesCounter . ' profiles were updated.' . "\n";


// Users
$usersCounter = 0;
$usersModel = new OpenSKOS_Db_Table_Users();		
foreach ($usersModel->fetchAll() as $user) {
    
    if ($user->searchOptions !== null) {
        $searchOptions = unserialize($user->searchOptions);

        if (isset($searchOptions['status']) && in_array('expired', $searchOptions['status'])) {
            $searchOptions['status'] = $replaceExpired($searchOptions['status']);    
            $user->searchOptions = serialize($searchOptions);

            $user->save();
            $usersCounter ++;
        }
    }
}

echo $usersCounter . ' users were updated.' . "\n";

