
OffWave is the **OFFline Web Application Version Extractor**
============================================================

It is a set of PHP scripts and classes that search and discover web applications in a web server.

It can detect many kind of CMS, Frameworks, Wikis, Forums etc., their version, and also their modules and their modules' versions.

Please note that this is only a library, although the demo/ folder contains sample code to use it, and the test/ folder contains scripts to download all known application versions and test the library on them.

The lib/Offwave/Agents folder contains the different detectors for each CMS/Framework/Utilities etc. and their versions

Project's Goal
--------------

The aim of this code is to be added as an extension to [AlternC Hosting Software control Panel](https://alternc.org/), to detect applications and check their freshness against security issues.

It can be used by any other application. It is distributed under the GPL-v3 License.

Please contact us (using license.txt file) if you are : 

* An application developer and want to include your application in our code,
* A hosting provider or a hosting control panel development team.

Installation offwave-scan
-------------------------

git clone git@git.octopuce.fr:offwave-scan
cd offwave-scan
git submodule init
git submodule update

Currently implemented Agents
----------------------------

We currently implemented detection agents for the following applications : 

* Spip CMS
* Joomla CMS
* Wordpress CMS
* Drupal Framework
* Phpbb Forum
* PhpMyAdmin Utility

If you want to help, you can create an agent for a new application, and try to maintain it!


Creating your own Agent 
-----------------------

To create your own agent to detect an application you want to add, add it to either Framework/ or Cms/ 
(or create your own hierarchy, and add it to $option["agents_directories"] when creating Offwave_Scanner() object.)

Copy an existing agent and change its functions. There may be 3 of them : 

    function identifyApplication($path) 

returns an array with "application" => "name" and maybe "version" => "1.x" 
when an application has been quickly detected.

    function identifyVersion($path,$parameters) 

modify the $parameters array with more informations detected for the application (usually a precise version number, and maybe other information) 
If nothing more is found, shall return $parameters as it is.

    function identifyModules($path,$parameters)

modify the $parameters array with more informations detected for the application (usually the list of submodules if they are used, and their version number, and maybe other information)
If nothing more is found, shall return $parameters as it is.

If you know a list of file and folders used in your application, or some version of it, you can skip "identifyApplication" function and only create a Applicationname/tree.ini file which will describe the file and folders hierarchy for each version of your application. See Spip/tree.ini as an example.

