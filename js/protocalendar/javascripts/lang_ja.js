(function() {
   ProtoCalendar.LangFile['ja'] = {
     HOUR_MINUTE_ERROR: '時間が無効です。',
     NO_DATE_ERROR: '日を選択して下さい。',
     OK_LABEL: 'OK',
     DEFAULT_FORMAT: 'yyyy/mm/dd',
     LABEL_FORMAT: 'yyyy年mm月dd日 ddddi',
     MONTH_ABBRS: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
     WEEKDAY_ABBRS: ['日','月','火','水','木','金','土'],
     WEEKDAY_NAMES: ['日曜日','月曜日','火曜日','水曜日','木曜日','金曜日','土曜日'],
     YEAR_LABEL: '年',
     MONTH_LABEL: '月',
     YEAR_AND_MONTH: true,
     today: '今日',
     tomorrow: '明日',
     yesterday: '昨日',

     parseDate: function(inputValue) {
       if (inputValue == '一昨日') {
         var d = new Date();
         d.setDate(d.getDate() - 2);
         return d;
       } else if (inputValue == '明後日') {
         var d = new Date();
         d.setDate(d.getDate() + 2);
         return d;
       }
     },

     getHolidays:   function(calendar) {
       var year = calendar.getYear();
       var month = calendar.getMonth();
       var lastDay = calendar.getNumDayOfMonth();

       var temp;
       if (year < 2000) {
         temp = 2213;
       } else {
         temp = 2089;
       }
       var springDay = Math.floor((31 * year + temp)/128) - Math.floor(year/4) + Math.floor(year/100);

       if (year < 2000) {
         temp = 2525;
       } else {
         temp = 2395;
       }
       var autumnDay =  Math.floor((31 * year + temp)/128) - Math.floor(year/4) + Math.floor(year/100);

       var holidays = [];
       var mondayIndex = 0;
       for(var day = 1; day <= lastDay; day++) {
         var dayOfWeek = new Date(year, month, day).getDay();
         holidays[day] = 0;

         if (dayOfWeek == ProtoCalendar.MONDAY) {
           ++mondayIndex;
         }

         if (day == 1 && month == ProtoCalendar.JAN) {
           holidays[day] = '元旦';
         } else if (day == 3 && month == ProtoCalendar.APR) {
           holidays[day] = '休日';
         } else if (day == 5 && month == ProtoCalendar.APR) {
           holidays[day] = '休日';
         } else if (day == 6 && month == ProtoCalendar.APR) {
           holidays[day] = '休日';
         } else if (day == 27 && month == ProtoCalendar.APR) {
           holidays[day] = '休日';
         } else if (day == 5 && month == ProtoCalendar.MAY) {
           holidays[day] = '休日';
         } else if (day == 15 && month == ProtoCalendar.MAY) {
           holidays[day] = '休日';
         } else if (day == 16 && month == ProtoCalendar.MAY) {
           holidays[day] = '休日';
         } else if (day == 6 && month == ProtoCalendar.DEC) {
           holidays[day] = '休日';
         } else if (day == 25 && month == ProtoCalendar.DEC) {
           holidays[day] = '休日';
         } else if (day == 26 && month == ProtoCalendar.DEC) {
           holidays[day] = '休日';
         }
       }
       var hasHoliday = year > 1973 || year == 1973 && month > 4;
       var curRule = hasHoliday && (year < 2008 || year == 2008 && month < 5);
       var newRule = hasHoliday && (year > 2008 || year == 2008 && month >= 5) 
       for(var day = 1; day <= lastDay; day++) {
         var dayOfWeek = new Date(year, month, day).getDay();
         if (dayOfWeek == ProtoCalendar.SUNDAY && holidays[day]) {
           var next = day + 1;
           if (curRule) {
             for (; holidays[next]; next += 1) { }
           } else if (newRule) {
             if (holidays[next]) continue;
           }
           holidays[next] = '振替休日';
         } else if (holidays[day - 1] && holidays[day + 1] && !holidays[day]) {
           holidays[day] = '休日';
         }
       }
       calendar.holidays = holidays;
     }
   };

 })();
