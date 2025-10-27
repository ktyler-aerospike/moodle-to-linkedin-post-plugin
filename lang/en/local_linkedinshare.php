<?php
$string['pluginname'] = 'LinkedIn Share for Certificates';

// text for banner prompt and buttons
$string['prompt_text'] = 'Are you ready to share your certificate on LinkedIn?';
$string['share'] = 'Share';
$string['notnow'] = 'Not now';

//text for when banner button is chosen
$string['dismiss:done'] = 'Okay, we won’t show this again for a while.';
$string['share:done'] = 'Great! We will route you to LinkedIn for authentication and posting.';

$string['privacy:metadata'] = 'This plugin stores minimal data to enable creating a badge post on linked in.';
$string['privacy:metadata:local_linkedinshare'] = 'Pending LinkedIn share prompts for users.';
$string['privacy:metadata:local_linkedinshare:userid'] = 'The ID of the user receiving the prompt.';
$string['privacy:metadata:local_linkedinshare:badgeid'] = 'The course idnumber to find coure outline and image for post.';
$string['privacy:metadata:local_linkedinshare:verifcode'] = 'Verification code associated with the badge for a link to verif process';
$string['privacy:metadata:local_linkedinshare:timecreated'] = 'When the prompt was created.';
$string['privacy:metadata:local_linkedinshare:dismissuntil'] = 'If dismissed, don’t show again until this timestamp.';

// config page strings
$string['setting:endpoint'] = 'Share endpoint URL';
$string['setting:endpoint_desc'] = 'Base URL to start the LinkedIn share/auth flow. Usually ends with /auth/linkedin/start';
$string['enabled'] = 'Enable Share Banner for LinkedIn';
$string['enabled_desc'] = 'If disabled, the LinkedIn share banner and related functionality will be completely turned off.';

//error message strings
$string['missingconfig'] = 'Missing configuration: {$a}';
$string['curlerror'] = 'Could not contact the sharing service: {$a}';
$string['invalidupstreamresponse'] = 'Invalid upstream response: {$a}';
$string['redirectmissinglocation'] = 'Upstream redirect missing Location header (HTTP {$a}).';
$string['error:invalidrecord'] = 'Invalid or missing prompt.';