/**
 * @file
 * Contains the definition of the behaviour jsTestBlackWeight.
 */

(function ($, Drupal, drupalSettings) {

  'use strict';

  Drupal.behaviors.scheduler = {
    attach: function (context, settings) {

      /*
      $('#calendar').fullCalendar({
        schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
        defaultView: 'agendaDay',
        defaultDate: '2018-02-07',
        editable: true,
        selectable: true,
        eventLimit: true, // allow "more" link when too many events
        header: {
          left: 'prev,next today',
          center: 'title',
          right: 'agendaDay,agendaTwoDay,agendaWeek,month'
        },
        views: {
          agendaTwoDay: {
            type: 'agenda',
            duration: { days: 2 },

            // views that are more than a day will NOT do this behavior by default
            // so, we need to explicitly enable it
            groupByResource: true

            //// uncomment this line to group by day FIRST with resources underneath
            //groupByDateAndResource: true
          }
        },

        //// uncomment this line to hide the all-day slot
        //allDaySlot: false,

        resources: [
          { id: 'a', title: 'Room A' },
          { id: 'b', title: 'Room B', eventColor: 'green' },
          { id: 'c', title: 'Room C', eventColor: 'orange' },
          { id: 'd', title: 'Room D', eventColor: 'red' }
        ],
        events: [
          { id: '1', resourceId: 'a', start: '2018-02-06', end: '2018-02-08', title: 'event 1' },
          { id: '2', resourceId: 'a', start: '2018-02-07T09:00:00', end: '2018-02-07T14:00:00', title: 'event 2' },
          { id: '3', resourceId: 'b', start: '2018-02-07T12:00:00', end: '2018-02-08T06:00:00', title: 'event 3' },
          { id: '4', resourceId: 'c', start: '2018-02-07T07:30:00', end: '2018-02-07T09:30:00', title: 'event 4' },
          { id: '5', resourceId: 'd', start: '2018-02-07T10:00:00', end: '2018-02-07T15:00:00', title: 'event 5' }
        ],

        select: function(start, end, jsEvent, view, resource) {
          console.log(
              'select',
              start.format(),
              end.format(),
              resource ? resource.id : '(no resource)'
          );
        },
        dayClick: function(date, jsEvent, view, resource) {
          console.log(
              'dayClick',
              date.format(),
              resource ? resource.id : '(no resource)'
          );
        }
      });
      */

      /* initialize the external events
-----------------------------------------------------------------*/

      $('#external-events .fc-event').each(function() {

        // store data so the calendar knows to render an event upon drop
        $(this).data('event', {
          title: $.trim($(this).text()), // use the element's text as the event title
          stick: true // maintain when user navigates (see docs on the renderEvent method)
        });

        // make the event draggable using jQuery UI
        $(this).draggable({
          zIndex: 999,
          revert: true,      // will cause the event to go back to its
          revertDuration: 0  //  original position after the drag
        });

      });


      /* initialize the calendar
      -----------------------------------------------------------------*/

      $('#calendar').fullCalendar({
        now: '2018-02-07',
        editable: false, // enable draggable events
        droppable: true, // this allows things to be dropped onto the calendar
        eventOverlap: false,
        aspectRatio: 1.8,
        scrollTime: '00:00', // undo default 6am scrollTime
        slotDuration: '00:15:00',
        header: {
          left: 'today prev,next',
          center: 'title'
        },
        defaultView: 'agendaDay',
        allDaySlot: false,
        groupByResource: true,
        resources: settings.camp_schedule.venues,
        events: settings.camp_schedule.events,
        drop: function(date, jsEvent, ui, resourceId) {
          console.log('drop', date.format(), resourceId);
          $(this).remove();
        },
        eventReceive: function(event) { // called when a proper external event is dropped
          console.log('eventReceive', event);
        },
        eventDrop: function(event) { // called when an event (already on the calendar) is moved
          console.log('eventDrop', event);
        }
      });
    }
  };
})(jQuery, Drupal, drupalSettings);