# local_linkedinshare

Shows a LinkedIn share prompt when a certificate is issued by tool_certificate.

**How it works**
- Observes `tool_certificate` issue events (same filter as your SQL).
- Stores (userid, courseid, badgeid=course.idnumber, verifcode=other->code).
- Injects a top-of-body banner for the user with a Share button and Not now.

**Install**
1. Place in `local/linkedinshare`.
2. Visit `Site administration → Notifications` to install DB table.
3. (Optional) Limit the banner to the coursecertificate activity by uncommenting the block in `lib.php`.

**Notes**
- Compatible with Moodle 5 and Moodle Workplace.
- No changes to `tool_certificate` or `mod_coursecertificate` required.
