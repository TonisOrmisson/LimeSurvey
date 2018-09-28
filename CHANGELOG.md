# Change Log
Only partial changelog, [commit history](https://framagit.org/Shnoulle/LimeSurvey/commits/2.06_SondagesPro) show all changelog.

## [1.6.0] - 2017-06-17

### Fix
- Unable to install (runtimePath)

## [1.5.3] - 2017-06-17

### Dev
- Improve filter on sid, gid and qid

## [1.5.2] - 2017-03-17

### Fix
- [security] XSS in upload file

### Feature
- Export as xlsx (not as xls)

## [1.4.0] - 2017-03-02

### Fix
- Language can be updated during survey
- Update to 2.6.4_lts

### Feature
- Leave partial HTML in admin email

## [1.3.1] - 2017-02-02

### Fix
- Hidden Welcome page can be shown with token (Thank to [@Ben_V](http://www.pmidcalc.org/)) (re-fix)

### Translation
- Update catalan translation (Thanks to [@Ben_V](http://www.pmidcalc.org/)

## [1.3.0] - 2017-02-02

### Fix
- Import lsa don't import all token

### Feature
- Allow to update via cli
- Allow to update runtime directory in config (LimeSurvey API 3 compatible only)

### [1.2.2] - 2017-01-12
- Update [PHPMailer](https://github.com/PHPMailer/PHPMailer/releases/tag/v5.2.22) for security issue

## [1.2.1] - 2016-12-30

### Fix
- Update [PHPMailer](https://github.com/PHPMailer/PHPMailer/releases/tag/v5.2.21) for security issue

## [1.2.0] - 2016-11-16

### Fix
- Issue #11996: Possible remote code execution [LouisGac](https://github.com/LouisGac)
- Possible session fixation on survey entry with token [Carsten Schmitz](https://github.com/c-schmitz)

## [1.1.2] - 2016-11-16

### Feature
- Allow to import broken lsq and lsg
- Show (all) errors when try to import VV file
- Better gitignore for plugins
- Order survey listing by date desc by default

## [1.1.1] - 2016-11-16

### Fix
- Expression manager convert_value does not obey strict setting (Olle Haerstedt) (2.6.1lts)
- Date/Time filed does not record the answer (Olle Haerstedt) (2.6.1lts)

## [1.1.0] - 2016-11-10

### Fix
- Table in reponse view and response edit can take a big width and are unusuable
- readonly for readonly attribute
- Set secure cookies by default is connexion is secure

### Feature
- Allow self signed or invalid TLS/SSL smtp server (with ssl_allow_self_signed in config)

## [1.0.28] - 2016-10-23

### Fix
- Rank show rtl on ltr
- #11821: Apache error uploading images kcfinder ([Carsten Schmitz](http://limesurvey.org))

## [1.0.27] - 2016-10-06

### Fix
- PHP7 compatibility fix ([SamMousa](http://befound.nl))

## [1.0.26] - 2016-09-29

### Fix
- Statistics show more rank than available
- Max sub-question attribute for ranking question still editable after activation

## [1.0.25] - 2016-09-27

### Fix
- #11455: MapQuest discontinued the free tileserver ([Markus Flür](http://limesurvey.org))

## [1.0.24] - 2016-09-16

### Feature
- Allow plugin to add new question attributes

### Fix
- If no plugin add attribute : no questions are shown
- #10226: {ANSWERTABLE} includes <script> placed in question text ([LouisGac](http://limesurvey.org))
- ~ and _ in tokens hard to manually enter
- #11509: numerical input option integer only leads to positive integer input only

### Updated
- improve Plain text ANSWERTABLE for email
- better ANSWERTABLE for multiple question.

## [1.0.23] - 2016-07-17

### Feature
- Allow plugin to set more option when using renderHtml

### Fix
- Language is not correct when session timed out  or other error page
- Allow plugin to allow preview question or group without admin rights

## [1.0.22] - 2016-07-05

### Feature
- Log error and warning in tmp/runtime/application.log by default
- Allow plugin to set more option when using renderHtml

### Fix
- Improvement on error when DB save for public survey
- Fix DECIMAL value before try to save

## [1.0.21] - 2016-06-17

### Feature
- Allow to set column number on ranking question

### Fix
- EM tips are empty after reloading page via browser (F5)
- Ranking question : Alert are not show every time
- Ranking question : Add answers can broke Survey DB

## [1.0.20] - 2016-06-16

### Feature
- afterSurveyMenuLoad event to add survey specific menu items

### Fix
- Improvement on Conditions page with array (number) questions
- Google Analytics code not running

## [1.0.19] - 2016-06-09

### Fix
- Better loading of plugins for command
- Default email format in survey to html

## [1.0.17] - 2016-05-19

### Feature
- Add cssclass question attribute

### Fix
- Plugin survey setting type "checkbox" does not properly save
- Map question : google.maps fix for hidden text element
- Numeric comparaison with Expression
- Survey response marked as 'finished' after opening email link/password twice
- Unable to export result as PDF
- PHP memory_limit being set too low
- Bad link for Browse uploaded ressources if publicurl is set

### Updated
- New token table firstname/lastname to 150

## [1.0.9] - 2016-04-22

### Feature
- beforeController event plugin (for web)
- newUnsecureRequest : plugin direct request without CRSF

### Fix
- Checked responses are not read when load "surveys uploaded file"
- Using mktime() EM function broke survey administration
- EM regexMatch function don't show pattern error
- Attachments for registration emails don't get attached
- Remote control list_surveys can list whole surveys, and not only needed
- event beforeTokenEmail is not dispatched for register
- thousand separator break slider in some condition

## [1.0.6] - 2016-03-20

### Fix
- Issue with relevance on X scale with multilingual
- Languages can not be updated in label set administration
- Error with SMTP mail method
- Broken HTML or script can broke Survey Logic File
- beforeHasPermission event don't happen for owner of survey
- [Security] Survey ID not properly sanitized on survey creation
- 4-byte UTF characters (e.g. Emojis) entered into text can cause DB issue (mysql)
- [Security] issue when saving/loading responses on public survey

## [1.0.0] - 2016-03-01

### Fix
- Higher risk that the emails are rated as Spam
- Filter script in Plugin management and Survey Logic file.

### Updated
- Use updatable from config and use it, set to updatable=false

Start with LimeSurvey 2.06lts
