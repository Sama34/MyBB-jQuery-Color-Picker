/***************************************************************************
 *
 *	MyBB jQuery Color Picker plugin (/Documentation/README.txt)
 *	Authors: Omar Gonzalez & Vintagedaddyo & iAndrew & AmazOuz
 *	Copyright: © 2022
 *
 *	Websites: https://github.com/Sama34 https://github.com/Vintagedaddyo
 *
 *       Based off: https://community.mybb.com/thread-158934-post-1343419.html#pid1343419
 *
 *	Allow your users to select custom colors to adapt the theme display.
 *
 ***************************************************************************
 
****************************************************************************
	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.
****************************************************************************/


Custom Color Changer * (1.2): 

* Install a color changer in your forums & let every member choose their color



Current localization support:

- english * yes
- englishgb * yes
- espanol * yes
- french * yes
- italiano * yes


To Install:

Upload the files found within the "Upload" folder to your forums directory, And Go to Admin CP And Activate!


Backend usage:

Default Hex Color:

- Write here the hex of the default theme color. Example: for #008dd4 write 008dd4

Ie:

008dd4

Custom Text Color Elements:

- Write here Classes & Ids (separated by comma) of elements for which you want to change the text color

Ie:

.top_links a:link, .top_links a:hover, .top_links a:focus, .top_links a:visited, .navigation a:link, .navigation a:hover, .navigation a:focus, .navigation a:visited, .trow1 a:link, .trow1 a:hover, .trow1 a:focus, .trow1 a:visited,  .trow2 a:link, .trow2 a:hover, .trow2 a:focus, .trow2 a:visited, #footer .lower span#copyright  a:link


Custom Border Color Elements:

- Write here Classes & Ids (separated by comma) of elements for which you want to change the border color

.postbit_buttons > a:link


Custom Background Color Elements:

- Write here Classes & Ids (separated by comma) of elements for which you want to change the background color

#search input.button, .thead






Note: You have to use the correct identifiers, there are tons you can use and the ones below are just to get your brain on the thought process,  also note that for some items you might also need also to remove image attributes from the css on the respective items else some elements with such it may not work properly...

Anyhoo, as you see you can think of and come up with all sorts elements to specific color/style and ways to use such....

table a:link, .top_links a:link, a:link, a:hover, a:focus, a:visited, #panel .lower ul.panel_links a:link, #panel .lower ul.panel_links  a:visited, #panel .lower ul.panel_links a:hover, #panel .lower ul.panel_links a:active, #panel .lower ul.user_links a:link, #panel .lower ul.user_links  a:visited, #panel .lower ul.user_links a:hover, #panel .lower ul.user_links a:active, #footer a:link, #footer a:visited, #footer a:hover, #footer a:active

.thead, .tfoot, #search input.button, .postbit_buttons > a:link

#panel, .tfoot, .upper, .thead, .tcat, #footer, #copyright, .breadcrumb, #header

.post_block h3, .post_block h3 a, .trow1 a:link, .trow2 a:link, #logo ul.top_links a:link, #panel .lower a:link, .navigation a:link


Etc, etc, .....


Changes:

- changed version numbering as it made more sense rather than be reflective of software version and saying hey the plugin is on 1.8 so plug version is 1.8? Instead factoring that initial version should be factored as version 1, then, my initial conversions begin thus @ 1.1 and finalized as 1.2 as it made more sense to me and should be such as to not confuse from previous project as this with various changes and dependency removals changes is now diff project...

- added the old touch specific files I shared back in 2016 tutorial this was based on

- replaced dark version of picker with the light coloured version

- removed the dependency on plugin library by writing those specific parts to be standalone for the plugin to be used without such a need (as that sort of thing bugs me when folks say hey, here is this plug you should use, but, wait, you first need another plug/library in order to use the plug, grrr, while plenty prefer standalone, or leave it alone, lol)

- began implementing localization support (localization is currently in place for English, EnglishGB, Espanol, French, Italiano)
