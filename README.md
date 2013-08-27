Newscoop Article Of The Day Plugin
=======================

Very simple and flexible plugin to show an article of the day.

**Purpose:** Sets articles as an "Article of the Day" and displays it in nice calendar widget.

Features
-------------
- Set available articles as an Article of the Day
- Define the publication in which the article of the day may appear
- Define the date of the article of the day
- Define your own style sheet for the calendar widget appearance
- Choose publications in which articles may appear
- Select one of available image resolution types (ex. issuethumb (130x70))
- Manually define articles image resolution (optional)
- Define first day of the month
- Set the first and last month (months range to be available in calendar)
- The ability to hide / show navigation (prev / next buttons, etc.)
- The ability to hide / show the names of days
- Available in diffrent languages ([list](https://github.com/newscoop/plugin-ArticlesCalendar/tree/master/Resources/translations))

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
Plugin documentation can be found [here](http://nps-docs.grupasiedzieje.pl/Plugins/Newscoop_Article_Of_The_Day).

License
-------

This bundle is under the GNU General Public License v3. See the complete license in the bundle:

    LICENSE

About
-------
Newscoop Article Of The Day Plugin is a [Sourcefabric o.p.s](https://github.com/sourcefabric) initiative.
