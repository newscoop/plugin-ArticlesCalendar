(function( $ ){
    
    var defaults = {
        'namespace': '',
        'defaultView' : 'month',
        'today' : new Date(),
        'date' : new Date(),
        'dayNames': ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'],
        'firstDay': 0,
        'monthNames': ['January','February','March','April','May','June','July','August','September','October','November','December'],
        'navigation': true,
        'showDayNames': true,
        //'earliestMonth': new Date(2011, 8),
        //'latestMonth': new Date(2012, 1),
        'articles': "json-events.php",
        'title': "Artikel des Tages",
        'buttons': {
            'prev': "Vorheriger Monat",
            'next': "Nächster Monat"
        }
    };
  
    var methods = {
        init : function( options ) { 
            
            options = $.extend( defaults, options );

            this.each(function(i, _element) {               
                var element = $(_element);
                var calendar = new ArticlesCalendar(element, options);
                element.data('articlescalendar', calendar);
                calendar.render();              
            });
        
            return this;
        }
    };
        
    $.fn.articlescalendar = function( method ) {
        
        // Method calling logic
        if ( methods[method] ) {
            return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } 
        else if ( typeof method === 'object' || ! method ) {
            return methods.init.apply( this, arguments );
        } 
        else {
          $.error( 'Method ' +  method + ' does not exist on jquery.calendar' );
        } 

    };

    Date.prototype.addMonths = function(months) {
        this.setMonth(this.getMonth() + months);
        return this;
    }

    Date.prototype.isSameMonth = function(date) {
        return this.getFullYear() === date.getFullYear() && this.getMonth() === date.getMonth();
    }

    Date.prototype.getDaysFrom = function(date) {
        var one_day, diff;
            
        one_day=1000*60*60*24; //one day in milliseconds.

        return Math.round((this.getTime() - date.getTime())/one_day);
    }
    
    //element is in jquery form $()
    function ArticlesCalendar(element, options) {
        
        var t = this;
    
        var _get_articles = options.articles;
        var _date_cache = [];   
        var _start = undefined;
        var _end = undefined;
        var _today = options.today;
        var _date = options.date;
        var _view = options.defaultView;
        var _header = undefined;

        delete options.articles;
        delete options.date;
        delete options.today;
        
        t.addMonths = addMonths;
        t.getCalendarDate = getCalendarDate;
        t.getTodaysDate = getTodaysDate;
        t.render = render;
        t.redraw = redraw;
        
    
        function render() {
    
            if (_view === 'month') {
                _header = new Header(t, element, options);
                monthView();
            }
            else if(_view === 'widget') {
                widgetView();
            }
            
            renderArticles();
        }
        
        function getTodaysDate() {
            return _today;
        }
        
        function getCalendarDate() {
            return _date;
        }

        // return Date object.
        function addMonths(months) {
            return _date.addMonths(months);
        }
        
        function redraw() {
            
            //set these to empty date boxes.
            for (var i=0; i<_date_cache.length; i++) {
                _date_cache[i].clear();
            }
            
            if (_view === 'month') {
                setMonthViewDates();
            }
            
            renderArticles();
        }
        
        function renderArticles() {
            //user passed in a function to get events.
            if (_get_articles !== undefined && $.isFunction(_get_articles)) {
                _get_articles(_start, _end, updateCalendar);
            }
            //a url to get some events.
            else if (_get_articles !== undefined && typeof _get_articles === "string") {
                $.ajax({
                  url: _get_articles,
                  dataType: 'json',
                  data: {start: _start.getTime(), end: _end.getTime(), _: (new Date()).getTime()},
                  success: updateCalendar
                });
            }   
        }
        
        //callback after receiving articles.
        function updateCalendar(events) {
            var cached_date, tmpDate, event;
                
            for (var i=0; i<events.length; i++) {
                event = events[i];
                
                tmpDate = new Date(event.date.year, event.date.month-1, event.date.day);
                cached_date = retrieveDateFromCache(tmpDate);

                //article returned does not fit the date range, ignore.
                if (cached_date === undefined) {
                    continue;
                }
                
                if (event.title !== undefined) {
                    cached_date.setTitle(event.title);
                }
                if (event.image !== undefined) {
                    cached_date.setThumbnail(event.image);
                }
                if (event.url !== undefined) {
                    cached_date.setUrl(event.url);
                }       
            }
        }
        
        //date is a js Date object.
        function retrieveDateFromCache(date) {
            var diff;
            
            diff = date.getDaysFrom(_start);
            
            return _date_cache[diff];
        }
        
        function widgetView() {
            var table, tr, td, dateBox, ns;
            
            ns = options.namespace ? options.namespace + "-" : "";
            table = $('<table><tbody></tbody></table>');
            tr = $('<tr/>');
            
            for (var j=0; j<7; j++) {
                
                td = $('<td/>');
                td.addClass(ns+'day-'+j);
                
                dateBox = new DayBox(j, td, options);
                _date_cache.push(dateBox);
                
                tr.append(td);
            }
            
            table.append(tr);
            setWidgetViewDates();
            element.append(table);
        }
        
        function setWidgetViewDates() {
            var y, m, d;
            
            y = _date.getFullYear();
            m = _date.getMonth();
            d = _date.getDate() - 1;
            
            var tmp_date;
            for (var c=_date_cache.length-1; c>-1; c--) {
                tmp_date = new Date(y, m, d);
                
                if(c === 0) {
                    _start = tmp_date;
                }
                else if(c === _date_cache.length-1) {
                    _end = tmp_date;
                }
                
                _date_cache[c].setDate(tmp_date);
                d--;
            }
        }
        
        function monthView() {
            var $ul, 
                $li, 
                dateBox, 
                ns = options.namespace ? options.namespace + "-" : "",
                i;
           
            $ul = $('<ul/>', {
                'class': ns+'calendar-list '+ns+'clearfix'
            });
            
            //show the names of the days of the week on the calendar
            if (options.showDayNames) {

                /*
                for (i = 0; i < 7; i++) {
                    th = $('<th/>');
                    
                    dayIndex = (options.firstDay + i) % 7;
                    th.append(options.dayNames[dayIndex]);
                    
                    thead.find('tr').append(th);
                }
                */
            }
            
            for (i = 0; i < 42; i++) {
                
                $li = $('<li/>');
                
                dateBox = new DayBox(t, $li, options);
                _date_cache.push(dateBox);
                
                $ul.append($li);
            }
            
            setMonthViewDates();
                
            element.append($ul);
        }   
        
        function setMonthViewDates() {
            var y, m, begin, s_dofw, d, tmp_date;
            
            y = _date.getFullYear();
            m = _date.getMonth();
            
            begin = new Date(y, m, 1);
            s_dofw = begin.getDay();
            
            //need this first day option to start week on either sunday/monday etc (leftmost day)
            d = 1 - s_dofw + options.firstDay;
            if (s_dofw < options.firstDay) {
                d = d - 7;
            }

            for (var c = 0; c < _date_cache.length; c++) {
                tmp_date = new Date(y, m, d);
                
                if(c == 0) {
                    _start = tmp_date;
                }
                else if(c == _date_cache.length-1) {
                    _end = tmp_date;
                }
                
                _date_cache[c].setDate(tmp_date);
                d++;
            }
        }
    }
    
    function Header(calendar, element, options) {
        
        var html='', 
            ns = options.namespace ? options.namespace + "-" : "",
            $ul = $('<ul class="'+ns+'calendar-nav"/>'),
            $div = $('<div class="'+ns+'top"><h4>'+options.title+'</h4><h3></h3></div>');
        
        if (options.navigation === true) {
            html = html + '<li class="'+ns+'button-prev"><a class="grey-button">'+options.buttons.prev+'</a></li>';
            html = html + '<li class="'+ns+'button-next"><a class="grey-button">'+options.buttons.next+'</a></li>';   
         
            $ul.append(html);
            
            updateHeader(calendar.getCalendarDate());
            enableHeader();
            
            $ul.find('.'+ns+'button-prev').click(function() {
                var date;
                
                if ($(this).hasClass(ns+'state-disabled')) {
                    return;
                }
    
                disableHeader();        
                date = calendar.addMonths(-1);
    
                updateHeader(date);
                calendar.redraw();
                enableHeader();         
            });
            
            $ul.find('.'+ns+'button-next').click(function(){
                var date;
                
                if ($(this).hasClass(ns+'state-disabled')) {
                    return;
                }
                
                disableHeader();
                date = calendar.addMonths(1);
                
                updateHeader(date);
                calendar.redraw();
                enableHeader();
            });
            
            $div.append($ul);
        }
        
        element.append($div);
        
        function disableHeader() {
            
            disableButton('prev');
            disableButton('next');      
        }
        
        function enableHeader() {
            var date;
            
            date = calendar.getCalendarDate();
            
            //have reached the earliest month we should show.
            if (options.earliestMonth !== undefined && date.isSameMonth(options.earliestMonth)) {
                disableButton('prev');
            }
            else {
                enableButton('prev');
            }
            
            //have reached the latest month we should show.
            if (options.latestMonth !== undefined && date.isSameMonth(options.latestMonth)) {
                disableButton('next');
            }
            else {
                enableButton('next');
            }               
        }
        
        function disableButton(buttonName) {
            $ul.find('.'+ns+'button-'+buttonName)
                .addClass(ns+'state-disabled');
        }
            
        function enableButton(buttonName) {
            $ul.find('.'+ns+'button-'+buttonName)
                .removeClass(ns+'state-disabled')
                .removeClass('disabled');
        }
        
        function updateHeader(date) {
            var month, 
                yyyy;
            
            yyyy = date.getFullYear();
            month = options.monthNames[date.getMonth()]; 
           
            $div.find('h3')
                .empty()
                .append(month+" "+yyyy);
        }
        
        return this;
    }
    
    function DayBox(calendar, $li, options) {
        
        var _date = undefined;
        var _title = undefined;
        var _s_image = undefined;
        var _url = undefined;

        var ns = options.namespace ? options.namespace + "-" : "";
        
        this.setDate = setDate;
        this.setTitle = setTitle;
        this.setThumbnail = setThumbnail;
        this.setUrl = setUrl;
        this.clear = clear;
        
        function buildLI() {
            $li.removeClass()
                .append("<a><h5></h5><span></span></a>");
        }

        function isCurrentMonth() {
            var cdate, cm, dm;

            cdate = calendar.getCalendarDate();
            cm = cdate.getMonth();
            dm = _date.getMonth();
        
            if (cm === dm) {
                return true;
            }

            return false;
        }

        function isToday() {
            var today, ty, tm, td, dy, dm, dd;

            today = calendar.getTodaysDate();
            ty = today.getFullYear();
            tm = today.getMonth();
            td = today.getDate();

            dy = _date.getFullYear();
            dm = _date.getMonth();
            dd = _date.getDate();

            //date is today
            if (ty === dy &&
                tm === dm &&
                td === dd ) 
            {
                return true;
            }

            return false;
        }
        
        function setDate(date) {
            
            _date = date;
    
            $li.find('span')
                .append(date.getDate());
            
            if (!isCurrentMonth()) {
                $li.addClass(ns+'inactive');
            }

            if (isToday()) {
                $li.addClass(ns+'today');
            }
        }
        
        function setTitle(title) {
            _title = title;
            
            $li.find('h5').append(title);
        }
        
        function setThumbnail(url) {
            _s_image = url;
            
            $li.find("span").after($("<img />", {
                "src": url
            }));
        }
        
        function setUrl(url) {
            _url = url;
            $li.find("a").attr("href", url);
        }
        
        function clear() {
            _date = undefined;
            _title = undefined;
            _s_image = undefined;
            _url = undefined;
            
            $li.empty();
            buildLI();
        }

        //create the initial content within the td.
        buildLI();
    }
    
})( jQuery );


