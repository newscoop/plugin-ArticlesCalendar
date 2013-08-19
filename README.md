Newscoop Article Of The Day Calendar
=======================

Very simple and flexible plugin to show an article of the day.

**Purpose:** Marks an article as an "Article of the day" and displays it in nice calendar widget.

Installation
-------------
Installation is a quick process:


1. Installing plugin through our Newscoop Plugin System
2. Include plugin smarty block
3. That's all!

### Step 1: Installing plugin through our Newscoop Plugin System
Run the command:
``` bash
$ php application/console plugins:install "newscoop/articles-calendar-plugin" --env=prod
```
Plugin will be installed to your project's `newscoop/plugins/Newscoop` directory.

### Step 2: Include plugin smarty block

Include below smarty block into template (ex. front.tpl - calendar will be shown on homepage)
```smarty
{{ articles_calendar }}{{ /articles_calendar }}
```
### Step 3: That's all!
Go to an article edition in Newscoop Admin Panel to see Newscoop Article Of The Day Calendar Plugin on the right sidebar in action.

If you included calendar smarty block in front.tpl file then go to your homepage to see the calendar widget!

Newscoop Article Of The Day Calendar Plugin Documentation
-------------
Plugin documentation can be found [here](http://nps-docs.grupasiedzieje.pl/Plugins/Newscoop_Article_Of_The_Day_Calendar_Plugin).

License
-------

This bundle is under the GNU General Public License v3. See the complete license in the bundle:

    LICENSE

About
-------
Newscoop Article Of The Day Calendar Plugin is a [Sourcefabric o.p.s](https://github.com/sourcefabric) initiative.
