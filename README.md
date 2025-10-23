# local_linkedinshare

Shows a LinkedIn share prompt when a certificate is issued by tool_certificate. The other half (what happens after you hit the share button) is stored in a different repo and is a GCP Cloud Run serverless function.

**How it works**
- Observes `tool_certificate` issue events (same filter as the first SQL snippet shown below).
- Stores (userid, courseid, badgeid=course.idnumber, verifcode=other->code).
- Injects a top-of-body banner for the user with a Share button and Not now.

**Install**
1. Place in `local/linkedinshare`.
2. Visit `Site administration → Notifications` to install DB table.
3. (Optional) Limit the banner to the coursecertificate activity by uncommenting the block in `lib.php`.

**Notes**
- Compatible with Moodle 5 and Moodle Workplace.
- No changes to `tool_certificate` or `mod_coursecertificate` required.
- 'tool_certificate' and 'mod_coursecertificate' are pre-reqs


**FILTER USED TO SEE WHAT OBSERVER LOOKS FOR:**
SELECT l.userid, m.idnumber, JSON_VALUE(l.other, '$.code') as verifcode
FROM mdl_logstore_standard_log l
JOIN  mdl_course m
ON l.courseid = m.id
WHERE component = 'tool_certificate'
AND action = 'issued'
AND target = 'certificate'
and courseid <> 0;

**SQL TO LOOK AT STORED NOTIFICATION BANNERS**
SELECT id, userid, courseid, badgeid, verifcode, timecreated, dismissuntil
FROM mdl_local_linkedinshare
ORDER BY id DESC
LIMIT 10;

**SQL TO INSERT A TEST NOTIF BANNER**
INSERT INTO mdl_local_linkedinshare (userid, courseid, badgeid, verifcode, timecreated, dismissuntil)
VALUES (6, 2, 'TESTBADGE', 'TESTCODE123', UNIX_TIMESTAMP(), NULL);

**If you need to look up a userid for a particular user name:**
SELECT id as userid, username from mdl_user;
